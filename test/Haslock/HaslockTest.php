<?php
require_once(dirname(dirname(__DIR__))."/src/Haslock/Haslock.php");

use Akoriq\Haslock\Haslock;

Haslock::config( array(
   'SubDirPath' => '/gluephp'
));