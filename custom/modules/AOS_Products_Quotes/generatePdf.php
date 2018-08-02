<?php
/**
 *
 * SugarCRM Community Edition is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004-2013 SugarCRM Inc.
 *
 * SuiteCRM is an extension to SugarCRM Community Edition developed by SalesAgility Ltd.
 * Copyright (C) 2011 - 2017 SalesAgility Ltd.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU Affero General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU Affero General Public License for more
 * details.
 *
 * You should have received a copy of the GNU Affero General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 *
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU Affero General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU Affero General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo and "Supercharged by SuiteCRM" logo. If the display of the logos is not
 * reasonably feasible for technical reasons, the Appropriate Legal Notices must
 * display the words "Powered by SugarCRM" and "Supercharged by SuiteCRM".
 */


if (!isset($_REQUEST['uid']) || empty($_REQUEST['uid']) || !isset($_REQUEST['templateID']) || empty($_REQUEST['templateID'])) {
    die('Error retrieving record. This record may be deleted or you may not be authorized to view it.');
}
$state = new \SuiteCRM\StateSaver();
$state->pushErrorLevel();
error_reporting(1);
ini_set('display_errors',1);
require_once('modules/AOS_PDF_Templates/PDF_Lib/mpdf.php');
require_once('modules/AOS_PDF_Templates/templateParser.php');
require_once('modules/AOS_PDF_Templates/sendEmail.php');
require_once('modules/AOS_PDF_Templates/AOS_PDF_Templates.php');
$state->popErrorLevel();

global $mod_strings, $sugar_config,$app_list_strings;

$bean = BeanFactory::getBean($_REQUEST['module'], $_REQUEST['uid']);

if(!$bean){
    sugar_die("Invalid Record");
}

$task = $_REQUEST['task'];
$variableName = strtolower($bean->module_dir);
$lineItemsGroups = array();
$lineItems = array();

$sql = "SELECT pg.id, pg.product_id, pg.group_id FROM aos_products_quotes pg LEFT JOIN aos_line_item_groups lig ON pg.group_id = lig.id WHERE pg.parent_type = '" . $bean->object_name . "' AND pg.parent_id = '" . $bean->id . "' AND pg.deleted = 0 ORDER BY lig.number ASC, pg.number ASC";
$res = $bean->db->query($sql);
while ($row = $bean->db->fetchByAssoc($res)) {
    $lineItemsGroups[$row['group_id']][$row['id']] = $row['product_id'];
    $lineItems[$row['id']] = $row['product_id'];

}


$template = new AOS_PDF_Templates();
$template->retrieve($_REQUEST['templateID']);

$object_arr = array();
$object_arr[$bean->module_dir] = $bean->id;

//backward compatibility
$object_arr['Accounts'] = $bean->billing_account_id;
$object_arr['Contacts'] = $bean->billing_contact_id;
$object_arr['Users'] = $bean->assigned_user_id;
$object_arr['Currencies'] = $bean->currency_id;

$search = array('/<script[^>]*?>.*?<\/script>/si',      // Strip out javascript
    '/<[\/\!]*?[^<>]*?>/si',        // Strip out HTML tags
    '/([\r\n])[\s]+/',          // Strip out white space
    '/&(quot|#34);/i',          // Replace HTML entities
    '/&(amp|#38);/i',
    '/&(lt|#60);/i',
    '/&(gt|#62);/i',
    '/&(nbsp|#160);/i',
    '/&(iexcl|#161);/i',
    '/<address[^>]*?>/si',
    '/&(apos|#0*39);/',
    '/&#(\d+);/'
);

$replace = array('',
    '',
    '\1',
    '"',
    '&',
    '<',
    '>',
    ' ',
    chr(161),
    '<br>',
    "'",
    'chr(%1)'
);

$header = preg_replace($search, $replace, $template->pdfheader);
$footer = preg_replace($search, $replace, $template->pdffooter);
$text = preg_replace($search, $replace, $template->description);
$text = str_replace("<p><pagebreak /></p>", "<pagebreak />", $text);
$text = preg_replace_callback('/\{DATE\s+(.*?)\}/',
    function ($matches) {
        return date($matches[1]);
    },
    $text);
