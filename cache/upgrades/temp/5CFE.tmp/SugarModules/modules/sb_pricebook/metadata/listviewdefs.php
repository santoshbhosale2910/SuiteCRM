<?php
$module_name = 'sb_pricebook';
$OBJECT_NAME = 'SB_PRICEBOOK';
$listViewDefs [$module_name] = 
array (
  'NAME' => 
  array (
    'width' => '30%',
    'label' => 'LBL_LIST_SALE_NAME',
    'link' => true,
    'default' => true,
  ),
  'SALES_STAGE' => 
  array (
    'width' => '10%',
    'label' => 'LBL_LIST_SALE_STAGE',
    'default' => true,
  ),
  'AMOUNT_USDOLLAR' => 
  array (
    'width' => '10%',
    'label' => 'LBL_LIST_AMOUNT',
    'align' => 'right',
    'default' => true,
    'currency_format' => true,
  ),
  'DATE_CLOSED' => 
  array (
    'width' => '10%',
    'label' => 'LBL_LIST_DATE_CLOSED',
    'default' => true,
  ),
  'ASSIGNED_USER_NAME' => 
  array (
    'width' => '5%',
    'label' => 'LBL_LIST_ASSIGNED_USER',
    'module' => 'Employees',
    'id' => 'ASSIGNED_USER_ID',
    'default' => true,
  ),
  'AMOUNT' => 
  array (
    'type' => 'currency',
    'label' => 'LBL_AMOUNT',
    'currency_format' => true,
    'width' => '10%',
    'default' => false,
  ),
  'PRICE_FORMAT' => 
  array (
    'type' => 'enum',
    'studio' => 'visible',
    'label' => 'LBL_PRICE_FORMAT',
    'width' => '10%',
    'default' => false,
  ),
  'TIER_PRICE' => 
  array (
    'type' => 'enum',
    'studio' => 'visible',
    'label' => 'LBL_TIER_PRICE',
    'width' => '10%',
    'default' => false,
  ),
  'STARTING_UNIT' => 
  array (
    'type' => 'currency',
    'label' => 'LBL_STARTING_UNIT',
    'currency_format' => true,
    'width' => '10%',
    'default' => false,
  ),
  'ENDING_UNIT' => 
  array (
    'type' => 'currency',
    'label' => 'LBL_ENDING_UNIT',
    'currency_format' => true,
    'width' => '10%',
    'default' => false,
  ),
  'SB_PRICEBOOK_TYPE' => 
  array (
    'width' => '15%',
    'label' => 'LBL_TYPE',
    'default' => false,
  ),
  'LEAD_SOURCE' => 
  array (
    'width' => '15%',
    'label' => 'LBL_LEAD_SOURCE',
    'default' => false,
  ),
  'NEXT_STEP' => 
  array (
    'width' => '10%',
    'label' => 'LBL_NEXT_STEP',
    'default' => false,
  ),
  'PROBABILITY' => 
  array (
    'width' => '10%',
    'label' => 'LBL_PROBABILITY',
    'default' => false,
  ),
  'DATE_ENTERED' => 
  array (
    'width' => '10%',
    'label' => 'LBL_DATE_ENTERED',
    'default' => false,
  ),
  'CREATED_BY_NAME' => 
  array (
    'width' => '10%',
    'label' => 'LBL_CREATED',
    'default' => false,
  ),
  'MODIFIED_BY_NAME' => 
  array (
    'width' => '5%',
    'label' => 'LBL_MODIFIED',
    'default' => false,
  ),
);
;
?>
