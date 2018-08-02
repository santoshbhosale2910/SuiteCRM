<?php
// created: 2018-06-27 19:47:19
$dictionary["aos_products_aos_product_categories_1"] = array (
  'true_relationship_type' => 'many-to-many',
  'from_studio' => true,
  'relationships' => 
  array (
    'aos_products_aos_product_categories_1' => 
    array (
      'lhs_module' => 'AOS_Products',
      'lhs_table' => 'aos_products',
      'lhs_key' => 'id',
      'rhs_module' => 'AOS_Product_Categories',
      'rhs_table' => 'aos_product_categories',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'aos_products_aos_product_categories_1_c',
      'join_key_lhs' => 'aos_products_aos_product_categories_1aos_products_ida',
      'join_key_rhs' => 'aos_products_aos_product_categories_1aos_product_categories_idb',
    ),
  ),
  'table' => 'aos_products_aos_product_categories_1_c',
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
      'name' => 'aos_products_aos_product_categories_1aos_products_ida',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'aos_products_aos_product_categories_1aos_product_categories_idb',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'aos_products_aos_product_categories_1spk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'aos_products_aos_product_categories_1_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'aos_products_aos_product_categories_1aos_products_ida',
        1 => 'aos_products_aos_product_categories_1aos_product_categories_idb',
      ),
    ),
  ),
);