# README #

### What's this repository? ###

* Knob MVC
* This is a PHP MVC Framework for creating Wordpress templates easier and with more fun than ever before.
* Version: 0.6
* Author José María Valera Reales


### Views based on Mustache templates

You create views using [Mustache](http://mustache.github.com/).

Here is an example of a header template that displays the above data.

```html
<!DOCTYPE html>
  <html lang="{{currentLang}}">
  <head>
    <title>{{{blogTitle}}}</title>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="{{publicDir}}/img/favicon.ico">    
    <link media="all" rel="stylesheet" href="{{publicDir}}/css/main.css">
    <script src="{{publicDir}}/js/main.js"></script>
```

### Controllers to pull everything together

A controller talks to the data helpers, loads the mustache template and can then be called from your WordPress template files.

Here's a sample function from a controller that loads the header data into the header template.

```php
/**
 * home.php
 */
public function getHome() {
	$args = [
		'posts' => Post::getAll(get_option('posts_per_page'))
	];
	return $this->renderPage('home', $args);
}
```

## Creating basic controllers and views

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
		if (have_posts()) {
			the_post();
			$post = Post::find(get_the_ID());
		}
		if (!isset($post)) {
			return $this->getError();
		}
		return $this->renderPage($type, [ 
			$type => $post 
		]);
	}
}
```

You could group functions in a single controller, or create separate controllers for each template type. We favour the later.

Place controllers inside mvc/controllers.

### Calling a controller from a WordPress template page

[Create a template for WordPress](http://codex.wordpress.org/Template_Hierarchy), for example single.php which is used when a Post is loaded.

```php
use Controllers\HomeController;

$controller = new HomeController();
$controller->getSingle('post');
```    

### Creating mustache templates

Create your mustache template within mvc/templates.

[The Mustache manual](http://mustache.github.com/mustache.5.html) will be your guide.

Here is an example template showing a post:

```html
{{< base }}

	{{$ content }}	

		<div id="post" class="col-xs-12">
			
			<h1 class="title">{{ post.getTitle }}</h1>
			
			{{{ post.getContent }}}
			
		</div>

	{{/ content }}

{{/ base }}
```

### Loading a template from within a controller

To load the above template, you can use the built-in render function from within your controller.

```php	
$args = [ 
	'error' => [ 
		'code' => 404,
		'message' => 'Not found' 
	] 
];
return $this->renderPage('error', $args);
```


### Loading templates with automatically included Header and footer feature

The 3 first most important templates are:

* head.mustache
* base.mustache
* footer.mustache 

`head` should include `<!DOCTYPE html>` until the first `<body class="...">` tag.

`footer` should include just the footer content and `</body></html>`

* We use the `base.mustache` as Decorator pattern:

```html
<header id="top" class="container">	

	<div id="blog-title" class="row">
		<a class="col-xs-12" href="{{homeUrl}}">{{blogTitle}}</a>
		<span class="col-xs-12">{{blogDescription}}</span>
	</div>
	
</header>

<section id="page" class="container">	

    <article id="content" class="row">
    	{{$ content }} 
    		You don't have to see this text, cause you can override this 
    		tag's "content" in your child template.
    	{{/content }}
	</article>
	
</section>

{{$ js }} {{/ js }}
```
And then we have `home.mustache`:

```html
{{< base }}	

	{{$ content }}

		<div id="home" class="col-xs-12">

			<section class="all-posts">
				{{# posts }}
					{{> home/_post}}
				{{/ posts }}		
			</section>

		</div>

	{{/ content }}

{{/ base }}
```

And we have the partial `home/_post.mustache`:

```html
<article class="col-xs-12 post">

	<span class="col-xs-12">
		<span class="post-time">{{getDate | date.string}}</span>
	</span>

	<span class="col-xs-12">
		<a href="{{getPermalink}}">{{getTitle}}</a>
	</span>

	<span class="col-xs-12">
		{{{ getExcerpt }}}
	</span>

</article>
```

# Before the start... you'll need! #

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


## Way to work (Recommended) ##

### Local branch ###
The best way to work with a team is working in one "local branch", and then when we finished
our task to do a `merge --squash` on the "dev branch" for at the end have just one commit with all
changes together. Then once tested in dev we can do a merge with master for to push all on production.

