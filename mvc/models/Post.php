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
	 * Const
	 */
	const IMG_THUMBNAIL = 'thumbnail';
	const IMG_MEDIUM = 'medium';
	const IMG_LARGE = 'large';
	const IMG_FULL = 'full';
	
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