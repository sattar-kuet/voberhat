<?php

$upload_dir = wp_upload_dir();
if ( isset($upload_dir['basedir']) ) {

	$demo_import_base_dir = $upload_dir['basedir'] . '/greenmart_import/';
	$path_dir = $demo_import_base_dir . 'data/';
	$path_uri = $demo_import_base_dir . 'data/';

	if ( is_dir($path_dir) ) {

		$demo_datas = array(
			'home'               => array(
				'data_dir'      => $path_dir . 'home',
				'title'         => esc_html__( 'Home', 'greenmart' ),
			),
			'home2'               => array(
				'data_dir'      => $path_dir . 'home2',
				'title'         => esc_html__( 'Home 2', 'greenmart' ),
			),
			'home3'               => array(
				'data_dir'      => $path_dir . 'home3',
				'title'         => esc_html__( 'Home 3', 'greenmart' ),
			),
			'home4'               => array(
				'data_dir'      => $path_dir . 'home4',
				'title'         => esc_html__( 'Home 4', 'greenmart' ),
			),			
			'home5'               => array(
				'data_dir'      => $path_dir . 'home5',
				'title'         => esc_html__( 'Home 5', 'greenmart' ),
			),
			'home6'               => array(
				'data_dir'      => $path_dir . 'home6',
				'title'         => esc_html__( 'Home 6', 'greenmart' ),
			),
			'home7'               => array(
				'data_dir'      => $path_dir . 'home7',
				'title'         => esc_html__( 'Home 7', 'greenmart' ),
			),
			'home8'               => array(
				'data_dir'      => $path_dir . 'home8',
				'title'         => esc_html__( 'Home 8', 'greenmart' ),
			)
		);
	}
}