<?php
/*
Plugin Name: Contact Form 7 - CUIT field
Description: This plugin adds a new field in which you can set the CUIT number to Contact Form 7.
Version: 1.0.0
Author: diego2k
Text Domain: cf7-cuit-field
Domain Path: /assets/languages/

Copyright © 2020 Diego Coppari
*/

//if(class_exists('WPCF7'))
{
	require dirname(__FILE__).'/cf7-cuit-settings.php';
	require dirname(__FILE__).'/cf7-cuit-field.php';
}
