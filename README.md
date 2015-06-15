# README #

### What's this repository? ###

* Knob-MVC
* 
* This is a PHP Framework for to create templates for Wordpress on more easy and funny than before.
* Version: 0.1
* Author José María Valera Reales


* Using namespaces and composer for PHPMailer y Mustache by default.

### Before to start... you need ###

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

