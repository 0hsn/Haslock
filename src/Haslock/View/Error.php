<?php
/**
 * Default Error view controller
 *
 * Basically this controller will load up default view for different kind of error
 */

use \Exception;

class Error {
    public function displayAction($ex=null) {
        if($ex) {
            echo $ex->getFile();
        }
    }
}