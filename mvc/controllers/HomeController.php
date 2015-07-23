<?php

namespace Controllers;

use Models\Post;
use Models\User;
use I18n\I18n;
use Libs\Ajax;
use Models\Archive;

/**
 * Home Controller
 *
 * @author José María Valera Reales
 */
class HomeController extends BaseController {

	/**
	 * construct
	 */
	public function __construct() {
		parent::__construct();
		ob_start();
		dynamic_sidebar('home_right_1');
		$this->sidebar['home_right_1'] = ob_get_clean();
	}

	/**
	 * author.php
	 */
	public function getAuthor() {
		$author = get_queried_object();
		$user = User::find($author->ID);
		if (!$user) {
			return $this->get404();
		}
		$args = [
			'postsWhereKey' => Ajax::AUTHOR,
			'postsWhereValue' => $user->ID,
			'user' => $user
		];
		return $this->renderPage('author', $args);
	}

	/**
	 * archive.php
	 */
	public function getArchive() {
		global $wp_query;

		$keys = array_keys($wp_query->query);
		$postsArgs = [ ];
		foreach ( $keys as $k ) {
			$postsArgs['date_query'][] = [
				$k => $wp_query->query[$k]
			];
			$thingToSearch .= '/' . $wp_query->query[$k];
		}

		$args = [
			'thingType' => I18n::transu('archive'),
			'thingToSearch' => $thingToSearch,
			'postsWhereKey' => Ajax::ARCHIVE,
			'postsWhereValue' => $wp_query->query['year'] . Archive::DELIMITER . $wp_query->query['monthnum'],
			'posts' => Post::getByArchive('', false, false, $postsArgs)
		];
		return $this->renderPage('search', $args);
	}

	/**
	 * category.php
	 */
	public function getCategory() {
		$cat = get_queried_object();
		$args = [
			'thingType' => I18n::transu('category'),
			'thingToSearch' => $cat->name,
			'postsWhereKey' => Ajax::CATEGORY,
			'postsWhereValue' => $cat->term_id,
			'posts' => Post::getByCategory($cat->term_id)
		];
		return $this->renderPage('search', $args);
	}

	/**
	 * home.php
	 */
	public function getHome() {
		$args = [
			'postsWhereKey' => Ajax::HOME,
			'posts' => Post::getAll(get_option('posts_per_page'))
		];
		return $this->renderPage('home', $args);
	}

	/**
	 * 404.php
	 */
	public function get404() {
		return $this->renderPage('error_404');
	}

	/**
	 * search.php
	 */
	public function getSearch() {
		$searchQuery = get_search_query();
		$args = [
			'postsWhereKey' => Ajax::SEARCH,
			'postsWhereValue' => $searchQuery,
			'thingToSearch' => $searchQuery,
			'posts' => Post::getBySearch($searchQuery)
		];
		return $this->renderPage('search', $args);
	}

	/**
	 * single.php
	 */
	public function getSingle($type = 'post') {
		if (have_posts()) {
			the_post();
			$post = Post::find(get_the_ID());
		}
		if (!isset($post)) {
			return $this->get404();
		}
		return $this->renderPage($type, [
			$type => $post
		]);
	}

	/**
	 * tag.php
	 */
	public function getTag() {
		$tag = get_queried_object();
		$args = [
			'postsWhereKey' => Ajax::TAG,
			'postsWhereValue' => $tag->term_id,
			'thingType' => I18n::transu('tag'),
			'thingToSearch' => $tag->name,
			'posts' => Post::getByTag($tag->term_id)
		];
		return $this->renderPage('search', $args);
	}
}
