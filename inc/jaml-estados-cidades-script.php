<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function jaml_script_estados_cidades(){	
	wp_enqueue_script( 'jamlcidades', WP_PLUGIN_URL . '/jaml-restricted-access/' . 'js/jaml_cidades.js');
	wp_enqueue_script( 'jamlestados', WP_PLUGIN_URL . '/jaml-restricted-access/'. 'js/jaml_estados.js');
}

add_action( 'wp_enqueue_scripts', 'jaml_script_estados_cidades' );