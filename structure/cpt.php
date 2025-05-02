<?php

if ( ! function_exists('custom_post_apartamente') ) {
// Register Custom Post Type
function custom_post_apartamente() {

	$labels = array(
		'name'                  => _x( 'Apartamente', 'Post Type General Name', 'srz' ),
		'singular_name'         => _x( 'Apartament', 'Post Type Singular Name', 'srz' ),
		'menu_name'             => __( 'Apartamente', 'srz' ),
		'name_admin_bar'        => __( 'Apartament', 'srz' ),
		'archives'              => __( 'Arhiva Apartamente', 'srz' ),
		'attributes'            => __( 'Atribute Apartament', 'srz' ),
		'parent_item_colon'     => __( 'Apartament Parinte:', 'srz' ),
		'all_items'             => __( 'Toate Apartamentele', 'srz' ),
		'add_new_item'          => __( 'Adauga apartament nou', 'srz' ),
		'add_new'               => __( 'Adauga nou', 'srz' ),
		'new_item'              => __( 'Apartament nou', 'srz' ),
		'edit_item'             => __( 'Editeaza Apartament', 'srz' ),
		'update_item'           => __( 'Actualizeaza Apartament', 'srz' ),
		'view_item'             => __( 'Vezi Apartament', 'srz' ),
		'view_items'            => __( 'Vezi Apartamentele', 'srz' ),
		'search_items'          => __( 'Cauta Apartament', 'srz' ),
		'not_found'             => __( 'Nu a fost gasit', 'srz' ),
		'not_found_in_trash'    => __( 'Nu este in cosul de gunoi', 'srz' ),
		'featured_image'        => __( 'Imagine reprezentativa', 'srz' ),
		'set_featured_image'    => __( 'Adauga imagine reprezentativa', 'srz' ),
		'remove_featured_image' => __( 'Sterge imagine reprezentativa', 'srz' ),
		'use_featured_image'    => __( 'Foloseste ca imagine reprezentativa', 'srz' ),
		'insert_into_item'      => __( 'Adauga in apartament', 'srz' ),
		'uploaded_to_this_item' => __( 'Incarcate in acest apartament', 'srz' ),
		'items_list'            => __( 'Lista Apartamente', 'srz' ),
		'items_list_navigation' => __( 'Items list navigation', 'srz' ),
		'filter_items_list'     => __( 'Filter items list', 'srz' ),
	);
	$rewrite = array(
		'slug'                  => 'vanzare-apartamente',
		'with_front'            => true,
		'pages'                 => true,
		'feeds'                 => true,
	);
	$args = array(
		'label'                 => __( 'Apartament', 'srz' ),
		'description'           => __( 'Apartamente', 'srz' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'thumbnail', 'revisions' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-building',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'show_in_rest'          => true,
		'has_archive'           => 'vanzare-apartamente',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'rewrite'               => $rewrite,
		'capability_type'       => 'page',
	);
	register_post_type( 'apartamente', $args );

}
add_action( 'init', 'custom_post_apartamente', 0 );

}

if ( ! function_exists('custom_post_ansamblu') ) {

// Register Custom Post Type
function custom_post_ansamblu() {

	$labels = array(
		'name'                  => _x( 'Ansambluri', 'Post Type General Name', 'srz' ),
		'singular_name'         => _x( 'Ansambluri', 'Post Type Singular Name', 'srz' ),
		'menu_name'             => __( 'Ansambluri', 'srz' ),
		'name_admin_bar'        => __( 'Ansambluri', 'srz' ),
		'archives'              => __( 'Arhiva Ansambluri', 'srz' ),
		'attributes'            => __( 'Atribute Ansambluri', 'srz' ),
		'parent_item_colon'     => __( 'Parent Item:', 'srz' ),
		'all_items'             => __( 'Toate Ansamblurile', 'srz' ),
		'add_new_item'          => __( 'Adauga Ansamblu', 'srz' ),
		'add_new'               => __( 'Add New', 'srz' ),
		'new_item'              => __( 'New Item', 'srz' ),
		'edit_item'             => __( 'Edit Item', 'srz' ),
		'update_item'           => __( 'Update Item', 'srz' ),
		'view_item'             => __( 'View Item', 'srz' ),
		'view_items'            => __( 'View Items', 'srz' ),
		'search_items'          => __( 'Search Item', 'srz' ),
		'not_found'             => __( 'Not found', 'srz' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'srz' ),
		'featured_image'        => __( 'Featured Image', 'srz' ),
		'set_featured_image'    => __( 'Set featured image', 'srz' ),
		'remove_featured_image' => __( 'Remove featured image', 'srz' ),
		'use_featured_image'    => __( 'Use as featured image', 'srz' ),
		'insert_into_item'      => __( 'Insert into item', 'srz' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'srz' ),
		'items_list'            => __( 'Items list', 'srz' ),
		'items_list_navigation' => __( 'Items list navigation', 'srz' ),
		'filter_items_list'     => __( 'Filter items list', 'srz' ),
	);
	$args = array(
		'label'                 => __( 'Ansambluri', 'srz' ),
		'description'           => __( 'Ansambluri', 'srz' ),
		'labels'                => $labels,
		'supports'              => array('title', 'thumbnail', 'editor' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-bank',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'show_in_rest'          => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'ansambluri', $args );

}
add_action( 'init', 'custom_post_ansamblu', 0 );

}


if ( ! function_exists( 'custom_taxonomy_zone' ) ) {

// Register Custom Taxonomy
function custom_taxonomy_zone() {

	$labels = array(
		'name'                       => _x( 'Zone', 'Taxonomy General Name', 'srz' ),
		'singular_name'              => _x( 'Zona', 'Taxonomy Singular Name', 'srz' ),
		'menu_name'                  => __( 'Zona', 'srz' ),
		'all_items'                  => __( 'Toate zonele', 'srz' ),
		'parent_item'                => __( 'Parent Item', 'srz' ),
		'parent_item_colon'          => __( 'Parent Item:', 'srz' ),
		'new_item_name'              => __( 'New Item Name', 'srz' ),
		'add_new_item'               => __( 'Add New Item', 'srz' ),
		'edit_item'                  => __( 'Edit Item', 'srz' ),
		'update_item'                => __( 'Update Item', 'srz' ),
		'view_item'                  => __( 'View Item', 'srz' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'srz' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'srz' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'srz' ),
		'popular_items'              => __( 'Popular Items', 'srz' ),
		'search_items'               => __( 'Search Items', 'srz' ),
		'not_found'                  => __( 'Not Found', 'srz' ),
		'no_terms'                   => __( 'No items', 'srz' ),
		'items_list'                 => __( 'Items list', 'srz' ),
		'items_list_navigation'      => __( 'Items list navigation', 'srz' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'zona', array( 'apartamente', 'ansambluri' ), $args );

}
add_action( 'init', 'custom_taxonomy_zone', 0 );

}