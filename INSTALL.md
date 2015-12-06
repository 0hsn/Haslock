INSTALL Haslock
============================

process 1:
-------------
* Open your composer.json and paste in require section:
`
"require": {
	...
	"akoriq/haslock": "dev-master",
	...
}
`
* update your source repo by doing `composer install` or `composer update`

process 2:
-------------
* Download the Haslock repo as a zip 
* require() the src/Haslock/Haslock.php
* `use \Akoriq\Haslock\Haslock`
* Follow the README.md
