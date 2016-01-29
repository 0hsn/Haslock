<?php

namespace SomeApp\Web\Frontend;
    
class Page {
    public function StaticPageAction () {
        echo __METHOD__."\n";
    }
    public function ErrorAction ($ex) {
        echo __METHOD__."\n";
        var_dump($ex);
    }
    
}

class Payment {
    public function DefaultFunction () {
        echo __METHOD__."\n";        
    }
}

namespace Builder\Run;
/*
$a = [
   '/' => 'SomeApp\Web\Frontend\Page@StaticPageAction',
   '/some' => 'SomeApp\Web\Frontend\Page@StaticPageAction',
   '/some/:any' => 'SomeApp\Web\Frontend\Page@StaticPageAction',
   '/payment' => 'SomeApp\Web\Frontend\Payment',
];
*/
$a = [ 
    "www" => [
               '/' => 'SomeApp\Web\WWW\Page@StaticPageAction',
               '/some' => 'SomeApp\Web\WWW\Page@StaticPageAction',
               '/some/:any' => 'SomeApp\Web\WWW\Page@StaticPageAction',
               '/payment' => 'SomeApp\Web\WWW\Payment',
            ],
    "vv" => [
               '/' => 'SomeApp\Web\VV\Page@StaticPageAction',
               '/some' => 'SomeApp\Web\VV\Page@StaticPageAction',
               '/some/:any' => 'SomeApp\Web\VV\Page@StaticPageAction',
               '/payment' => 'SomeApp\Web\VV\Payment',
            ],
    "app" => [
               '/' => 'SomeApp\Web\APP\Page@StaticPageAction',
               '/some' => 'SomeApp\Web\APP\Page@StaticPageAction',
               '/some/:any' => 'SomeApp\Web\APP\Page@StaticPageAction',
               '/payment' => 'SomeApp\Web\APP\Payment',
            ],
    "*" => [
               '/' => 'SomeApp\Web\ANY\Page@StaticPageAction',
               '/some' => 'SomeApp\Web\ANY\Page@StaticPageAction',
               '/some/:any' => 'SomeApp\Web\ANY\Page@StaticPageAction',
               '/payment' => 'SomeApp\Web\ANY\Payment',
            ],
];

$_SERVER['REQUEST_URI'] = isset($argv[1]) ? $argv[1] : "";

require_once(dirname(dirname(__DIR__))."/src/Haslock/Haslock.php");

use Hasanlock\Haslock\Haslock;

Haslock::config([
    'DefaultFunction' => 'DefaultFunction',
    'ErrorView' => 'SomeApp\Web\Frontend\Page@ErrorAction',
    'SubdomainSupport' => 'On',
]);
/*
Haslock::forge([
   '/' => 'SomeApp\Web\Frontend\Page@StaticPageAction',
   '/some' => 'SomeApp\Web\Frontend\Page@StaticPageAction',
   '/some/:any' => 'SomeApp\Web\Frontend\Page@StaticPageAction',
   '/payment' => 'SomeApp\Web\Frontend\Payment',
]);
*/

Haslock::forge($a);