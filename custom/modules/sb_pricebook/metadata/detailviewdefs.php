<?php
$module_name = 'sb_pricebook';
$_object_name = 'sb_pricebook';
$viewdefs [$module_name] = 
array (
  'DetailView' => 
  array (
    'templateMeta' => 
    array (
      'form' => 
      array (
        'buttons' => 
        array (
          0 => 'EDIT',
          1 => 'DUPLICATE',
          2 => 'DELETE',
          3 => 'FIND_DUPLICATES',
        ),
      ),
      'maxColumns' => '2',
      'widths' => 
      array (
        0 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
        1 => 
        array (
          'label' => '10',
          'field' => '30',
        ),
      ),
      'useTabs' => false,
      'tabDefs' => 
      array (
        'LBL_SALE_INFORMATION' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
      ),
      'syncDetailEditViews' => true,
    ),
    'panels' => 
    array (
      'lbl_sale_information' => 
      array (
        0 => 
        array (
          0 => 
          array (
            'name' => 'product_name_c',
            'studio' => 'visible',
            'label' => 'LBL_PRODUCT_NAME',
          ),
          1 => 
          array (
            'name' => 'currency_id',
            'comment' => 'Currency used for display purposes',
            'label' => 'LBL_CURRENCY',
          ),
        ),
        1 => 
        array (
          0 => 'sb_pricebook_type',
          1 => 'amount',
        ),
        2 => 
        array (
          0 => 
          array (
            'name' => 'tier_price',
            'studio' => 'visible',
            'label' => 'LBL_TIER_PRICE',
          ),
          1 => 
          array (
            'name' => 'price_format',
            'studio' => 'visible',
            'label' => 'LBL_PRICE_FORMAT',
          ),
        ),
        3 => 
        array (
          0 => 
          array (
            'name' => 'amount_usdollar',
            'comment' => 'Formatted amount of the sale',
            'label' => 'LBL_AMOUNT_USDOLLAR',
          ),
          1 => 'assigned_user_name',
        ),
        4 => 
        array (
          0 => 
          array (
            'name' => 'starting_unit',
            'label' => 'LBL_STARTING_UNIT',
          ),
          1 => 
          array (
            'name' => 'ending_unit',
            'label' => 'LBL_ENDING_UNIT',
          ),
        ),
        5 => 
        array (
          0 => 'description',
          1 => 
          array (
            'name' => 'product_name',
            'studio' => 'visible',
            'label' => 'LBL_PRODUCT_NAME',
          ),
        ),
        6 => 
        array (
          0 => 
          array (
            'name' => 'start_date',
            'label' => 'LBL_START_DATE',
          ),
          1 => 
          array (
            'name' => 'end_date',
            'label' => 'LBL_END_DATE',
          ),
        ),
      ),
    ),
  ),
);
;
?>
