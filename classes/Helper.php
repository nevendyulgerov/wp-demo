<?php

namespace Symmetry;

class Helper {

	public function __construct() {}

	/**
	 * @description Get featured image
	 * @param $postId
	 * @return false|string
	 */
	public static function getFeaturedImage( $postId ) {
		return wp_get_attachment_url( get_post_thumbnail_id( $postId ) );
	}

	/**
	 * @description Check if variable exists
	 * @param $var
	 * @return boolean
	 */
	public static function exists( $var ) {
		return isset( $var ) && ! empty( $var );
	}

	/**
	 * @description Get GET param
	 * @param $param
	 * @return null|string
	 */
	public static function getGetParam( $param ) {
		return isset( $_GET[ $param ] ) ? sanitize_text_field( $_GET[ $param ] ) : null;
	}

	/**
	 * @description Get POST param
	 * @param $param
	 * @return null|string
	 */
	public static function getPostParam( $param ) {
		return isset( $_POST[ $param ] ) ? sanitize_text_field( $_POST[ $param ] ) : null;
	}


	/**
	 * @description Get post excerpt
	 * @param $postContent
	 * @param $count
	 * @return string
	 */
	public static function getPostExcerpt( $postContent, $count ) {
		$content = strip_tags( $postContent );
		$excerpt = substr( $content, 0, $count ).'...';

		return $excerpt;
	}

  /**
   * @description Get menu items
   * @param string $menuName
   * @return array|false
   */
	public static function getMenuItems($menuName = 'primary') {
    $locations = get_nav_menu_locations();
    $menu = wp_get_nav_menu_object($locations[$menuName]);
    $menuItems = wp_get_nav_menu_items($menu->term_id, array('order' => 'DESC'));

    return $menuItems;
  }

  /**
   * @description Get primary menu items
   * @param string $menuName
   * @return array
   */
  public static function getPrimaryMenuItems($menuName = 'primary') {
	  $menuItems = self::getMenuItems($menuName);
    $primaryMenuItems = array_filter($menuItems, function($item) {
      return !$item->menu_item_parent;
    });

    return $primaryMenuItems;
  }

  public static function getMenuItemIndexByID($targetID, $menuItems) {
    $targetIndex = -1;
    $index = 0;
    foreach ($menuItems as $menuItem) {
      if ($menuItem->ID === $targetID) {
        $targetIndex = $index;
      }
      $index++;
    }

    return $targetIndex;
  }
}
