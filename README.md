# README #

### What's this repository? ###

* Knob MVC
* Knob is a PHP MVC Framework for creating Wordpress templates easier and funnier than ever before.
* Version: 1.0
* Author: José María Valera Reales

### You'll need [Knob-base](https://github.com/Chemaclass/knob-base/)

You will need install via [Composer](https://getcomposer.org/) the Knob core structure. 

### Views based on Mustache templates

You create views using [Mustache](http://mustache.github.com/).

Here is an example of a header template that displays the above data.

```html
<!DOCTYPE html>
  <html lang="{{currentLang}}">
  <head>
    <title>{{{blogTitle}}}</title>
    <meta charset="{{blogCharset}}">
    <link media="all" rel="stylesheet" href="{{publicDir}}/css/main.css">
    <script src="{{publicDir}}/js/main.js"></script>
```

### Controllers to pull everything together

A controller talks to the data helpers, loads the mustache template and can then be called from your WordPress template files.

Here's a sample function from a controller that loads all posts, limited by 'posts per page', into the home template.

```php

/**
 * home.php
 */
public function getHome() {
	$args = [
		'posts' => Post::getAll(Option::get('posts_per_page'))
	];
	
	return $this->renderPage('home', $args);
}
```

## Creating basic controllers and views

All controllers are inside app/controllers.

* AjaxController: Controller for ajax petitions.
* BackendController: Controller  for backend stuff.
* HomeController: Controller for all files from WP:
	- author.php -> getAuthor() -> render the base/author.mustache template
	- archive.php -> getArchive() -> render the base/search.mustache template
	- category.php -> getCategory() -> render the base/search.mustache template
	- home.php -> getHome() -> render the base/home.mustache template
	- index.php -> getIndex() -> render the base/error_404.mustache template
	- search.php -> getSearch() -> render the base/search.mustache template
	- single.php -> getSingle($type = 'post') -> render the base/[post|page].mustache template
	- tag.php -> getTag() -> render the base/search.mustache template
	- 404.php -> get404() -> render the base/error_404.mustache template

### Calling a controller from a WordPress template page.
All this files are already created by [Knob-base](https://github.com/Chemaclass/knob-base/). 
So you just need to override the function in your HomeController, or extend by ´´´use Knob\Controllers\HomeController´´´

[Create a template for WordPress](http://codex.wordpress.org/Template_Hierarchy), 
for example single.php which is used when a Post is loaded.

```php
use Controllers\HomeController;

$controller = new HomeController();
$controller->getSingle('post');
```

### Creating a controller

Controllers should extend BaseController. This then provides access to the templating functions.

```php
namespace Controllers;

use Models\Post;

class HomeController extends BaseController {

	/**
	 * single.php
	 */
	public function getSingle($type = 'post') {
		if (!have_posts()) {
			return $this->get404();
		}
		
		the_post();
		$post = Post::find(get_the_ID());
		
		return $this->renderPage($type, [ 
			$type => $post 
		]);
	}
}
```

### Creating mustache templates

Create your mustache template within app/templates.

[The Mustache manual](http://mustache.github.com/mustache.5.html) will be your guide.

Here is an example template showing a post:

```html
{{< base/layout }}

	{{$ content }}	

		<div id="post">
			
			<h1 class="title">{{ post.getTitle }}</h1>
			<div class="content">
				{{{ post.getContent }}}
			</div>
			
		</div>

	{{/ content }}

{{/ base/layout }}
```

### Loading templates with automatically included header and footer feature

The most important template is:

* base/layout.mustache [as Decorator pattern]

```html
<!DOCTYPE html>
<html lang="{{currentLang}}">
	<head>
		<title>{{{blogTitle}}}</title>
		<meta charset="{{blogCharset}}">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="{{blogAuthor}}">
		<meta name="description" content="{{blogDescription}}">		
		<script src="{{publicDir}}/js/main.js"></script>
		<!-- more sentences -->
		{{{ wp_head }}}
	</head>
	
	<body>		
		<header id="header">	
			<a class="col-xs-12" href="{{homeUrl}}">{{blogTitle}}</a>
			<span class="col-xs-12">{{blogDescription}}</span>
		</header>

		<div id="content">
			{{$ content }}
				This could be your content section
			{{/content }}
		</div>

		{{$ js }} {{/ js }}
		
		<div id="footer">
			{{{ wp_footer }}}			
		</div>
	</body>
</html>
```

And then we have `home.mustache`:

```html
{{< base/layout }}

	{{$ content }}
	
		<div id="home">
			<section class="all-posts">
				{{# posts }}
					<article class="post">
						<span class="post-time">{{getDate | date.string}}</span>
						<a class="permalink" href="{{getPermalink}}">{{getTitle}}</a>
						<span class="excerpt">{{{ getExcerpt }}}</span>
					</article>
				{{/ posts }}
			</section>
		</div>
		
	{{/ content }}
	
{{/ base/layout }}
```

# Before start... you will need this! #

### Install ruby and compass ###
* sudo apt-get install ruby
* sudo gem update --system
* sudo apt-get install ruby1.9.1-dev
* sudo gem install compass
* sudo gem install rake

### Then, you will be able to compile the scss in the directory of your project: ###
* /knob-mvc $> rake watch_scss

### You'll need a PHP graphics library to be able to use the image editor: ###
* apt-get install php5-imagick php5-gd
* service apache2 reload 

### To configure:
* Go to your panel admin.
* Click into Settings > Permalinks.
* I recommend select "Common Settings" using "Post name".
* Copy the new ```.htaccess``` content file and update it. That file will be in your worpress root directory.
* Go into your Appearance > Themes.
* Select your Theme.
* Enjoy!

## Any PR are welcome!
* Please, feel free to fork this project and commit your Pull Request. Here or into the [Kernel-base](https://github.com/Chemaclass/knob-base/).