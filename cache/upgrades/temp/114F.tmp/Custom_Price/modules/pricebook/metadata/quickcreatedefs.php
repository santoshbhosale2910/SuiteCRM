<?php
$module_name = 'sb_pricebook';
$_object_name = 'sb_pricebook';
$viewdefs [$module_name] = 
array (
  'QuickCreate' => 
  array (
    'templateMeta' => 
    array (
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
      'javascript' => '{$PROBABILITY_SCRIPT}',
      'useTabs' => false,
      'tabDefs' => 
      array (
        'LBL_SALE_INFORMATION' => 
        array (
          'newTab' => false,
          'panelDefault' => 'expanded',
        ),
      ),
    ),
    'panels' => 
    array (
      'lbl_sale_information' => 
      array (
        0 => 
        array (
          0 => 'name',
          1 => 
          array (
            'name' => 'assigned_user_name',
            'displayParams' => 
            array (
              'required' => true,
            ),
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
            'name' => 'starting_unit',
            'label' => 'LBL_STARTING_UNIT',
          ),
          1 => 
          array (
            'name' => 'ending_unit',
            'label' => 'LBL_ENDING_UNIT',
          ),
        ),
        4 => 
        array (
          0 => 'date_closed',
          1 => 
          array (
            'name' => 'version_optimized_lock',
            'studio' => 'visible',
            'label' => 'LBL_VERSION_OPTIMIZED_LOCK',
          ),
        ),
        5 => 
        array (
          0 => 'description',
          1 => '',
        ),
      ),
    ),
  ),
);
;
?>
