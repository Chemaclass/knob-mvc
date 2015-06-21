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
	private function addGlobalVariables($templateVars = []) {
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
	 * Pintar header + plantilla + footer
	 *
	 * @param string $templateName
	 *        	Nombre de la vista a pintar
	 * @param array $templateVars
	 *        	Parámetros para la vista
	 */
	public function renderPage($templateName, $templateVars = []) {
		$templateVars = $this->addGlobalVariables($templateVars);
		// Pintamos el header, la plantilla que nos dieron y seguidamente el footer
		foreach ( [ 
			'head',
			$templateName,
			'footer' 
		] as $_template ) {
			echo $this->render($_template, $templateVars);
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
	 * @param string $dateFormat        	
	 * @param string $postType        	
	 * @param integer $numberPostsToFetch        	
	 * @param array $customFields        	
	 * @param string $oddOrEven        	
	 * @param array $moreQuerySettings        	
	 * @return array<Post>
	 */
	public static function getPosts($dateFormat = false, $postType = 'post', $numberPostsToFetch = -1, $customFields = [], $oddOrEven = false, $moreQuerySettings = []) {
		// Get all fixed posts.
		$posts = self::getStickyPosts($dateFormat, $postType, $numberPostsToFetch, $customFields, $oddOrEven, $moreQuerySettings);
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
		$numberPostsToFetch = (count($posts) == $countSticky) ? $numberPostsToFetch - $countSticky : $numberPostsToFetch;
		
		$querySettings = [ 
			'orderby' => [ 
				'date' => 'DESC' 
			],
			'post_type' => [ 
				$postType 
			],
			'post__not_in' => $postsStickyIds,
			'posts_per_page' => $numberPostsToFetch,
			'post_status' => Post::STATUS_PUBLISH 
		];
		$querySettings = array_merge($querySettings, $moreQuerySettings);
		$loop = new \WP_Query($querySettings);
		
		return array_merge($posts, self::loopQueryPosts($loop));
	}
	
	/**
	 * Devuelve los post fijados
	 *
	 * @return array<Post>
	 */
	private static function getStickyPosts($dateFormat = false, $postType = 'post', $numberPostsToFetch = -1, $customFields = array(), $oddOrEven = false, $moreQuerySettings = array()) {
		$sticky_posts = get_option('sticky_posts');
		if (!$sticky_posts) {
			return [ ];
		}
		$querySettings = [ 
			'post_type' => [ 
				$postType 
			],
			'post__in' => $sticky_posts,
			'posts_per_page' => $numberPostsToFetch 
		];
		$querySettings = array_merge($querySettings, $moreQuerySettings);
		$loop = new \WP_Query($querySettings);
		
		return self::loopQueryPosts($loop);
	}
	
	/**
	 * Loop the query and mount the Post objects
	 *
	 * @param WP_Query $loop        	
	 * @param boolean $oddOrEven        	
	 * @return array<Post>
	 */
	private static function loopQueryPosts($loop, $oddOrEven = false) {
		$posts = [ ];
		for($index = 0; $loop->have_posts(); $index++) {
			$loop->the_post();
			if (!($oddOrEven) || ($oddOrEven == 'EVEN' && $index % 2) || ($oddOrEven == 'ODD' && !($index % 2))) {
				$posts[] = Post::find(get_the_ID());
			}
		}
		return $posts;
	}
}
