<?php
// created: 2018-07-10 20:00:30
$dictionary["sb_pricebook_aos_products"] = array (
  'true_relationship_type' => 'one-to-many',
  'relationships' => 
  array (
    'sb_pricebook_aos_products' => 
    array (
      'lhs_module' => 'AOS_Products',
      'lhs_table' => 'aos_products',
      'lhs_key' => 'id',
      'rhs_module' => 'sb_pricebook',
      'rhs_table' => 'sb_pricebook',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'sb_pricebook_aos_products_c',
      'join_key_lhs' => 'sb_pricebook_aos_productsaos_products_ida',
      'join_key_rhs' => 'sb_pricebook_aos_productssb_pricebook_idb',
    ),
  ),
  'table' => 'sb_pricebook_aos_products_c',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'id',
      'type' => 'varchar',
      'len' => 36,
    ),
    1 => 
    array (
      'name' => 'date_modified',
      'type' => 'datetime',
    ),
    2 => 
    array (
      'name' => 'deleted',
      'type' => 'bool',
      'len' => '1',
      'default' => '0',
      'required' => true,
    ),
    3 => 
    array (
      'name' => 'sb_pricebook_aos_productsaos_products_ida',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'sb_pricebook_aos_productssb_pricebook_idb',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'sb_pricebook_aos_productsspk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'sb_pricebook_aos_products_ida1',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'sb_pricebook_aos_productsaos_products_ida',
      ),
    ),
    2 => 
    array (
      'name' => 'sb_pricebook_aos_products_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'sb_pricebook_aos_productssb_pricebook_idb',
      ),
    ),
  ),
);