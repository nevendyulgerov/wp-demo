<?php

namespace Symmetry;

abstract class Component {

	/**
	 * @description Default values
	 * @var array
	 */
	protected static $defaults = array(
		'count' => 3
	);

	/**
	 * @description Construct
	 */
	public function __construct() {}

	/**
	 * @description Get template directory
	 * @param $component
	 * @return string
	 */
	protected static function getTemplateDir( $component ) {
		$templatesDir = get_stylesheet_directory() . '/components/' . $component . '/templates/';

		return $templatesDir;
	}

	/**
	 * @description Render given template
	 * @param string $component
	 * @param array $args
	 */
	public static function render( $component = 'component', $args ) {
		$extension = '.php';
		$template = isset( $args['template'] )
			? $args['template']
			: 'default';

		$templateDir = self::getTemplateDir( $component );
		$templateFile = $templateDir . $template . $extension;
		$templateArgs = self::filterTemplateArgs( $args );

		if ( self::validate( 'template_exists', $templateFile ) ) {
			self::renderTemplate( $templateFile, $templateArgs );
		} else {
			self::throwError( 'template_not_found', $template, $component );
		}
	}

	/**
	 * @description Render template
	 * @param $template
	 * @param $templateArgs
	 */
	protected static function renderTemplate( $template, $templateArgs ) {
		include( $template );
	}

	/**
	 * @description Filter template arguments
	 * @param $templateArgs
	 * @return mixed
	 */
	protected static function filterTemplateArgs( $templateArgs ) {
		if ( $templateArgs['template'] ) {
			unset( $templateArgs['template'] );
		}

		if ( $templateArgs['data'] ) {
			// cast data to object
			$templateArgs['data'] = (object) $templateArgs['data'];
		}

		// cast template args to object
		$templateArgs = (object) $templateArgs;

		return $templateArgs;
	}

	/**
	 * @description Validate
	 * @param $type
	 * @param $data
	 * @return bool
	 */
	protected static function validate( $type, $data ) {
		$isValid = false;

		switch ( $type ) {
			case 'template_exists':
				$isValid = file_exists( $data );
				break;
			default:
				break;
		}

		return $isValid;
	}

	/**
	 * @description Throw error
	 * @param $type
	 * @param $data1
	 * @param $data2
	 */
	protected static function throwError( $type, $data1 = null, $data2 = null ) {
		$errorStyle = 'style="margin: 20px 0px; padding: 20px; background-color: #f2dede; color: #a94442;"';
		$message = '<span style="font-size: 20px; text-transform: uppercase;">Component Error</span><br/>';

		switch ( $type ) {
			case 'template_not_found':
				$message .= 'Template ['.$data1.'] for component ['.$data2.'] not found! Make sure to define this template before calling it through ::render()';
				break;
			default:
				break;
		}

		echo '<div '.$errorStyle.'>'.$message.'</div>';
	}

	/**
	 * @description Get default
	 * @param $type
	 * @return null
	 */
	protected static function getDefault( $type ) {
		if ( in_array( $type, self::$defaults ) ) {
			return self::$defaults[ $type ];
		} else {
			return null;
		}
	}
}
