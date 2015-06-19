<?php

namespace Models;

/**
 * Post Model
 *
 * @author José María Valera Reales
 */
class Post extends Image {
	public static $table = "posts";
	
	/*
	 * Images sizes
	 */
	const IMG_SIZE_THUMBNAIL = 'thumbnail';
	const IMG_SIZE_MEDIUM = 'medium';
	const IMG_SIZE_LARGE = 'large';
	const IMG_SIZE_FULL = 'full';
	
	/*
	 * STATUS
	 */
	const STATUS_PUBLISH = "publish";
	const STATUS_PENDING = "pending";
	const STATUS_APPROVE = 'aprove';
	
	/*
	 * Counts
	 */
	const COUNT_SHORT_TITLE = 40;
	
	/**
	 * Return the author
	 *
	 * @return User
	 */
	public function getAuthor() {
		return User::find($this->post_author);
	}
	
	/**
	 *
	 * @return array
	 */
	public function getCategories() {
		$categories = get_the_category($this->ID);
		if (!$categories) {
			return [ ];
		}
		foreach ( $categories as $category ) {
			$category->category_link = get_category_link($category->term_id);
			$array[] = $category;
		}
		return $array;
	}
	
	/**
	 * Return the total of categories.
	 *
	 * @return number
	 */
	public function getCountCategories() {
		return count($this->getCategories());
	}
	
	/**
	 * Return all comments
	 *
	 * @see http://codex.wordpress.org/Function_Reference/get_comments
	 */
	public function getComments() {
		$args_comments = [ 
			'post_id' => $this->ID,
			'orderby' => 'comment_date_gmt',
			'status' => static::STATUS_APPROVE 
		];
		$comments = [ ];
		foreach ( get_comments($args_comments, $this->ID) as $c ) {
			$comments[] = Comment::find($c->comment_ID);
		}
		return $comments;
	}
	
	/**
	 * Return the publish date
	 *
	 * @return string
	 */
	public function getDate() {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare('SELECT post_date FROM wp_posts WHERE ID = %d', $this->ID));
	}
	
	/**
	 * Return the modified date
	 *
	 * @return string
	 */
	public function getDateModified() {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare('SELECT post_modified FROM wp_posts WHERE ID = %d', $this->ID));
	}
	
	/**
	 * Return the first Category from this Post
	 * http://codex.wordpress.org/Function_Reference/get_the_category
	 *
	 * @return object
	 */
	public function getFirstCategory() {
		$categories = get_the_category($this->ID);
		return $categories[0];
	}
	
	/**
	 *
	 * @return Post
	 */
	public function getPreviousPost() {
		return Post::find(get_previous_post()->ID);
	}
	
	/**
	 *
	 * @return Post
	 */
	public function getNextPost() {
		return Post::find(get_next_post()->ID);
	}
	
	/**
	 * Return the title Post
	 *
	 * @param string $short        	
	 * @param integer $countShort        	
	 * @return string The Title
	 */
	public function getTitle($short = false, $countShort = self::COUNT_SHORT_TITLE) {
		$title = get_the_title($this->ID);
		if (!$short || (strlen($title) > $countShort)) {
			return $title;
		}
		
		$substr = substr($title, 0, $countShort);
		// strrchr => return all after the last ocurrence from one str
		$lastSpace = strpos($substr, strrchr($substr, ' '));
		if ($lastSpace) {
			$substr = substr($substr, 0, $lastSpace) . '...';
		}
		return $substr;
	}
}