$text = str_replace("\$aos_quotes", "\$" . $variableName, $text);
$text = str_replace("\$aos_invoices", "\$" . $variableName, $text);
$text = str_replace("\$total_amt", "\$" . $variableName . "_total_amt", $text);
$text = str_replace("\$discount_amount", "\$" . $variableName . "_discount_amount", $text);
$text = str_replace("\$subtotal_amount", "\$" . $variableName . "_subtotal_amount", $text);
$text = str_replace("\$tax_amount", "\$" . $variableName . "_tax_amount", $text);
$text = str_replace("\$shipping_amount", "\$" . $variableName . "_shipping_amount", $text);
$text = str_replace("\$total_amount", "\$" . $variableName . "_total_amount", $text);

$text = populate_group_lines($text, $lineItemsGroups, $lineItems);

$productDetails = getProductLineItemDetails($lineItemsGroups, $lineItems);
$text = str_replace("\$PRODCUT_DETAILS", $productDetails, $text);

$converted = templateParser::parse_template($text, $object_arr);
$header = templateParser::parse_template($header, $object_arr);
$footer = templateParser::parse_template($footer, $object_arr);

$printable = str_replace("\n", "<br />", $converted);

echo $text;
exit;

if ($task == 'pdf' || $task == 'emailpdf') {
    $file_name = $mod_strings['LBL_PDF_NAME'] . "_" . str_replace(" ", "_", $bean->name) . ".pdf";

    ob_clean();
    try {
        $orientation = ($template->orientation == "Landscape") ? "-L" : "";
        $pdf = new mPDF('en', $template->page_size . $orientation, '', 'DejaVuSansCondensed', $template->margin_left, $template->margin_right, $template->margin_top, $template->margin_bottom, $template->margin_header, $template->margin_footer);
        $pdf->SetAutoFont();
        $pdf->SetHTMLHeader($header);
        $pdf->SetHTMLFooter($footer);
        $pdf->WriteHTML($printable);
        if ($task == 'pdf') {
            $pdf->Output($file_name, "D");
        } else {
            $fp = fopen($sugar_config['upload_dir'] . 'attachfile.pdf', 'wb');
            fclose($fp);
            $pdf->Output($sugar_config['upload_dir'] . 'attachfile.pdf', 'F');
            $sendEmail = new sendEmail();
            $sendEmail->send_email($bean, $bean->module_dir, '', $file_name, true);
        }
    } catch (mPDF_exception $e) {
        echo $e;
    }
} elseif ($task == 'email') {
    $sendEmail = new sendEmail();
    $sendEmail->send_email($bean, $bean->module_dir, $printable, '', false);
}


function populate_group_lines($text, $lineItemsGroups, $lineItems, $element = 'table')
{
    $firstValue = '';
    $firstNum = 0;

    $lastValue = '';
    $lastNum = 0;

    $startElement = '<' . $element;
    $endElement = '</' . $element . '>';

    $groups = new AOS_Line_Item_Groups();
    foreach ($groups->field_defs as $name => $arr) {
        if (!((isset($arr['dbType']) && strtolower($arr['dbType']) == 'id') || $arr['type'] == 'id' || $arr['type'] == 'link')) {

            $curNum = strpos($text, '$aos_line_item_groups_' . $name);
            if ($curNum) {
                if ($curNum < $firstNum || $firstNum == 0) {
                    $firstValue = '$aos_line_item_groups_' . $name;
                    $firstNum = $curNum;
                }
                if ($curNum > $lastNum) {
                    $lastValue = '$aos_line_item_groups_' . $name;
                    $lastNum = $curNum;
                }
            }
        }
    }
    if ($firstValue !== '' && $lastValue !== '') {
        //Converting Text
        $parts = explode($firstValue, $text);
        $text = $parts[0];
        $parts = explode($lastValue, $parts[1]);
        if ($lastValue == $firstValue) {
            $groupPart = $firstValue . $parts[0];
        } else {
            $groupPart = $firstValue . $parts[0] . $lastValue;
        }

        if (count($lineItemsGroups) != 0) {
            //Read line start <tr> value
            $tcount = strrpos($text, $startElement);
            $lsValue = substr($text, $tcount);
            $tcount = strpos($lsValue, ">") + 1;
            $lsValue = substr($lsValue, 0, $tcount);


            //Read line end values
            $tcount = strpos($parts[1], $endElement) + strlen($endElement);
            $leValue = substr($parts[1], 0, $tcount);

            //Converting Line Items
            $obb = array();

            $tdTemp = explode($lsValue, $text);

            $groupPart = $lsValue . $tdTemp[count($tdTemp) - 1] . $groupPart . $leValue;

            $text = $tdTemp[0];

            foreach ($lineItemsGroups as $group_id => $lineItemsArray) {
                $groupPartTemp = populate_product_lines($groupPart, $lineItemsArray);
                $groupPartTemp = populate_service_lines($groupPartTemp, $lineItemsArray);

                $obb['AOS_Line_Item_Groups'] = $group_id;
                $text .= templateParser::parse_template($groupPartTemp, $obb);
                $text .= '<br />';
            }
            $tcount = strpos($parts[1], $endElement) + strlen($endElement);
            $parts[1] = substr($parts[1], $tcount);
        } else {
            $tcount = strrpos($text, $startElement);
            $text = substr($text, 0, $tcount);

            $tcount = strpos($parts[1], $endElement) + strlen($endElement);
            $parts[1] = substr($parts[1], $tcount);
        }

        $text .= $parts[1];
    } else {
        $text = populate_product_lines($text, $lineItems);
        $text = populate_service_lines($text, $lineItems);
    }

    $text .= 'Im here in quote pdf';
    return $text;

}

