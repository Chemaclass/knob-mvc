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
	
	/**
	 * Return the first Category from this Post
	 * http://codex.wordpress.org/Function_Reference/get_the_category
	 *
	 * @return object
	 */
	public function getCategory() {
		$categories = get_the_category($this->ID);
		return $categories[0];
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
}