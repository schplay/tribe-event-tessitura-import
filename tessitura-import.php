<?php
/**
 * Plugin Name:     Tessitura Import
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     Import performances from Tessitura API into Events Calendar Pro
 * Author:          David Myers
 * Author URI:      http://davidmyers.name
 * Text Domain:     tessitura-import
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Tessitura_Import
 */

// Your code starts here.
require_once('src/Tessitura.php');

add_action('plugins_loaded', 'initTessituraImporter');

function initTessituraImporter()
{
	try {
		TessituraImporter::checkConfig();
	} catch(Exception $e) {
		return new WP_Error('TEIM', $e->getMessage());
	}
}