function populate_product_lines($text, $lineItems, $element = 'tr')
{
    $firstValue = '';
    $firstNum = 0;

    $lastValue = '';
    $lastNum = 0;

    $startElement = '<' . $element;
    $endElement = '</' . $element . '>';

    //Find first and last valid line values
    $product_quote = new AOS_Products_Quotes();
    foreach ($product_quote->field_defs as $name => $arr) {
        if (!((isset($arr['dbType']) && strtolower($arr['dbType']) == 'id') || $arr['type'] == 'id' || $arr['type'] == 'link')) {

            $curNum = strpos($text, '$aos_products_quotes_' . $name);

            if ($curNum) {
                if ($curNum < $firstNum || $firstNum == 0) {
                    $firstValue = '$aos_products_quotes_' . $name;
                    $firstNum = $curNum;

                }
                if ($curNum > $lastNum) {
                    $lastValue = '$aos_products_quotes_' . $name;
                    $lastNum = $curNum;

                }
            }
        }
    }

    $product = new AOS_Products();
    foreach ($product->field_defs as $name => $arr) {
        if (!((isset($arr['dbType']) && strtolower($arr['dbType']) == 'id') || $arr['type'] == 'id' || $arr['type'] == 'link')) {

            $curNum = strpos($text, '$aos_products_' . $name);
            if ($curNum) {
                if ($curNum < $firstNum || $firstNum == 0) {
                    $firstValue = '$aos_products_' . $name;


                    $firstNum = $curNum;
                }
                if ($curNum > $lastNum) {
                    $lastValue = '$aos_products_' . $name;
                    $lastNum = $curNum;
                }
            }
        }
    }

    if ($firstValue !== '' && $lastValue !== '') {

        //Converting Text
        $tparts = explode($firstValue, $text);
        $temp = $tparts[0];

        //check if there is only one line item
        if ($firstNum == $lastNum) {
            $linePart = $firstValue;
        } else {
            $tparts = explode($lastValue, $tparts[1]);
            $linePart = $firstValue . $tparts[0] . $lastValue;
        }


        $tcount = strrpos($temp, $startElement);
        $lsValue = substr($temp, $tcount);
        $tcount = strpos($lsValue, ">") + 1;
        $lsValue = substr($lsValue, 0, $tcount);

        //Read line end values
        $tcount = strpos($tparts[1], $endElement) + strlen($endElement);
        $leValue = substr($tparts[1], 0, $tcount);
        $tdTemp = explode($lsValue, $temp);

        $linePart = $lsValue . $tdTemp[count($tdTemp) - 1] . $linePart . $leValue;
        $parts = explode($linePart, $text);
        $text = $parts[0];

        //Converting Line Items
        if (count($lineItems) != 0) {
            foreach ($lineItems as $id => $productId) {
                if ($productId != null && $productId != '0') {
                    $obb['AOS_Products_Quotes'] = $id;
                    $obb['AOS_Products'] = $productId;
                    $text .= templateParser::parse_template($linePart, $obb);
                }
            }
        }

for ($i = 1; $i < count($parts); $i++) {        $text .= $parts[$i];
	}
    }
    return $text;
}

