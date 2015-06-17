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
		] 
	];
	return $this->renderPage('home', $args);
}
```

## Creating basic controllers and views

### Creating a controller

Controllers should extend BaseController. This then provides access to the templating functions. 

```php
class PageController extends BaseController {

    public function getPage() { ... }

}
```

You could group functions in a single controller, or create separate controllers for each template type. We favour the later.

Place controllers inside mvc/controllers.

### Calling a controller from a WordPress template page

[Create a template for WordPress](http://codex.wordpress.org/Template_Hierarchy), for example page.php which is used when pages are loaded.

Require the controller, init it and call the relevant function.

```php
require_once(dirname(__FILE__).'/mvc/controllers/PageController.php');

$controller = new PageController();
$controller->getPage();
```    


### Creating mustache templates

Create your mustache template within mvc/templates.

[The Mustache manual](http://mustache.github.com/mustache.5.html) will be your guide.

Here is an example template showing a post:

```html
<div class="col-xs-12 titular text-center">
	<h1 class="title">Error {{error.code}}</h1>
	<p>{{error.message}}</p>
</div>
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

Create the following templates:

* head.mustache
* base.mustache
* footer.mustache 

`head` include '<!DOCTYPE html>' until the first 'body' tag.
`footer` include just '</body></html>'


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

