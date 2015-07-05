<?php

namespace Controllers;

use Libs\Utils;
use Libs\Env;
use Models\User;
use Models\Post;
use I18n\I18n;
use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;
use Mustache_Logger_StreamLogger;
use Models\Term;

/**
 *
 * @author José María Valera Reales
 */
abstract class BaseController {

	/*
	 * Some const
	 */
	const LIMIT_POST_DEFAULT = 5;

	/*
	 * Members
	 */
	protected $currentUser;
	protected $template;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->currentUser = Utils::getCurrentUser();

		$templatesFolder = self::getTemplatesFolderLocation();

		$this->template = new Mustache_Engine(array (
			'cache_file_mode' => 0660,
			'cache_lambda_templates' => true,
			'loader' => new Mustache_Loader_FilesystemLoader($templatesFolder),
			'partials_loader' => new Mustache_Loader_FilesystemLoader($templatesFolder),
			'helpers' => array (
				'trans' => function ($value) {
					return I18n::trans($value);
				},
				'transu' => function ($value) {
					return I18n::transu($value);
				},
				'case' => [
					'lower' => function ($value) {
						return strtolower((string) $value);
					},
					'upper' => function ($value) {
						return strtoupper((string) $value);
					}
				],
				'count' => function ($value) {
					return count($value);
				},
				'moreThan1' => function ($value) {
					return count($value) > 1;
				},
				'date' => [
					'xmlschema' => function ($value) {
						return date('c', strtotime($value));
					},
					'string' => function ($value) {
						return date('l, d F Y', strtotime($value));
					},
					'format' => function ($value) {
						return date(get_option('date_format'), strtotime($value));
					}
				],
				'toArray' => function ($value) {
					return explode(',', $value);
				},
				'ucfirst' => function ($value) {
					return ucfirst($value);
				}
			),
			'escape' => function ($value) {
				return htmlspecialchars($value, ENT_COMPAT, 'UTF-8');
			},
			'charset' => 'UTF-8',
			'logger' => new Mustache_Logger_StreamLogger('php://stderr'),
			'strict_callables' => true,
			'pragmas' => [
				Mustache_Engine::PRAGMA_FILTERS,
				Mustache_Engine::PRAGMA_BLOCKS
			]
		));
	}

	/**
	 * Return the relative path location where are the templates.
	 *
	 * @return string
	 */
	protected static function getTemplatesFolderLocation() {
		return str_replace('//', '/', dirname(__FILE__) . '/') . '../templates';
	}

	/**
	 * Add the global variables for all controllers
	 *
	 * @param array $templateVars
	 */
	private function addGlobalVariables(&$templateVars = []) {
		return array_merge($templateVars, [
			'adminEmail' => ADMIN_EMAIL,
			'atomUrl' => get_bloginfo('atom_url'),

			'blogTitle' => BLOG_TITLE,
			'blogDescription' => get_bloginfo('description'),

			'categoryBase' => ($c = get_option('category_base')) ? $c : Post::CATEGORY_BASE_DEFAULT,
			'charset' => get_bloginfo('charset'),
			'commentsAtomUrl' => get_bloginfo('comments_atom_url'),
			'commentsRss2Url' => get_bloginfo('comments_rss2_url'),
			'componentsDir' => COMPONENTS_DIR,
			'currentLang' => I18n::getLangBrowserByCurrentUser(),
			'currentUser' => $this->currentUser,

			'homeUrl' => get_home_url(),
			'htmlType' => get_bloginfo('html_type'),

			'isEnvProd' => Env::isProd(),
			'isEnvDev' => Env::isDev(),
			'isEnvLoc' => Env::isLoc(),
			'isUserLoggedIn' => is_user_logged_in(),

			'language' => get_bloginfo('language'),
			'loginUrl' => wp_login_url($_SERVER['REQUEST_URI']),

			'name' => get_bloginfo('name'),

			'pingbackUrl' => get_bloginfo('pingback_url'),
			'publicDir' => PUBLIC_DIR,

			'rdfUrl' => get_bloginfo('rdf_url'),
			'rss2Url' => get_bloginfo('rss2_url'),
			'rssUrl' => get_bloginfo('rss_url'),

			'stylesheetDirectory' => get_bloginfo('stylesheet_directory'),
			'stylesheetUrl' => get_bloginfo('stylesheet_url'),

			'tagBase' => ($t = get_option('tag_base')) ? $t : Post::TAG_BASE_DEFAULT,
			'templateDirectory' => get_bloginfo('template_directory'),
			'templateUrl' => get_bloginfo('template_url'),
			'textDirection' => get_bloginfo('text_direction'),

			'version' => get_bloginfo('version'),

			'wpurl' => get_bloginfo('wpurl')
		]);
	}

	/**
	 * Print head + template + footer
	 *
	 * @param string $templateName
	 *        	Template name to print
	 * @param array $templateVars
	 *        	Parameters to template
	 */
	public function renderPage($templateName, $templateVars = []) {
		$this->addGlobalVariables($templateVars);
		$this->checkAndAddMagicVariables($templateVars);
		echo $this->render('head', $templateVars);
		wp_head();
		echo '</head>';
		echo $this->render($templateName, $templateVars);
		wp_footer();
		echo $this->render('footer', $templateVars);
	}

	/**
	 * Put all defined vars into the "default" ones (and override it).
	 *
	 * @param array $templateVars
	 */
	private function transformTemplateVars(&$templateVars) {
		// Get the default values
		$postWithTo = static::getPostWithInHomeDefault();
		$sidebarTo = static::getSidebarPropertiesDefault();

		// If doesn't exists, we put the values by default
		$postWithFrom = (isset($templateVars['postWith']) ? $templateVars['postWith'] : $postWithTo);
		$sidebarFrom = (isset($templateVars['sidebar']) ? $templateVars['sidebar'] : $sidebarTo);

		$listFromTo = [
			'postWith' => [
				'from' => $postWithFrom,
				'to' => $postWithTo
			],
			'sidebar' => [
				'from' => $sidebarFrom,
				'to' => $sidebarTo
			]
		];

		// overwriting
		$overwritingDefaultValues = function ($listFrom, &$listTo) use(&$overwritingDefaultValues) {
			foreach ( array_keys($listFrom) as $key ) {
				$value = $listFrom[$key];
				if (!is_array($value)) {
					$listTo[$key] = $value;
				} else {
					$overwritingDefaultValues($listFrom[$key], $listTo[$key]);
				}
			}
		};

		// Do it!
		foreach ( $listFromTo as $itemKey => $itemFromTo ) {
			// Overwriting
			$overwritingDefaultValues($itemFromTo['from'], $itemFromTo['to']);
			// Put the final array into the templateVars array
			$templateVars[$itemKey] = $itemFromTo['to'];
		}
	}

	/**
	 *
	 * @param array $templateVars
	 */
	private function checkingTemplateVars(&$templateVars) {
		// searcher
		// TODO:

		// sidebar
		if (isset($templateVars['sidebar'])) {
			$sidebar = $templateVars['sidebar'];
			// sidebar.active
			if (!isset($sidebar['active'])) {
				$templateVars['sidebar']['active'] = true;
			}
			// sidebar.position
			if (isset($sidebar['position'])) {
				$pos = $sidebar['position'];
				$templateVars['sidebar'][$pos] = true;
			}
			// sidebar.content
			if (isset($sidebar['content'])) {
				$content = $sidebar['content'];

				// sidebar.content.pages
				if (isset($content['pages'])) {
					$ids = ($content['pages'] === 'all') ? get_all_page_ids() : $content['pages'];
					if (!is_array($ids)) {
						$_ids[] = $ids;
						$ids = $_ids;
					}
					foreach ( $ids as $id ) {
						$p = Post::find($id);
						if ($p->ID) {
							$pages[] = $p;
						}
					}

					$templateVars['sidebar']['content']['pages'] = $pages;
				} else {
					$templateVars['sidebar']['content']['pages'] = [ ];
				}

				// sidebar.content.categories
				if (isset($content['categories']) && $content['categories'] === 'all') {

					$categories = Term::getAllCategories([
						'orderby' => 'count',
						'hide_empty' => true
					]);

					$templateVars['sidebar']['content']['categories'] = $categories;
				} else {
					$templateVars['sidebar']['content']['categories'] = [ ];
				}

				// sidebar.content.tags
				if (isset($content['tags']) && $content['tags'] === 'all') {
					$tags = Term::getAllTags([
						'orderby' => 'count',
						'hide_empty' => true
					]);
					$templateVars['sidebar']['content']['tags'] = $tags;
				} else {
					$templateVars['sidebar']['content']['tags'] = [ ];
				}
			}
		}
		// postWith
		if (isset($templateVars['postWith'])) {
			$postsWith = $templateVars['postWith'];
			// postsWith.author
			if (isset($postsWith['author'])) {
				$author = $postsWith['author'];
				// postsWith.author.url
				if (isset($author['url'])) {
					$url = $author['url'];
					$templateVars['postWith']['author'][$url] = true;
				}
			}
		}
	}

	/**
	 * Check and add magic variables
	 *
	 * @param array $templateVars
	 */
	private function checkAndAddMagicVariables(&$templateVars) {
		$this->transformTemplateVars($templateVars);
		$this->checkingTemplateVars($templateVars);
	}

	/**
	 *
	 * @return array
	 */
	protected static function getPostWithInHomeDefault() {
		/*
		 * Options:
		 * - author.url => postsUrl | userUrl
		 * - commentsNumber => true | false
		 * - date => true | false
		 * - thumbnail => true | false
		 * - excerpt => true | false
		 */
		return [
			'author' => [
				'url' => 'postsUrl'
			],
			'commentsNumber' => true,
			'date' => true,
			'thumbnail' => true,
			'excerpt' => true
		];
	}

	/**
	 *
	 * @return array
	 */
	protected static function getSidebarPropertiesDefault() {
		/*
		 * Options:
		 * - position => left | right
		 * - content.pages => all
		 * - content.categories => all
		 * - content.searcher.withButton => true
		 * - content.tags => all
		 * - position => left | right
		 */
		return [
			'active' => true,
			'content' => [
				'pages' => 'all',
				'categories' => 'all',
				'searcher' => [
					'withButton' => true
				],
				'tags' => 'all'
			],
			'position' => 'left'
		];
	}

	/**
	 * Render a partial
	 *
	 * @param string $templateName
	 * @param array $templateVars
	 */
	public function render($templateName, $templateVars = []) {
		return $this->template->render($templateName, $this->addGlobalVariables($templateVars));
	}

	/**
	 * Get Posts
	 *
	 * @param integer $limit
	 * @param string $offset
	 * @param array $moreQuerySettings
	 * @param string $postType
	 * @param string $oddOrEven
	 * @return array<Post>
	 */
	public static function getPosts($limit = -1, $offset = false, $moreQuerySettings = []) {
		// Get all fixed posts.
		$posts = self::getStickyPosts($limit, $offset, $moreQuerySettings, $postType, $oddOrEven);
		$isCat = isset($moreQuerySettings['cat']);
		$postsStickyIds = [ ];
		// Check all fixed posts with the category we're searching.
		foreach ( get_option('sticky_posts') as $postId ) {
			if ($isCat && ($post = Post::find($postId)) && $post->getCategory()->term_id == $moreQuerySettings['cat']) {
				$postsStickyIds[] = $postId;
			}
		}
		// Check the total fixed posts with the total post we got it.
		$countSticky = count($postsStickyIds);
		// if it's the same doesn't matter. If it's different we have to rest the different.
		$limit = (count($posts) == $countSticky) ? $limit - $countSticky : $limit;

		if (!isset($moreQuerySettings['post_type'])) {
			$moreQuerySettings['post_type'] = Post::TYPE_POST;
		}

		$querySettings = [
			'orderby' => [
				'date' => 'DESC'
			],
			'post_type' => [
				$moreQuerySettings['post_type']
			],
			'post__not_in' => $postsStickyIds,
			'posts_per_page' => $limit,
			'post_status' => Post::STATUS_PUBLISH
		];
		if ($offset) {
			$querySettings['offset'] = $offset;
		}
		$querySettings = array_merge($querySettings, $moreQuerySettings);
		$loop = new \WP_Query($querySettings);
		return array_merge($posts, self::loopQueryPosts($loop));
	}

	/**
	 * Return the fixed posts
	 *
	 * @return array<Post>
	 */
	private static function getStickyPosts($limit = -1, $offset = false, $moreQuerySettings = []) {
		$sticky_posts = get_option('sticky_posts');
		if (!$sticky_posts) {
			return [ ];
		}
		if (!isset($moreQuerySettings['post_type'])) {
			$moreQuerySettings['post_type'] = Post::TYPE_POST;
		}
		$querySettings = [
			'post_type' => [
				$moreQuerySettings['post_type']
			],
			'post__in' => $sticky_posts,
			'posts_per_page' => $limit
		];
		if ($offset) {
			$querySettings['offset'] = $offset;
		}
		$querySettings = array_merge($querySettings, $moreQuerySettings);
		$loop = new \WP_Query($querySettings);

		return self::loopQueryPosts($loop);
	}

	/**
	 * Loop the query and mount the Post objects
	 *
	 * @param WP_Query $loop
	 * @return array<Post>
	 */
	private static function loopQueryPosts($loop) {
		$posts = [ ];
		for($index = 0; $loop->have_posts(); $index++) {
			$loop->the_post();
			$posts[] = Post::find(get_the_ID());
		}
		return $posts;
	}

	/**
	 * Get posts from an author
	 *
	 * @param integer $autorId
	 * @param integer $limit
	 * @param array $moreQuerySettings
	 * @return array<Post>
	 */
	protected static function getPostsByAuthor($autorId, $limit = false, $moreQuerySettings = []) {
		return self::getPostsBy(Utils::TYPE_AUTHOR, $autorId, $limit, $moreQuerySettings);
	}

	/**
	 * Get posts from query search
	 *
	 * @param string $searchQuery
	 * @param integer $limit
	 * @param array $moreQuerySettings
	 * @return array<Post>
	 */
	protected static function getPostsBySearch($searchQuery, $limit = false, $moreQuerySettings = []) {
		return self::getPostsBy(Utils::TYPE_SEARCH, $searchQuery, $limit, $moreQuerySettings);
	}

	/**
	 * Get posts from a category
	 *
	 * @param integer $catId
	 * @param integer $limit
	 * @param array $moreQuerySettings
	 * @return array<Post>
	 */
	protected static function getPostsByCategory($catId, $limit = false, $moreQuerySettings = []) {
		return self::getPostsBy(Utils::TYPE_CATEGORY, $catId, $limit, $moreQuerySettings);
	}

	/**
	 *
	 * @param integer $tagId
	 * @param integer $limit
	 * @param array $moreQuerySettings
	 * @return array<Post>
	 */
	protected static function getPostsByTag($tagId, $limit = false, $moreQuerySettings = []) {
		return self::getPostsBy(Utils::TYPE_TAG, $tagId, $limit, $moreQuerySettings);
	}

	/**
	 * Get posts by type
	 *
	 * @param string $type
	 * @param integer|string $by
	 * @param integer $limit
	 * @param array $moreQuerySettings
	 * @return array<Post>
	 */
	private static function getPostsBy($type, $by, $limit = false, $moreQuerySettings = []) {
		if (!$limit) {
			$limit = get_option('posts_per_page');
		}
		if ($type == Utils::TYPE_TAG) {
			$tagId = is_numeric($by) ? $by : get_term_by('name', $by, 'post_tag')->term_id;
			$moreQuerySettings['tag_id'] = "$tagId";
		} elseif ($type == Utils::TYPE_CATEGORY) {
			$catId = is_numeric($by) ? $by : get_cat_ID($by);
			$moreQuerySettings['cat'] = "$catId";
		} elseif ($type == Utils::TYPE_SEARCH) {
			$moreQuerySettings['s'] = "$by";
		} elseif ($type == Utils::TYPE_AUTHOR) {
			$moreQuerySettings['author'] = $by;
		}
		return self::getPosts($limit, $offset, $moreQuerySettings);
	}
}
