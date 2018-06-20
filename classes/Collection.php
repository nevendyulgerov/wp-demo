<?php

namespace Symmetry;

class Collection {
	
	/**
	 * @description Get posts
	 * @param $postType
	 * @param $count
	 * @param $metaQuery
	 * @param $sortOptions
	 * @return array
	 */
	public static function getPosts($postType, $count = -1, $metaQuery = array(), $sortOptions = array()) {
		$query = array(
			'post_type' => $postType,
			'posts_per_page' => $count,
			'suppress_filters' => false
		);
		
		if ($metaQuery) {
			$query['meta_query'] = $metaQuery;
		}
		
		if ($sortOptions['orderby'] && $sortOptions['order']) {
			$query['orderby'] = $sortOptions['orderby'];
			$query['order'] = $sortOptions['order'];
		}
		
		if ($sortOptions['include']) {
			$query['post__in'] = $sortOptions['include'];
		}
		
		if ($sortOptions['exclude']) {
			$query['post__not_in'] = array($sortOptions['exclude']);
		}
		
		return get_posts($query);
	}
	
	/**
	 * @description Get single post
	 * @param $postType
	 * @param $postId
	 * @return mixed
	 */
	public static function getSinglePost($postType, $postId) {
		$data = get_posts(array(
			'post_type' => $postType,
			'p' => $postId,
			'suppress_filters' => false
		));
		
		return $data[0];
	}
	
	/**
	 * @description Get featured posts
	 * @param string $postType
	 * @param int $postCount
	 * @param array $sortOptions
	 * @return array
	 */
	public static function getFeaturedPosts($postType, $postCount, $sortOptions = array()) {
		$query = array(
			'post_type' => $postType,
			'posts_per_page' => $postCount,
			'orderby' => 'post_title',
			'order' => 'ASC',
			'suppress_filters' => false,
			'meta_query' => array(
				array(
					'key' => 'is_featured',
					'value' => 1,
					'compare' => 'LIKE'
				)
			)
		);
		
		if ($sortOptions['orderby'] && $sortOptions['order']) {
			$query['orderby'] = $sortOptions['orderby'];
			$query['order'] = $sortOptions['order'];
		}
		
		if ($sortOptions['exclude']) {
			$query['post__not_in'] = array($sortOptions['exclude']);
		}
		
		return get_posts($query);
	}
	
	
	/**
	 * @description Sort taxonomy by meta 'order'
	 * @param $tax
	 * @return mixed
	 */
	protected static function sortTaxonomyByOrder($tax) {
		usort($tax, function ($a, $b) {
			$aId = $a->term_id;
			$aMeta = get_option("taxonomy_$aId");
			$aOrder = (int)$aMeta['order'];
			
			$bId = $b->term_id;
			$bMeta = get_option("taxonomy_$bId");
			$bOrder = (int)$bMeta['order'];
			
			return $aOrder > $bOrder ? 1 : ($aOrder < $bOrder ? -1 : 0);
		});
		
		return $tax;
	}
}
