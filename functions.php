<?php
use Symmetry\Loader,
  Symmetry\Settings;


/**
 * @description Bootstrap theme
 */
function bootstrapSymmetryTheme() {
	
	// set resource directories
	$themeDir = get_stylesheet_directory();
	$coreDir = $themeDir.'/core/';
	$classDir = $themeDir.'/classes/';
	$postTypesDir = $themeDir.'/post-types/';
	
	// define required classes
	$classes = array('Settings', 'Loader', 'Component');
	
	// load required classes
	foreach($classes as $class) {
		require_once($classDir . $class . '.php');
	}

	// apply settings
	Settings::$themeName = 'symmetry';

	// load components
	Loader::loadComponents();

	// load classes, skipping already loaded ones
	Loader::requireBulk($classDir, $classes);
	
	// load core functions
	Loader::requireBulk($coreDir);
	
	// load custom post types
	Loader::requireBulk($postTypesDir);
}

bootstrapSymmetryTheme();
