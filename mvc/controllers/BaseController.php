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
	private function _addGlobalVars($templateVars = []) {
		return array_merge($templateVars, [ 
			'blogTitle' => self::_getBlogTitle(),
			'currentUser' => $this->currentUser,
			'homeUrl' => get_home_url(),
			'isEnvProd' => Env::isProd(),
			'isEnvDev' => Env::isDev(),
			'isEnvLoc' => Env::isLoc(),
			'publicDir' => PUBLIC_DIR,
			'componentsDir' => COMPONENTS_DIR,
			'loginUrl' => wp_login_url($_SERVER['REQUEST_URI']),
			'currentLang' => I18n::getLangBrowserByCurrentUser() 
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
		$templateVars = $this->_addGlobalVars($templateVars);
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
		return $this->template->render($templateName, $this->_addGlobalVars($templateVars));
	}
	
	/**
	 * Devuelve una lista con la información básica del blog
	 *
	 * @return multitype:string NULL
	 */
	public static function getBlogInfoData() {
		return array (
			'blogTitle' => self::_getBlogTitle(),
			'name' => get_bloginfo('name'),
			'description' => get_bloginfo('description'),
			'adminEmail' => get_bloginfo('admin_email'),
			
			'url' => get_bloginfo('url'),
			'wpurl' => get_bloginfo('wpurl'),
			
			'stylesheetDirectory' => get_bloginfo('stylesheet_directory'),
			'stylesheetUrl' => get_bloginfo('stylesheet_url'),
			'templateDirectory' => get_bloginfo('template_directory'),
			'templateUrl' => get_bloginfo('template_url'),
			
			'atomUrl' => get_bloginfo('atom_url'),
			'rss2Url' => get_bloginfo('rss2_url'),
			'rssUrl' => get_bloginfo('rss_url'),
			'pingbackUrl' => get_bloginfo('pingback_url'),
			'rdfUrl' => get_bloginfo('rdf_url'),
			
			'commentsAtom_url' => get_bloginfo('comments_atom_url'),
			'commentsRss2Url' => get_bloginfo('comments_rss2_url'),
			
			'charset' => get_bloginfo('charset'),
			'htmlType' => get_bloginfo('html_type'),
			'language' => get_bloginfo('language'),
			'textDirection' => get_bloginfo('text_direction'),
			'version' => get_bloginfo('version'),
			
			'isUserLoggedIn' => is_user_logged_in() 
		);
	}
	
	/**
	 *
	 * @return string
	 */
	private static function _getBlogTitle() {
		if (is_home()) {
			return get_bloginfo('name');
		} else {
			return wp_title("-", false, "right") . " " . get_bloginfo('name');
		}
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
		// Obtengo los post fijados
		$posts = self::_getStickyPosts($dateFormat, $postType, $numberPostsToFetch, $customFields, $oddOrEven, $moreQuerySettings);
		$isCat = isset($moreQuerySettings['cat']);
		$postsStickyIds = [ ];
		// Recorro los post fijados totales y compruebo que su categoría se corresponda con la categoría que se está buscando
		foreach ( get_option('sticky_posts') as $post_id ) {
			if ($isCat && ($post = Post::find($post_id)) && $post->getCategoria()->term_id == $moreQuerySettings['cat']) {
				$postsStickyIds[] = $post_id;
			}
		}
		// Comparamos la cantidad de post fijados totales con la cantidad de post fijados que hemos obtenido
		$countSticky = count($postsStickyIds);
		// De ser igual quiere decir que tenemos que restarle a la cantidad pedida el número de post fijados totales, de lo contrario
		// la cantidad pedida seguirá siendo la misma.
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
			'post_status' => 'publish' 
		];
		$querySettings = array_merge($querySettings, $moreQuerySettings);
		$loop = new \WP_Query($querySettings);
		
		return array_merge($posts, self::_loop($loop));
	}
	
	/**
	 * Devuelve los post fijados
	 *
	 * @return array<Post>
	 */
	private static function _getStickyPosts($dateFormat = false, $postType = 'post', $numberPostsToFetch = -1, $customFields = array(), $oddOrEven = false, $moreQuerySettings = array()) {
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
		
		return self::_loop($loop);
	}
	
	/**
	 * Recorrer la query y monta los objetos Post
	 *
	 * @param WP_Query $loop        	
	 * @param boolean $oddOrEven        	
	 * @return array<Post>
	 */
	private static function _loop($loop, $oddOrEven = false) {
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
