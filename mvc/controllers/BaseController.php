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

/**
 *
 * @author José María Valera Reales
 */
abstract class BaseController {

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
	 * Devuelve la ruta relativa donde se encuentran las vistas
	 *
	 * @return string
	 */
	protected static function getTemplatesFolderLocation() {
		return str_replace('//', '/', dirname(__FILE__) . '/') . '../templates';
	}

	/**
	 * Añadimos las variables comunes que todos los controladores.
	 * Aquí añadiremos las variables comunes como el usuario actual, entorno, etc, que tendrán
	 * disponibles todas las vistas.
	 *
	 * @param array $templateVars
	 *        	Referencia del array con las variables que pasaran todos los controladores a sus vistas
	 */
	private function addGlobalVariables(&$templateVars = []) {
		return array_merge($templateVars, [
			'adminEmail' => ADMIN_EMAIL,
			'atomUrl' => get_bloginfo('atom_url'),

			'blogTitle' => BLOG_TITLE,
			'blogDescription' => get_bloginfo('description'),

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
		echo $this->render('head_close');
		echo $this->render($templateName, $templateVars);
		wp_footer();
		echo $this->render('footer', $templateVars);
	}

	/**
	 *
	 * @param unknown $templateVars
	 */
	private function checkAndAddMagicVariables(&$templateVars) {
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
				if (isset($content['pages']) && $content['pages'] === 'all') {
					foreach ( get_all_page_ids() as $id ) {
						$pages[] = Post::find($id);
					}
					$templateVars['sidebar']['content']['pages'] = $pages;
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
	 * Pintar un partial
	 *
	 * @param string $templateName
	 *        	Nombre del partial a pintar
	 * @param array $templateVars
	 *        	Parámetros para la vista
	 */
	public function render($templateName, $templateVars = []) {
		return $this->template->render($templateName, $this->addGlobalVariables($templateVars));
	}

	/**
	 *
	 * @param integer $limit
	 * @param string $offset
	 * @param unknown $moreQuerySettings
	 * @param string $postType
	 * @param string $oddOrEven
	 * @return multitype:
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
	 * Devuelve los post fijados
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
	 *
	 * @param integer $autorId
	 * @param integer $limit
	 * @param array $moreQuerySettings
	 * @return array
	 */
	public static function getPostsByAuthor($autorId, $limit = self::LIMIT_POST_DEFAULT, $moreQuerySettings = []) {
		return self::getPostsBy(Utils::TYPE_AUTHOR, $autorId, $limit, $moreQuerySettings);
	}

	/**
	 *
	 * @param unknown $type
	 * @param unknown $by
	 * @param unknown $limit
	 * @param unknown $moreQuerySettings
	 * @return \Controllers\array<Post>
	 */
	private static function getPostsBy($type, $by, $limit = self::LIMIT_POST_DEFAULT, $moreQuerySettings = []) {
		if ($type == Utils::TYPE_TAG) {
			$tagId = Utils::getTagIdbyName($by);
			$moreQuerySettings['tag_id'] = "$tagId";
		} elseif ($type == Utils::TYPE_CATEGORY) {
			$catId = get_cat_ID($by);
			$moreQuerySettings['cat'] = "$catId";
		} elseif ($type == Utils::TYPE_SEARCH) {
			$aBuscar = $by;
			$moreQuerySettings['s'] = "$aBuscar";
		} elseif ($type == Utils::TYPE_AUTHOR) {
			$moreQuerySettings['author'] = $by;
		}
		return self::getPosts($limit, $offset, $moreQuerySettings);
	}
}
