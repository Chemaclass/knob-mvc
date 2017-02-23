<?php
/*
 * This file is part of the Knob-mvc package.
 *
 * (c) José María Valera Reales <chemaclass@outlook.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
    'orderby' => 'rand',
]);

/** @var Post $post */
$post = Post::find($posts[0]->ID);

header("Location: {$post->getPermalink()}");
