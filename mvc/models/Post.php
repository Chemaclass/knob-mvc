<?php

namespace Models;

use I18n\I18n;
use Libs\Utils;

/**
 * Post Model
 *
 * @author José María Valera Reales
 */
class Post extends Image {
	public static $table = "posts";

	/*
	 * Default values
	 */
	const CATEGORY_BASE_DEFAULT = 'category';
	const TAG_BASE_DEFAULT = 'tag';

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
	const STATUS_APPROVE = 'approve';

	/*
	 * Counts
	 */
	const COUNT_SHORT_TITLE = 40;
	const COUNT_EXCERPT = 20;

	/*
	 * Types
	 */
	const TYPE_POST = 'post';

	/**
	 * Return all pages.
	 * (Post type page)
	 *
	 * @return array<Post>
	 */
	public static function getAllPages() {
		foreach ( get_all_page_ids() as $id ) {
			$p = Post::find($id);
			if ($p->ID) {
				$pages[] = $p;
			}
		}
		return $pages;
	}

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
	 * Return the content
	 *
	 * @return string
	 */
	public function getContent() {
		$content = apply_filters('the_content', $this->post_content);
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
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
	 * Return the form for comments
	 *
	 * @return string
	 */
	public function getFormComments() {
		ob_start();

		$placeTextarea = I18n::transu('post.share_comment');

		$params = [
			'comment_notes_after' => '',
			'author' => '<p class="comment-form-author">' . '<label for="author">' . __('Your Name') . '</label>
					<input id="author" name="author" type="text"  value="Your First and Last Name" size="30" /></p>',
			'comment_field' => '
				<div class="form-group comment-form-comment">
		            <label for="comment">' . _x('Comment', 'noun') . '</label>
		            <textarea class="form-control" id="comment" name="comment" cols="45" rows="2"
							maxlength="1000" aria-required="true" placeholder="' . $placeTextarea . '"></textarea>
		        </div>'
		];

		$placeAuthor = I18n::transu('name');
		$placeEmail = I18n::transu('email');
		$placeUrl = I18n::transu('website');

		comment_form($params, $this->ID);
		$comment_form = ob_get_clean();
		$comment_form = str_replace('id="author"', 'class="author form-control" placeholder="' . $placeAuthor . '"', $comment_form);
		$comment_form = str_replace('id="email"', 'class="email form-control" placeholder="' . $placeEmail . '"', $comment_form);
		$comment_form = str_replace('id="url"', 'class="url form-control" placeholder="' . $placeUrl . '"', $comment_form);
		$comment_form = str_replace('id="submit"', 'class="btn btn-default"', $comment_form);
		return $comment_form;
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
	 * Return
	 *
	 * @return string
	 */
	public function getExcerpt() {
		$the_excerpt = strip_tags(strip_shortcodes($this->post_content));
		$the_excerpt = trim(preg_replace('/\s\s+/', ' ', $the_excerpt));
		$the_excerpt = preg_replace("/[\xc2|\xa0]/", ' ', $the_excerpt);
		$the_excerpt = Utils::getWordsByStr($the_excerpt, self::COUNT_EXCERPT);
		return $the_excerpt;
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
	 * Return the public link.
	 *
	 * @return string
	 */
	public function getPermalink() {
		return get_permalink($this->ID);
	}

	/**
	 * Return the edit url.
	 *
	 * @return string
	 */
	public function getEditLink() {
		return get_edit_post_link($this->ID);
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

	/**
	 * Return the thumbnail medium
	 *
	 * @return string src
	 */
	public function getThumbnailMedium() {
		return $this->getThumbnail(self::IMG_SIZE_MEDIUM);
	}

	/**
	 * Devuelve el src del thumbnail del post
	 *
	 * @param string $size
	 *        	size
	 */
	public function getThumbnail($size = self::IMG_SIZE_THUMBNAIL) {
		/*
		 * Define a func for to get the attachment-src from the post_id
		 */
		$getSrc = function ($_id) use($size) {
			$imageObject = wp_get_attachment_image_src(get_post_thumbnail_id($_id), $size);
			if (empty($imageObject)) {
				return false;
			}
			return $imageObject[0];
		};

		if (($imageObject = $getSrc($this->ID))) {
			return $imageObject;
		} else {
			// if they aren't, we get the first img from the post, and let it as thumbnail
			preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $this->post_content, $matches);
			$src = $matches[1];
			$attachmentId = Utils::getAttachmentIdFromUrl($src);
			// try to set the first img as thumbnail
			// Intento establecer como thumbnail la primera img encontrada al post
			set_post_thumbnail($this->ID, $attachmentId);
			// En caso de encontrarla la devolvemos, en caso contrario devolvemos el src
			if (($imageObject = $getSrc($this->ID))) {
				return $imageObject;
			}
			return $src;
		}
	}
}