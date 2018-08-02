<?php
$dictionary["aos_product_categories_aos_products_quotes"] = array (
	'true_relationship_type' => 'many-to-many',
	'relationships' =>
	array (
		'aos_product_categories_aos_products_quotes' =>
			array (
			'lhs_module' => 'AOS_Product_Categories',
			'lhs_table' => 'aos_product_categories',
			'lhs_key' => 'id',
			'rhs_module' => 'AOS_Products_Quotes',
			'rhs_table' => 'aos_products_quotes',
			'rhs_key' => 'id',
			'relationship_type' => 'one-to-many',
			'join_table' => 'aos_product_categories_aos_products_quotes_c',
			'join_key_lhs' => 'aos_product_categories_aos_products_quotesaos_product_categories_ida',
			'join_key_rhs' => 'aos_product_categories_aos_products_quotesaos_products_quotes_idb',
			),
	),
	'table' => 'aos_product_categories_aos_products_quotes_c',
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
	),
);