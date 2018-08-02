<?php
 // created: 2018-07-10 20:00:30
$layout_defs["AOS_Products"]["subpanel_setup"]['sb_pricebook_aos_products'] = array (
  'order' => 100,
  'module' => 'sb_pricebook',
  'subpanel_name' => 'default',
  'sort_order' => 'asc',
  'sort_by' => 'id',
  'title_key' => 'LBL_SB_PRICEBOOK_AOS_PRODUCTS_FROM_SB_PRICEBOOK_TITLE',
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
