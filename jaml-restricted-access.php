<?php
/*
Plugin name: Jaml Acesso Restrito
Plugin uri: 
Description: Plugin para gerenciar acesso restrito
Version: 1.0
Author: Augusto Monteiro
Author uri: 
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

require_once plugin_dir_path( __FILE__ ) . 'inc/jaml-estados-cidades-script.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/jaml-session-restrict-access.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/jaml-register-restrict-access.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/jaml-recover-restrict-access.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/jaml-global-restrict-style-script.php';
require_once plugin_dir_path( __FILE__ ) . 'inc/jaml-videos-restrict-access.php';
