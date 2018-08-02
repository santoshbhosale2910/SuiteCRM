<?php
/**
 * Advanced OpenSales, Advanced, robust set of sales modules.
 * @package Advanced OpenSales for SugarCRM
 * @copyright SalesAgility Ltd http://www.salesagility.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with this program; if not, see http://www.gnu.org/licenses
 * or write to the Free Software Foundation,Inc., 51 Franklin Street,
 * Fifth Floor, Boston, MA 02110-1301  USA
 *
 * @author SalesAgility <info@salesagility.com>
 */

function display_lines($focus, $field, $value, $view){

    global $sugar_config, $locale, $app_list_strings, $mod_strings;

    $enable_groups = (int)$sugar_config['aos']['lineItems']['enableGroups'];
    $total_tax = (int)$sugar_config['aos']['lineItems']['totalTax'];

    $html = '';

    if($view == 'EditView'){

        $html .= '<script src="modules/AOS_Products_Quotes/line_items.js"></script>';
        if(file_exists('custom/modules/AOS_Products_Quotes/line_items.js')){
            $html .= '<script src="custom/modules/AOS_Products_Quotes/line_items.js"></script>';
        }
        $html .= '<script language="javascript">var sig_digits = '.$locale->getPrecision().';';
        $html .= 'var module_sugar_grp1 = "'.$focus->module_dir.'";';
        $html .= 'var enable_groups = '.$enable_groups.';';
        $html .= 'var total_tax = '.$total_tax.';';
        $html .= '</script>';

        $html .= "<table border='0' cellspacing='4' id='lineItems'></table>";

        if($enable_groups){
            $html .= "<div style='padding-top: 10px; padding-bottom:10px;'>";
            $html .= "<input type=\"button\" tabindex=\"117\" class=\"button\" value=\"".$mod_strings['LBL_ADD_PRD_CATEGORY']."\" id=\"addCategory\" onclick=\"insertGroup(0)\" />";
            $html .= "</div>";
        }
                
        $html .= '<input type="hidden" name="vathidden" id="vathidden" value="'.get_select_options_with_id($app_list_strings['vat_list'], '').'">
				  <input type="hidden" name="discounthidden" id="discounthidden" value="'.get_select_options_with_id($app_list_strings['discount_list'], '').'">';
        if($focus->id != '') {
            require_once('modules/AOS_Products_Quotes/AOS_Products_Quotes.php');
            require_once('modules/AOS_Line_Item_Groups/AOS_Line_Item_Groups.php');

            $sql = "SELECT pg.id, pg.group_id,cstpg.aos_product_categories_id_c AS category_id FROM aos_products_quotes pg LEFT JOIN aos_line_item_groups lig ON pg.group_id = lig.id LEFT JOIN aos_products_quotes_cstm cstpg ON pg.id = cstpg.id_c WHERE pg.parent_type = '" . $focus->object_name . "' AND pg.parent_id = '" . $focus->id . "' AND pg.deleted = 0 ORDER BY lig.number ASC, pg.number ASC";
            
            $result = $focus->db->query($sql);
            $html .= "<script>
                if(typeof sqs_objects == 'undefined'){var sqs_objects = new Array;}
                </script>";
            
            while ($row = $focus->db->fetchByAssoc($result)) {
                $line_item = new AOS_Products_Quotes();
                $line_item->retrieve($row['id'], false);
                $arrLineItems = $line_item->toArray();
                
                foreach($arrLineItems as $fieldName=>$fieldValue){
                    switch($fieldName){
                      case 'charge_model_id_c':
                          $fieldValue = (Int)$fieldValue;
                          $fieldDropDownValue = $app_list_strings['chargemodel_list'][$fieldValue];
                          break;
                      case 'charge_type_id_c':
                          $fieldValue = (Int)$fieldValue;
                          $fieldDropDownValue = $app_list_strings['charge_type_id_list'][$fieldValue];
                          break;
                      case 'price_format':
                          echo $fieldValue;
                          $fieldDropDownValue = $app_list_strings['price_format_list'][$fieldValue];
                          break;
                      default:
                          $fieldDropDownValue = '';
                    }
                    
                    if(!empty($fieldDropDownValue)){
                        $arrLineItems[$fieldName.'_picklist'] = $fieldDropDownValue;
                    }
                }
                
                $line_item = json_encode($arrLineItems);

                $group_item = 'null';
                if ($row['group_id'] != null) {
                    $group_item = new AOS_Line_Item_Groups();
                    $group_item->retrieve($row['group_id'], false);
                    $arrGroup = $group_item->toArray();
                    $arrGroup['category_id'] = $row['category_id'];
                    $group_item = json_encode($arrGroup);
                }
                
                $html .= "<script>
                        insertLineItems(" . $line_item . "," . $group_item . ");
                    </script>";

            }
        }
        if(!$enable_groups){
            $html .= '<script>insertGroup();</script>';
        }

    } else if($view == 'DetailView'){
        $params = array('currency_id' => $focus->currency_id);

        $sql = "SELECT pg.id, pg.group_id FROM aos_products_quotes pg LEFT JOIN aos_line_item_groups lig ON pg.group_id = lig.id WHERE pg.parent_type = '".$focus->object_name."' AND pg.parent_id = '".$focus->id."' AND pg.deleted = 0 ORDER BY lig.number ASC, pg.number ASC";
        
        $result = $focus->db->query($sql);
        $sep = get_number_seperators();

        $html .= "<table border='0' width='100%' cellpadding='0' cellspacing='0' style='line-height: 20px;'>";

        $i = 0;
        $productCount = 0;
        $serviceCount = 0;
        $group_id = '';
        $groupStart = '';
        $groupEnd = '';
        $product = '';
        $service = '';

        while ($row = $focus->db->fetchByAssoc($result)) {
            $line_item = new AOS_Products_Quotes();
            $line_item->retrieve($row['id']);


            if($enable_groups && ($group_id != $row['group_id'] || $i == 0)){
                $html .= $groupStart.$product.$service.$groupEnd;
                if($i != 0)$html .= "<tr><td colspan='9' nowrap='nowrap'><br></td></tr>";
                $groupStart = '';
                $groupEnd = '';
                $product = '';
                $service = '';
                $i = 1;
                $productCount = 0;
                $serviceCount = 0;
                $group_id = $row['group_id'];

                $group_item = new AOS_Line_Item_Groups();
                $group_item->retrieve($row['group_id']);
                
                $groupStart .= "<tr>";
                $groupStart .= "<td colspan='9' class='tabDetailViewDL' style='text-align: left;padding: 5px;background: #badcfa;' scope='row'><b>".$mod_strings['LBL_GROUP_NAME'].": ".$group_item->name."</b></td>";
                $groupStart .= "</tr>";
            }
            if($line_item->product_id != '0' && $line_item->product_id != null){
                
                $product .= "<tr>";
                $product_note = wordwrap($line_item->description,40,"<br />\n");
                $product .= "<td colspan ='9' class='tabDetailViewDF' style='text-align: left; padding:5px;'><b>".++$productCount.")</b> ";
                $product .= "<b>".$mod_strings['LBL_PRODUCT_NAME'].': '.$line_item->name."</b><br />";
                $product .= "<b>".$mod_strings['LBL_CHARGE_MODEL'].': </b>'.$app_list_strings['chargemodel_list'][(int)$line_item->charge_model_id_c]."</br>";
                $product .= "<b>".$mod_strings['LBL_CHARGE_TYPE'].': </b>'.$app_list_strings['charge_type_id_list'][(int)$line_item->charge_type_id_c]."</br>";
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
                        $chargePriceLineItem .= insertChargePriceLineDetail($arrChargeTable, $count, $line_item->product_id, $params);
                        $count = $count+1;
                    }
                     $chargePriceLineItem .= '</table>';
                }
                if($chargePriceLineItem != '') {
                    $product .= "<tr>";
                    $product .= "<td colspan='9' style='text-align: center; padding:5px;'><div style='width:80%;margin:0px auto;'>".$chargePriceLineItem."</div></td>";
                    $product .= "</tr>";
                }
  
            } else {
                if($serviceCount == 0)
                {
                    $service .= "<tr>";
                    $service .= "<td width='5%' class='tabDetailViewDL' style='text-align: left;padding:5px;' scope='row'>&nbsp;</td>";
                    $service .= "<td width='46%' class='dataLabel' style='text-align: left;padding:5px;' colspan='2' scope='row'>".$mod_strings['LBL_SERVICE_NAME']."</td>";
                    $service .= "<td width='12%' class='dataLabel' style='text-align: right;padding:5px;' scope='row'>".$mod_strings['LBL_SERVICE_LIST_PRICE']."</td>";
                    $service .= "<td width='12%' class='dataLabel' style='text-align: right;padding:5px;' scope='row'>".$mod_strings['LBL_SERVICE_PRICE']."</td>";
                    $service .= "<td width='12%' class='dataLabel' style='text-align: right;padding:5px;' scope='row'>".$mod_strings['LBL_TOTAL_PRICE']."</td>";
                    $service .= "</tr>";
                }

                $service .= "<tr>";
                $service .= "<td class='tabDetailViewDF' style='text-align: left; padding:5px;'>".++$serviceCount."</td>";
                $service .= "<td class='tabDetailViewDF' style='padding:5px;' colspan='2'>".$line_item->name."</td>";
                $service .= "<td class='tabDetailViewDF' style='text-align: right; padding:5px;'>".currency_format_number($line_item->product_list_price,$params)."</td>";

                $service .= "<td class='tabDetailViewDF' style='text-align: right; padding:5px;'>".currency_format_number($line_item->product_unit_price,$params)."</td>";
                $service .= "<td class='tabDetailViewDF' style='text-align: right; padding:5px;'>".currency_format_number($line_item->product_total_price,$params )."</td>";
                $service .= "</tr>";

            }
        }
        $html .= $groupStart.$product.$service.$groupEnd;
        $html .= "</table>";
    }
    return $html;
}

