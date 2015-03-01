<?php
require_once(dirname(dirname(__DIR__))."/src/Haslock/Haslock.php");

use Akoriq\Haslock\Haslock;

Haslock::config([
   'SubDirPath' => '/some_dt',
   'SubDirPath2' => '/some_dt2',
]);
print_r(Haslock::config());

Haslock::config([
   'SubDirPath' => '/some_dt3',
   'SubDirPath_new' => '/some_key',
]);

print_r(Haslock::config());

var_dump( Haslock::config('',''));
print "\n";
var_dump( Haslock::config('','some_val'));
print "\n";
var_dump( Haslock::config('some_key',''));
print "\n";
print_r(Haslock::config());