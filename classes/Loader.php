<?php

namespace Symmetry;

class Loader {
	
	/**
	 * @description Construct
	 */
	public function __construct() {}
	
	/**
	 * @description Load components
	 */
	public static function loadComponents() {
		$baseDir = get_stylesheet_directory();
		$components = array_diff(scandir($baseDir . '/components/'), array('.', '..'));
		
		// load components
		foreach ($components as $component) {
			require_once($baseDir . '/components/' . $component . '/' . self::getComponentClass($component));
		}
	}
	
	
	/**
	 * @description Require all files in provided directory
	 * @param $dir
	 * @param $excludes
	 */
	public static function requireBulk($dir, $excludes = array()) {
		// define regex
		$fileNameWithoutExtensionRegex = '/([A-Z])\w+/';
		
		// extract files
		$files = array_diff(scandir($dir), array('.', '..'));
		
		// iterate and load files
		foreach ($files as $file) {
			
			// check if file must be skipped
			if (count($excludes) > 0 ) {
				preg_match($fileNameWithoutExtensionRegex, $file, $matches);
				if (array_search($matches[0], $excludes) > -1) {
					continue;
				}
			}
			require_once($dir . '/' . $file);
		}
	}
	
	
	/**
	 * @description Register widgets
	 * @param array $widgets
	 */
	public static function registerWidgets($widgets = array()) {
		if (count($widgets) > 0) {
			foreach ($widgets as $widget) {
				register_widget($widget);
			}
		}
	}
	
	
	/**
	 * @description Get component class
	 * @param $component
	 * @return string
	 */
	private static function getComponentClass($component) {
		$extension = '.php';
		$componentClass = ucfirst($component) . $extension;
		
		if (strpos($component, '-') !== false || strpos($component, '—') !== false) {
			$separator = strpos($component, '-') !== false ? '-' : '—';
			
			$nameArr = array_map(function ($word) {
				return ucfirst($word);
			}, explode($separator, $component));
			
			$componentClass = implode('', $nameArr) . $extension;
		}
		
		return $componentClass;
	}
}