//Bug #598
//The original approach to trimming the characters was rtrim(rtrim(format_number($line_item->product_qty), '0'),$sep[1])
//This however had the unwanted side-effect of turning 1000 (or 10 or 100) into 1 when the Currency Significant Digits
//field was 0.
//The approach below will strip off the fractional part if it is only zeroes (and in this case the decimal separator
//will also be stripped off) The custom decimal separator is passed in to the function from the locale settings
function stripDecimalPointsAndTrailingZeroes($inputString,$decimalSeparator)
{
    return preg_replace('/'.preg_quote($decimalSeparator).'[0]+$/','',$inputString);
}

function get_discount_string($type, $amount, $params, $locale, $sep){
    if($amount != '' && $amount != '0.00')
    {
        if($type == 'Amount')
        {
            return currency_format_number($amount,$params )."</td>";
        }
        else if($locale->getPrecision())
        {
            return rtrim(rtrim(format_number($amount), '0'),$sep[1])."%";
        } else{
            return format_number($amount)."%";
        }
    }
    else
    {
        return "-";
    }
}

function display_shipping_vat($focus, $field, $value, $view){

    if($view == 'EditView'){
        global $app_list_strings;

        if($value != '') $value = format_number($value);

        $html = "<input id='shipping_tax_amt' type='text' tabindex='0' title='' value='".$value."' maxlength='26,6' size='22' name='shipping_tax_amt' onblur='calculateTotal(\"lineItems\");'>";
        $html .= "<select name='shipping_tax' id='shipping_tax' onchange='calculateTotal(\"lineItems\");' >".get_select_options_with_id($app_list_strings['vat_list'], (isset($focus->shipping_tax) ? $focus->shipping_tax : ''))."</select>";

        return $html;

    }
    return format_number($value);

}

