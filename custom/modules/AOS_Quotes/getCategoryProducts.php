<?php
ini_set('display_errors','1');
error_reporting(1);
    if (!defined('sugarEntry') || !sugarEntry)
        die('Not A Valid Entry Point');
    $categoryId = $_GET['category_id'];
    global $app_list_strings;
    $db = DBManagerFactory::getInstance();
    
    $productCategories = BeanFactory::getBean('AOS_Product_Categories', $categoryId);
    $arrProductList = array();
    if ($productCategories->load_relationship('aos_products')) {
        //Fetch related beans
        $relatedBeans = $productCategories->aos_products->getBeans();
        $field_list = array("name", "id", "part_number", "cost", "price", "description", "currency_id", "charge_model_id_c", "charge_type_id_c", "event_id_c", "uom_c");
        
        $priceFieldList = array("id","sb_pricebook_type", "amount", "amount_usdollar", "currency_id", "version_optimized_lock", "price_format", "tier_price", "starting_unit", "ending_unit", "start_date", "end_date");
        if(!empty($relatedBeans)) {
            $counter = 0;
            foreach($relatedBeans as $row) {
                $arrPriceList = array();
                $sqlCharge = "SELECT a.id, a.name, a.sb_pricebook_type, a.amount, a.amount_usdollar, a.currency_id, a.version_optimized_lock, a.price_format, a.tier_price, a.starting_unit, a.ending_unit FROM sb_pricebook a INNER JOIN sb_pricebook_cstm b ON a.id = b.id_c WHERE b.aos_products_id_c = '".$row->id."'";
                $result = $db->query($sqlCharge);
                
                while ($rowCharge = $db->fetchByAssoc($result)) {
                    foreach($priceFieldList as $priceFieldName){
                        $arrPriceList[$priceFieldName] = $rowCharge[$priceFieldName];
                    }
                    $arrProductList[$counter]['pricebook'][] = $arrPriceList;
                }
                
                foreach($field_list as $fieldName){
                    
                    switch($fieldName){
                      case 'charge_model_id_c': 
                          $fieldDropDownValue = $app_list_strings['chargemodel_list'][$row->$fieldName];
                          break;
                      case 'charge_type_id_c':
                          
                          $fieldDropDownValue = $app_list_strings['chargetype_list'][$row->$fieldName];
                          break;
                      case 'price_format':
                          $fieldDropDownValue = $app_list_strings['price_format_list'][$row->$fieldName];
                          break;
                      default:
                          $fieldDropDownValue = '';
                    }
                    
                    if(!empty($fieldDropDownValue)){
                        $arrProductList[$counter][$fieldName.'_picklist'] = $fieldDropDownValue;
                    }
                    
                    $arrProductList[$counter][$fieldName] = $row->$fieldName;
                }
                $counter++;
            }
        }
        
        echo json_encode($arrProductList);
        exit();

    }
    
	