function populate_service_lines($text, $lineItems, $element = 'tr')
{
    $firstValue = '';
    $firstNum = 0;

    $lastValue = '';
    $lastNum = 0;

    $startElement = '<' . $element;
    $endElement = '</' . $element . '>';

    $text = str_replace("\$aos_services_quotes_service", "\$aos_services_quotes_product", $text);

    //Find first and last valid line values
    $product_quote = new AOS_Products_Quotes();
    foreach ($product_quote->field_defs as $name => $arr) {
        if (!((isset($arr['dbType']) && strtolower($arr['dbType']) == 'id') || $arr['type'] == 'id' || $arr['type'] == 'link')) {

            $curNum = strpos($text, '$aos_services_quotes_' . $name);
            if ($curNum) {
                if ($curNum < $firstNum || $firstNum == 0) {
                    $firstValue = '$aos_products_quotes_' . $name;
                    $firstNum = $curNum;
                }
                if ($curNum > $lastNum) {
                    $lastValue = '$aos_products_quotes_' . $name;
                    $lastNum = $curNum;
                }
            }
        }
    }
    if ($firstValue !== '' && $lastValue !== '') {
        $text = str_replace("\$aos_products", "\$aos_null", $text);
        $text = str_replace("\$aos_services", "\$aos_products", $text);

        //Converting Text
        $tparts = explode($firstValue, $text);
        $temp = $tparts[0];

        //check if there is only one line item
        if ($firstNum == $lastNum) {
            $linePart = $firstValue;
        } else {
            $tparts = explode($lastValue, $tparts[1]);
            $linePart = $firstValue . $tparts[0] . $lastValue;
        }

        $tcount = strrpos($temp, $startElement);
        $lsValue = substr($temp, $tcount);
        $tcount = strpos($lsValue, ">") + 1;
        $lsValue = substr($lsValue, 0, $tcount);

        //Read line end values
        $tcount = strpos($tparts[1], $endElement) + strlen($endElement);
        $leValue = substr($tparts[1], 0, $tcount);
        $tdTemp = explode($lsValue, $temp);

        $linePart = $lsValue . $tdTemp[count($tdTemp) - 1] . $linePart . $leValue;
        $parts = explode($linePart, $text);
        $text = $parts[0];

        //Converting Line Items
        if (count($lineItems) != 0) {
            foreach ($lineItems as $id => $productId) {
                if ($productId == null || $productId == '0') {
                    $obb['AOS_Products_Quotes'] = $id;
                    $text .= templateParser::parse_template($linePart, $obb);
                }
            }
        }

for ($i = 1; $i < count($parts); $i++) {        $text .= $parts[$i];
	}
    }
    return $text;
}

function getProductLineItemDetails($lineItemsGroups, $lineItems){
    global $mod_strings;
    $html = '';
    if(count($lineItemsGroups) > 0){
        $html .= "<table style='border:1px solid black;border-collapse: collapse;' width='860px;' cellpadding='0' cellspacing='0'>";

        $i = 0;
        $productCount = 0;
        $serviceCount = 0;
        $group_id = '';
        $groupStart = '';
        $groupEnd = '';
        $product = '';
        $service = '';
    
        foreach ($lineItemsGroups as $group_id => $lineItemsArray) {
                $group_item = new AOS_Line_Item_Groups();
                $group_item->retrieve($group_id);

                $html .= "<tr>";
                $html .= "<td colspan='9' class='tabDetailViewDL' style='text-align: left;padding:2px;' scope='row'><b>".$mod_strings['LBL_CATEGORY_NAME'].": ".$group_item->name."</b></td>";
                $html .= "</tr>";
                
                $productLineItems = getProductLineItems($lineItemsArray);
                $html .= $productLineItems;
               
            }
    }
   
    $html .= $groupStart.$product.$service.$groupEnd;
    $html .= "</table>";
    return $html;
}

