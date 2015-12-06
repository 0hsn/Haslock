<?php
namespace Hasanlock\Haslock\View;
/**
 * Default Error view controller
 *
 * Basically this controller will load up default view for different kind of error
 */

use \Exception;

class Error extends Base {
    protected $errorTplDir = "/static/error/";
    protected $errorTplExt = ".html";
    
    public function displayAction($ex=null) {
        if($ex) {
            include_once($this->getStaticFilePath().$this->errorTplDir.$ex->getCode().$this->errorTplExt);
        }
    }
}