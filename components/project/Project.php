<?php
namespace Symmetry;

class Project extends Component {

	/**
	 * @description Render
	 * @param string $component
	 * @param array $args
	 */
	public static function render($args = array(), $component = '') {
		parent::render(basename(__DIR__), $args);
	}
}