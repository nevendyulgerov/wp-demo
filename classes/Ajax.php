<?php

namespace Symmetry;

class Ajax {
	
	/**
	 * @description Get ajax name
	 * @return null
	 */
	public static function getAjaxName() {
		return Settings::$themeName;
	}
	
	/**
	 * @description Get ajax settings
	 * @return array
	 */
	public static function getAjaxSettings() {
		return array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'ajaxAction' => Ajax::getAjaxName() . '_ajax_request',
			'ajaxToken' => wp_create_nonce(Ajax::getAjaxName())
		);
	}
	
	/**
	 * @description Handle AJAX request
	 */
	public static function handleAjaxRequest() {
		self::authorizeRequest();
		$method = isset($_POST['method']) ? $_POST['method'] : '';
		
		// TODO: Add ajax methods
		
		switch ($method) {
			case 'get_interface_settings':
				self::sendJsonResponse(array(
					'status' => 'success'
				));
				break;
				
			case 'get_projects':
				
				self::sendJsonResponse(self::createSuccessResponse(self::getPosts()));
				break;
				
			default:
				self::sendJsonResponse(self::createErrorResponse());
				break;
		}
	}
	
	/**
	 * @description Get posts
	 * @return array
	 */
	private static function getPosts() {
		$requestData = $_POST['data'];
		$projectsOffsetIndex = $requestData['projectsOffsetIndex'];
		$projectsPerPage = $requestData['projectsPerPage'];
		
		$projects_query = array(
			'post_type' => 'project',
			'posts_per_page' => $projectsPerPage,
			'offset' => $projectsOffsetIndex,
			'meta_key' => 'project_end_date',
			'orderby' => 'meta_value',
			'order' => 'DESC'
		);
		
		return get_posts($projects_query);
	}
	
	/**
	 * @description Authorize REST request
	 * @param $errorMessage
	 */
	private static function authorizeRequest($errorMessage = 'Invalid request') {
		$isAuthorized = wp_verify_nonce($_POST['token'], self::getAjaxName());
		
		if (!$isAuthorized) {
			self::sendJsonResponse(self::createErrorResponse($errorMessage));
			die;
		}
	}
	
	/**
	 * @description Create error response
	 * @param $message
	 * @return array
	 */
	private static function createErrorResponse($message = 'Invalid request') {
		return array(
			'status' => 'error',
			'message' => $message
		);
	}
	
	/**
	 * @description Create success response
	 * @param $data
	 * @return array
	 */
	private static function createSuccessResponse($data) {
		return array(
			'status' => 'success',
			'data' => $data
		);
	}
	
	/**
	 * @description Send JSON response
	 * @param $json
	 * @param $code
	 */
	private static function sendJsonResponse($json, $code = 200) {
		header('Content-Type: application/json');
		header('Cache-Control: no-cache');
		header('Access-Control-Allow-Origin');
		http_response_code($code);
		echo json_encode($json) . "\n";
		exit;
	}
}