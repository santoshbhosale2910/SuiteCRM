<?php
// created: 2018-07-10 19:55:09
$dictionary["sb_pricebook"]["fields"]["sb_pricebook_aos_products"] = array (
  'name' => 'sb_pricebook_aos_products',
  'type' => 'link',
  'relationship' => 'sb_pricebook_aos_products',
  'source' => 'non-db',
  'module' => 'AOS_Products',
  'bean_name' => 'AOS_Products',
  'vname' => 'LBL_SB_PRICEBOOK_AOS_PRODUCTS_FROM_AOS_PRODUCTS_TITLE',
  'id_name' => 'sb_pricebook_aos_productsaos_products_ida',
);
$dictionary["sb_pricebook"]["fields"]["sb_pricebook_aos_products_name"] = array (
  'name' => 'sb_pricebook_aos_products_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_SB_PRICEBOOK_AOS_PRODUCTS_FROM_AOS_PRODUCTS_TITLE',
  'save' => true,
  'id_name' => 'sb_pricebook_aos_productsaos_products_ida',
  'link' => 'sb_pricebook_aos_products',
  'table' => 'aos_products',
  'module' => 'AOS_Products',
  'rname' => 'name',
);
$dictionary["sb_pricebook"]["fields"]["sb_pricebook_aos_productsaos_products_ida"] = array (
  'name' => 'sb_pricebook_aos_productsaos_products_ida',
  'type' => 'link',
  'relationship' => 'sb_pricebook_aos_products',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_SB_PRICEBOOK_AOS_PRODUCTS_FROM_SB_PRICEBOOK_TITLE',
);
