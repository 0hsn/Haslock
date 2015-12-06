<?php
namespace Hasanlock\Haslock\View;
/**
 * Default Error view controller
 *
 * Basically this controller will load up default view for different kind of error
 */

use \Exception;

class Base {    
    public function getStaticFilePath() {
        return substr(__DIR__, 0, strpos(__DIR__, 'src') + 3);
    }
}