function insertChargePriceLineHeader() {
    global $mod_strings;
    $chargePriceLineItem = '<table style="width:100%;border:1px solid black;border-collapse: collapse;padding:5px;"><tr>';
    $chargePriceLineItem .= '<th style="width:20%;text-align:left;border:1px solid black;padding:5px;background: #e3e5e7;"><b>'.$mod_strings['LBL_SR_NO'].'</b></th>';
    $chargePriceLineItem .= '<th style="width:20%;text-align:center;border:1px solid black;padding:5px;background: #e3e5e7;"><b>'.$mod_strings['LBL_FROM'].'</b></th>';
    $chargePriceLineItem .= '<th style="width:20%;text-align:center;border:1px solid black;padding:5px;background: #e3e5e7;"><b>'.$mod_strings['LBL_TO'].'</b></th>';
    $chargePriceLineItem .= '<th style="width:20%;text-align:center;border:1px solid black;padding:5px;background: #e3e5e7;"><b>'.$mod_strings['LBL_LIST_PRICE'].'</b></th>';
    $chargePriceLineItem .= '<th style="width:20%;text-align:center;border:1px solid black;padding:5px;background: #e3e5e7;"><b>'.$mod_strings['LBL_PRICE_FORMAT'].'</b></th></tr>';
    
    return $chargePriceLineItem;
}

