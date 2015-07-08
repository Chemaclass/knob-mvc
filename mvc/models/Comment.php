<?php

namespace Models;

use Models\Post;
use Models\User;

/**
 *
 * @author chema
 */
class Comment extends ModelBase {
	static $table = "comments";
	static $PK = 'comment_ID';

	/*
	 * Some constants
	 */
	const MAX_LENGTH = 1000;
	const PENDING = 0;
	const APROVE = 1;

	/**
	 * Delete comment
	 */
	public function delete($forceDelete = false) {
		wp_delete_comment($this->ID, $forceDelete);
	}

	/**
	 * Get the post relaction with the commnet
	 *
	 * @return Post
	 */
	public function getPost() {
		return Post::find($this->comment_post_ID);
	}

	/**
	 * Get the author of the comment
	 *
	 * @return User
	 */
	public function getUser() {
		// For to have all default values from an User
		if (!$this->user_id) {
			return new User();
		}
		return User::find($this->user_id);
	}

	/**
	 * Return the user_id
	 *
	 * @return int
	 */
	public function getUserId() {
		return $this->user_id;
	}

	/**
	 * Return the comment_author
	 *
	 * @return string
	 */
	public function getAuthorName() {
		return $this->comment_author;
	}

	/**
	 * Return the comment_author
	 *
	 * @return string
	 */
	public function getAuthorEmail() {
		return $this->comment_author_email;
	}

	/**
	 * Return the comment_content
	 *
	 * @return string
	 */
	public function getContent() {
		return $this->comment_content;
	}

	/**
	 * Return the comment_date_gmt
	 *
	 * @return string
	 */
	public function getDateGmt() {
		return $this->comment_date_gmt;
	}

	/**
	 * Save or Update the comment
	 */
	public function save() {
		global $wpdb;
		$isExists = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*)
				FROM wp_comments
				WHERE comment_ID = %d', $this->comment_ID));
		$c = 'comment_';
		if ($isExists) { // update
			return $wpdb->query($wpdb->prepare("UPDATE wp_comments
					SET {$c}post_ID = %d, {$c}author = %s,
						{$c}author_email = %s, {$c}author_url = %s,
						{$c}author_IP = %s, {$c}date = %s,
						{$c}date_gmt = %s, {$c}content = %s,
						{$c}karma = %s, {$c}approved = %s,
						{$c}agent = %s, {$c}type = %s,
						{$c}parent = %s, user_id = %s
					WHERE comment_ID = %d", $this->comment_post_ID, $this->comment_author, $this->comment_author_email, $this->comment_author_url, $this->comment_author_IP, $this->comment_date, $this->comment_date_gmt, $this->comment_content, $this->comment_karma, $this->comment_approved, $this->comment_agent, $this->comment_type, $this->comment_parent, $this->user_id, $this->comment_ID));
		}
	}
}