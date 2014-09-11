Haslock
=======================

Introduction
-----------------------

Haslock is a resource based URL routing mechanizm for PHP 5.3. To tell you a one-liner: it is a yet another micro-framework with a very powerful feature.

WHY another framework
-----------------------
What exactly is the difference between Haslock and other micro-framework; is limited by your imagination. Basically, you can do anything now a day with [Composer](https://getcomposer.org "Composer"); if you know what you are doing. You don't actually need a full-stack framework like `Symfony` or `Laravel`, at least for most of us not working for any idea based start-up or corporate enterprise. How?! You can create heart of a framework with one micro-framework and composer packages of your choice, but told before: if you know what you are doing.

**reason #1**

The problem is: In most cases a micro-framework or URL dispatcher will get in your way!!! You will find one thing in common in all URL dispatcher, every one will check the existence and pre-load your library / controller / function before you dispatch them.

So, why this is wrong? because, we might not need / like all `require()` before even the actual URL is called! Think about `Codeigniter`. `Codeigniter` don't care if you have a Controller or not; because, if you don't, you'll get an error eventually. **`Codeigniter` don't require you to let the framework know, all routers you are going to use and all corresponding controller before you even use those, right.**

**reason #2**

Every framework want us not to learn something new!!!

*Codeigniter* don't want you to learn something new. Yet, you are learning how to do this and that in it. Like, how to load model, library, hooks. How to extend core. How to use activerecord. etc. etc.

*Symfony* don't want you to learn something new: just you need to know PHP OOP. Then the start with there [World Engine](http://dccinematicuniverse.wikia.com/wiki/World_Engine) called `Bundle`. Then they have separation of architectural logic, views, models disguised `Doctrine`. etc. etc.

*Laravel* also want us to learn nothing new; but there bla..bla..bla.

Have you ever wondered **If I had something for myself in php? If I had created a framework that have the heart to do all for me? That will grow with me? And, I understand every line for its code. Something that really don't want me to learn anything new, other than just PHP.**

Yes. **YOUR ANSWER IS `Haslock`**

Configuration
----------------

There is not much to configure for this version. Just you can configure what sub-directory contains your application.

	use \Akoriq\Haslock\Haslock;

	Haslock::config(array(
		'SubDirPath' => '/sub-dir-name'
	));

You can also pass configuration value in key => value format. Like,

	Haslock::config('SubDirPath', '/sub-dir-name');


`SubDirPath`, must not end with slash(`/`); must start with it. 

URL Declaration
-----------------
Just put the URLs and Controller classes in an array and pass it to `Haslock::forge()`. Call any `Haslock::config()` call before `Haslock::forge()`. 

		use \Akoriq\Haslock\Haslock; 
		$urls = array(
	     	'/' => 'show_page',
	     	'/page/(\d+)' => '\Hasan\Lock\Test\Test@printd',
	     	'/book/(\w+)' => '\Hasan\Lock\Test\Test:sprintd'
       	);
		Haslock::forge($urls);

URL formats and parameters
--------------

###Formats###

`Haslock` supports three format of function declarations as callback.

**Open or free function:** Functions those are declared anywhere with out class or namespace. Just `include()` or `require()` in the file you declare `Haslock::forge()`    

		use \Akoriq\Haslock\Haslock; 
		$urls = array(
	     	'/' => 'show_page'
       	);
		Haslock::forge($urls);		
		function show_page() {
			echo 'Hello World!!!';
		}

**Method of a class:** Class in a Namespace, that have method those you want to be your callback. Like,

		# in `index.php`
		use \Akoriq\Haslock\Haslock; 
		$urls = array(
	     	'/page/(\d+)' => '\Hasan\Lock\Test\Test@printd'
       	);
		Haslock::forge($urls);
		
Note the `@` sign. To Haslock, it means we are going to use a method of the given classname before `@` sign and method name after. In above example, `\Hasan\Lock\Test` is the namespace; `Test` is the class and `printd` is the method in `Test@printd`. So in the callback implementation will be:

		# in `Hasan\Lock\Test\Test.php`
		namespace Hasan\Lock\Test;
		class Test {
			public function printd($param_name) {
				echo $param_name;
			}
		}


**Static method of a class:** Class in a Namespace, that have static method those you want to be your callback. Like,

		# in `index.php`
		use \Akoriq\Haslock\Haslock; 
		$urls = array(
	     	'/book/(\w+)' => '\Hasan\Lock\Test\Test:sprintd'
       	);
		Haslock::forge($urls);
		
Note the `@` sign. To Haslock, it means we are going to use a method of the given classname before `@` sign and method name after. In above example, `\Hasan\Lock\Test` is the namespace; `Test` is the class and `sprintd` is the static method in `Test:sprintd`. So in the callback implementation will be:
		
		# in `Hasan\Lock\Test\Test.php`
		namespace Hasan\Lock\Test;
		class Test {
			public static function sprintd($param_name) {
				echo $param_name;
			}
		}


###Parameters###

We are watching URL parameters now we have been using on several example. It time we have some details on that too.

There are two kind of URL parameters we are able to use with `Haslock`.

**CodeIgniter like:** you can use `:any`, `:num` as codeigniter here. It also have a new predefined parameter format called `:str`.

`:any` for anything you want as path component. support a-z, A-Z, 0-9, and characters. Ex: `'/admin/:any/list/show'` as key of your URL array.

`:str` for only alphabetic characters you want as path component. support a-z, A-Z and dash `-` and underscore `_` characters. Ex: `'/admin/:str/list/show'` as key of your URL array.

`:num` for only numeric characters you want as path component. support 0-9 and dot `.` characters. Ex: `'/admin/:num/list/show'` as key of your URL array.

**RegEx:** Regular expression formats are also supported as path component. For example

    '/admin/list/(\d*)' => '\Some\NS\Admin\HomeController@VideoListAction'

**NB:** path component you given on URL will be matched and will be passed as corresponding function on given order. Function parameter names are not relevant here. Meaning, the numeric value `20` from `'/admin/list/20'` will be passed as `VideoListAction($id)` in `HomeController` class, from above example.


