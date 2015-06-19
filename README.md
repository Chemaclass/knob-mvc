# README #

### What's this repository? ###

* Knob-MVC
* This is a PHP MVC Framework for to create templates for Wordpress on more easy and funny than before.
* Version: 0.1
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
public function getHome() {
	$args = [ 
		'project' => [ 
			'name' => 'Knob',
			'description' => 'Knob is one PHP MVC Framework for Templates for Wordpress' 
		],
		'author' => [ 
			'name' => 'José María Valera Reales' 
		],
		'posts' => self::getPosts() 
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
	 * post.php
	 */
    public function getPost() {
		if (have_posts()) {
			the_post();
			$post = Post::find(get_the_ID());
		}		
		if (!isset($post)) {
			return $this->getError();
		}		
		return $this->renderPage('post', [ 
			'post' => $post 
		]);
    }
}
```

You could group functions in a single controller, or create separate controllers for each template type. We favour the later.

Place controllers inside mvc/controllers.

### Calling a controller from a WordPress template page

[Create a template for WordPress](http://codex.wordpress.org/Template_Hierarchy), for example single.php which is used when one Post are loaded.

Require the controller, init it and call the relevant function.

```php
use Controllers\HomeController;

$controller = new HomeController();
$controller->getPost();
```    


### Creating mustache templates

Create your mustache template within mvc/templates.

[The Mustache manual](http://mustache.github.com/mustache.5.html) will be your guide.

Here is an example template showing a post:

```html
{{< base }}
	{{$ content }}	
		{{# post }}
		<div id="post" class="row">
			<div class="col-xs-12">
			
				<h1 class="title">{{{getTitle}}}</h1>
				
				{{{ getContent }}}
				
			</div>			
		</div>
		{{/ post }}
	{{/ content }}
{{/ base }}
```

### Loading a template from within a controller

To load the above template, you can use the built in function render from within your controller.

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

* templates/head.mustache
* templates/base.mustache
* templates/footer.mustache 

`head` include `<!DOCTYPE html>` until the first `<body class="...">` tag.

`footer` include just `</body></html>`

* We use the `templates/base.mustache` as Decorator pattern:

```html
<header id="top" class="container">	
	<div id="blog-title" class="row">
		<a class="col-xs-12" href="{{homeUrl}}">{{blogTitle}}</a>
		<span class="col-xs-12">{{blogDescription}}</span>
	</div>
</header>
<div id="page" class="container">	
    <div id="content" class="row">
    	{{$ content }} 
    		You don't have to see this text, cause you've to override this tags "content" in your Son template.
    	{{/content }}
	</div>
</div>
{{$ scripts }} {{/ scripts }}
```
And then we have `templates/home.mustache`:

```html
{{< base }}	
	{{$ content }}		
		<div id="home">		
			<div id="wellcome" class="row text-center">
				<span class="col-xs-12">
					{{#transu}}wellcome{{/transu}} to {{project.name}} by {{author.name}}
				</span>
			</div>			
			{{# posts }}
				{{> home/_post}}
			{{/ posts }}			
		</div>		
	{{/ content }}	
{{/ base }}
```

And we have the partial `templates/home/_post.mustache`:

```html
<div class="row">
	<div class="col-xs-12 post">	
		<span class="col-xs-12">
			<span class="post-time">{{getDate | date.string}}</span>
		</span>		
		<span class="col-xs-12">
			<a href="{{getPermalink}}">{{getTitle}}</a>
		</span>		
		<span class="col-xs-12">
			{{{ getExcerpt }}}
		</span>
	</div>	
</div>
```

# Before to start... you need #

* Isntall ruby y compass
	sudo apt-get install ruby
	sudo gem update --system
	sudo apt-get install ruby1.9.1-dev
	sudo gem install compass
	sudo gem install rake

* Then, you will be able to compile the scss on the directory of your project:
	/knob-mvc $> rake watch_scss

* You'll need one graphics library for to can use the image editor by php:
	apt-get install php5-imagick php5-gd
	service apache2 reload 


### Way to work ###

* Local branch
The best way to work with a team is working in one "local branch", and then when we finished
our task to do a merge --squash on the "dev branch" for at the end have just one commit with all
changes together. Then once tested in dev we can do a merge with master for to push all on production.

