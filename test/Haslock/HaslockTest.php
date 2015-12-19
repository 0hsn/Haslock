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

$_SERVER['REQUEST_URI'] = isset($argv[1]) ? $argv[1] : "";

require_once(dirname(dirname(__DIR__))."/src/Haslock/Haslock.php");

use Haslock\Haslock;

Haslock::config([
   'DefaultFunction' => 'DefaultFunction',
   'ErrorView' => 'SomeApp\Web\Frontend\Page@ErrorAction'
]);

Haslock::forge([
   '/' => 'SomeApp\Web\Frontend\Page@StaticPageAction',
   '/some' => 'SomeApp\Web\Frontend\Page@StaticPageAction',
   '/some/:any' => 'SomeApp\Web\Frontend\Page@StaticPageAction',
   '/payment' => 'SomeApp\Web\Frontend\Payment',
]);
