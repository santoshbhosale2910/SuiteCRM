<?php
 // created: 2018-07-10 03:04:41
$layout_defs["sb_pricebook"]["subpanel_setup"]['sb_pricebook_aos_products'] = array (
  'order' => 100,
  'module' => 'AOS_Products',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_SB_PRICEBOOK_AOS_PRODUCTS_FROM_AOS_PRODUCTS_TITLE',
  'get_subpanel_data' => 'sb_pricebook_aos_products',
  'top_buttons' => 
  array (
    0 => 
    array (
      'widget_class' => 'SubPanelTopButtonQuickCreate',
    ),
    1 => 
    array (
      'widget_class' => 'SubPanelTopSelectButton',
      'mode' => 'MultiSelect',
    ),
  ),
);