function getProductLineItems($lineItemsArray){
    global $mod_strings;
    $productCount = 1;
    $product = '';
    foreach($lineItemsArray as $productKey=>$productId ){
        $line_item = new AOS_Products_Quotes();
        $line_item->retrieve($productKey);
        
        if($line_item->product_id != '0' && $line_item->product_id != null){
            $product .= "<tr>";
            $product_note = wordwrap($line_item->description,40,"<br />\n");
            $product .= "<td colspan ='9' class='tabDetailViewDF' style='text-align: left; padding:5px;'><b>".$productCount.")</b> ";
            $product .= "<b>".$mod_strings['LBL_PRODUCT_NAME'].': '.$line_item->name."</b><br />".$product_note."\n</br>";
            $product .= "<b>".$mod_strings['LBL_CHARGE_MODEL'].': </b>'.$app_list_strings['chargemodel_list'][(int)$line_item->charge_model_id_c]."\n</br>";
            $product .= "<b>".$mod_strings['LBL_CHARGE_TYPE'].': </b>'.$app_list_strings['charge_type_id_list'][(int)$line_item->charge_type_id_c]."\n</br>";
            $product .= "<b>".$mod_strings['LBL_UOM'].': </b>'.$line_item->uom_c."</td>";
            $product .= "</tr>";

            $priceTable = $line_item->charge_price_table_c;
            $priceTable = html_entity_decode($priceTable);
            $chargePriceLineItem = '';
            $arrPriceTable = json_decode($priceTable);

            if(!empty($arrPriceTable)) {

                $chargePriceLineItem .= insertChargePriceLineHeader();
                $count = 1;
                foreach ($arrPriceTable as $key=>$arrChargeTable) {
                    $chargePriceLineItem .= insertChargePriceLineDetail($arrChargeTable, $count, $line_item->product_id);
                    $count = $count+1;
                }
                $chargePriceLineItem .= '</table>';
            }
            if($chargePriceLineItem != '') {
                $product .= "<tr>";
                $product .= "<td colspan='9' style='text-align: center; padding:5px;'><div style='width:80%;margin:0px auto;'>".$chargePriceLineItem."</div></td>";
                $product .= "</tr>";
            }
        }
        $productCount = $productCount+1;
    }
    return $product;
}

function insertChargePriceLineHeader() {
    global $mod_strings;
    $chargePriceLineItem = '<table style="width:100%;border:1px solid black;border-collapse: collapse;padding:5px;"><tr>';
    $chargePriceLineItem .= '<th style="width:20%;text-align:left;border:1px solid black;padding:5px;"><b>'.$mod_strings['LBL_SR_NO'].'</b></th>';
    $chargePriceLineItem .= '<th style="width:20%;text-align:center;border:1px solid black;padding:5px;"><b>'.$mod_strings['LBL_FROM'].'</b></th>';
    $chargePriceLineItem .= '<th style="width:20%;text-align:center;border:1px solid black;padding:5px;"><b>'.$mod_strings['LBL_TO'].'</b></th>';
    $chargePriceLineItem .= '<th style="width:20%;text-align:center;border:1px solid black;padding:5px;"><b>'.$mod_strings['LBL_LIST_PRICE'].'</b></th>';
    $chargePriceLineItem .= '<th style="width:20%;text-align:center;border:1px solid black;padding:5px;"><b>'.$mod_strings['LBL_PRICE_FORMAT'].'</b></th></tr>';
        
    return $chargePriceLineItem;
}

function insertChargePriceLineDetail($chargePrice, $count, $productId) {
    $params = array('currency_id' => $focus->currency_id);
    $chargePriceLineItem = '<tr>';
    $chargePriceLineItem .= '<td style="width:20%;text-align:left;padding:5px;"><span class="tier_value">'.$count.'</span></td>';
    
    $chargePriceLineItem .= '<td style="width:20%;text-align:right;padding:5px;">'.format_number($chargePrice->starting_unit, 0, 0).'&nbsp;</td>';
    $chargePriceLineItem .= '<td style="width:20%;text-align:right;padding:5px;">'.format_number($chargePrice->end_unit, 0, 0).'&nbsp;</td>';
    $chargePriceLineItem .= '<td style="width:20%;text-align:center;padding:5px;"><span class="listprice">'.currency_format_number($chargePrice->list_price, $params).'</span></td>';
    $chargePriceLineItem .= '<td style="width:20%;text-align:left;padding:5px;">';
    
    if($chargePrice->price_format == 'FlatFee'){
        $chargePriceLineItem .= 'Flat Fee';
    } else if($chargePrice->price_format == 'PerUnit'){
        $chargePriceLineItem .= 'Per Unit';
    }
    $chargePriceLineItem .= '</td>';
    $chargePriceLineItem .= '</tr>';
    
    return $chargePriceLineItem;
}