function insertChargePriceLine($chargePrice, $count, $productId) {
    $chargePriceLineItem = '<div class="pricetable_body" style="width:100%;float:left;overflow:hidden;padding: 5px;">';
    $chargePriceLineItem .= '<div class="priceitem">';
    $chargePriceLineItem .= '<div style="width:20%;float:left;"><input name="product_priceTables_'.$productId.'['.$count.'][id]" value="'.$chargePrice['id'].'" type="hidden"><input name="product_priceTables_'.$productId.'['.$count.'][productid]" value="'.$productId.'" type="hidden">';
    $chargePriceLineItem .= '<span class="tier_value">'.$count.'</span></div>';
    $chargePriceLineItem .= '<div style="width:20%;float:left;"><input class="tier_value" name="product_priceTables_'.$productId.'['.$count.'][tiervalue]" oldvalue="'.$chargePrice['tier_price'].'" value="'.$chargePrice['tier_price'].'" type="hidden">';
    $chargePriceLineItem .= '<input format="true" class="formtext" extname="From" title="Starting Unit" name="product_priceTables_'.$productId.'['.$count.'][starting_unit]" value="'.$chargePrice['starting_unit'].'" style="width: 120px;" type="text"></div>';
    $chargePriceLineItem .= '<div style="width:20%;float:left;"><input format="true" class="formtext" extname="To" title="Ending Unit" name="product_priceTables_'.$productId.'['.$count.'][end_unit]" value="'.$chargePrice['ending_unit'].'" style="width: 120px;" type="text"></div>';
    $chargePriceLineItem .= '<div style="width:20%;float:left;"><span class="listprice">';
    $chargePriceLineItem .= '<input format="true" extname="price" name="product_priceTables_'.$productId.'['.$count.'][list_price]" value="'.$chargePrice['amount'].'" class="formtext" style="width: 120px;" type="text">';
    //$chargePriceLineItem .= '&nbsp;'+chargePrice.currency_id+'&nbsp;';
    $chargePriceLineItem .= '</span></div>';
    $chargePriceLineItem .= '<div style="width:20%;float:left;"><select name="product_priceTables_'.$productId.'['.$count.'][price_format]" class="formselect">';
    $selectedFlatfee = '';
    $selectedPerUnit = '';
    
    if($chargePrice['price_format'] == 'FlatFee'){
        $selectedFlatfee = 'selected="selected"';
    } else if($chargePrice['price_format'] == 'PerUnit'){
        $selectedPerUnit = 'selected="selected"';
    }
    $chargePriceLineItem .= '<option value="FlatFee" '.$selectedFlatfee.'>Flat Fee</option>';
    $chargePriceLineItem .= '<option value="PerUnit" '.$selectedPerUnit.'>Per Unit</option>';
    $chargePriceLineItem .= '</select></div>';
    $chargePriceLineItem .= '</div>';
    $chargePriceLineItem .= '</div>';
    
    return $chargePriceLineItem;
}

function insertChargePriceLineDetail($chargePrice, $count, $productId, $params) {
    
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