<?php
use Models\Post;

/**
 * NOTE:
 * For to use this page remember that you have to create your own
 * page through the backend from Wordpress.
 *
 * @example yoursite.com/random
 * @see https://codex.wordpress.org/Template_Tags/get_posts
 */

$posts = get_posts([
	'post_status' => Post::STATUS_PUBLISH,
	'post_type' => Post::TYPE_POST,
	'showposts' => 1,
	'orderby' => 'rand'
]);

$post = Post::find($posts[0]->ID);

header("Location: {$post->getPermalink()}");
