<?php
$dictionary["aos_quotes_aos_product_categories"] = array (
	'true_relationship_type' => 'many-to-many',
	'relationships' =>
	array (
		'aos_quotes_aos_product_categories' =>
			array (
			'lhs_module' => 'AOS_Quotes',
			'lhs_table' => 'aos_quotes',
			'lhs_key' => 'id',
			'rhs_module' => 'AOS_Product_Categories',
			'rhs_table' => 'aos_products_categories',
			'rhs_key' => 'id',
			'relationship_type' => 'one-to-many',
			'join_table' => 'aos_quotes_aos_product_categories_c',
			'join_key_lhs' => 'aos_quotes_aos_product_categoriesaos_quotes_ida',
			'join_key_rhs' => 'aos_quotes_aos_product_categoriesaos_quotes_idb',
			),
	),
	'table' => 'aos_quotes_aos_product_categories_c',
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