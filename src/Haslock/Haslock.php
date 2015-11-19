<?php
    namespace Akoriq\Haslock;
    
    /**
     * Haslock
     *
     * Provides an easy way to map URLs to classes. URLs can be literal
     * strings or regular expressions.
     *
     * When the URLs are processed:
     *      * delimiter (/) are automatically escaped: (\/)
     *      * The beginning and end are anchored (^ $)
     *      * An optional end slash is added (/?)
     *	    * The i option is added for case-insensitive searches
     *
     * Example:
     * Predefine constants:
     *   
     *   'ErrorView' => class with namepspace that handles Error; 
     *       default: 
     *   'Domain' => domain name with URL Scheme and without forward slash on tail
     *   'SubDirPath' => sub directoy on the path
     *
     *   ----------------------------------------------------------------------------------------------------
     *   say URL is http://someurl.net/sub-dir/some-given-url
     *   Domain     = http://someurl.net
     *   SubDirPath = /sub-dir
     *   Route      = /some-given-url
     *   ----------------------------------------------------------------------------------------------------
     *
     * 
     *
     *   Haslock::config(array(
     *       'Domain' => 'http://someurl.net',
     *       'SubDirPath' => '/sub-dir',
     *   ));
     *
     *   $urls = array(
     *    '/' => 'show_page',
     *    '/page/(\d+)' => '\Hasan\Lock\Test\Test@printd',
     *    '/book/(\w+)' => '\Hasan\Lock\Test\Test:sprintd'
     *   );
     *
     *   Haslock::before(function() {
     *     Haslock::store('name1', "sumon");
     *   });
     *
     *   Haslock::before('/page/', function() {
     *     Haslock::store('name2', "rumana");
     *   });
     *
     *
     *   Haslock::after('/page/', function() {
     *     Haslock::store('name3', "hasan");
     *     echo "Google";
     *   });
     *
     *   Haslock::forge($urls);
     *
     */

    
    class Haslock {
        static $curPath = '';
        static $config = array();
        static $di = null;
        static $before_callbacks = array();
        static $after_callbacks = array();

        static function config ($key=null, $val=null) {
            /** if key is null just return the full array */
			if(is_null($key)) {
				return self::$config;
			}
			
			/** if key is an array they merge with old values [support updated value for existing key ]*/
            if(is_array($key)) { 
				self::$config = array_merge(self::$config, $key);
				return true;
			}
			
			/** else if key is a string set an individual item */
            elseif(is_string($key) && !empty($key)) {
                if (!is_null($val)) { /** if value is not null then set the value */
                    self::$config[$key] = $val;
                    return true;
                }
                else {
					/** if value is null then return the value */
                    return self::$config[$key];
                }
            }
			else {
				/** if not supported key type found */
				return false; 
			}

        }
        /**
         * forge
         *
         * the main static function of the Haslock class.
         *
         * @param   array    	$urls  	    The regex-based url to class mapping
         * @throws  Exception               Thrown if corresponding class is not found
         * @throws  Exception               Thrown if no match is found
         * @throws  BadMethodCallException  Thrown if a corresponding GET,POST is not found
         *
         */
        static function forge ($urls) {
            try {

                /** replace subdirectory path */
                $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                if (isset(self::$config['SubDirPath'])) $path = str_replace(self::$config['SubDirPath'], '', $path);

                $found = false;
                krsort($urls);

                foreach ($urls as $regex => $class_path) {

                    /** support [:any, :str, :num] */
                    if(strpos($regex, ":any") !== false || strpos($regex, ":str") !== false || strpos($regex, ":num") !== false) {
                        $regex = str_replace(array(':any', ':str', ':num'), array('(.+)', '([a-zA-Z\-_.]+)', "([0-9.]+)"), $regex);
                    }

                    $regex = str_replace('/', '\/', $regex);
                    $regex = '^' . $regex . '\/?$';

                    if (preg_match("/$regex/i", $path, $matches)) {

                        $found = true;
                        self::$curPath = array_shift($matches);

                        if(strpos($class_path, ":") !== false) {
                            /* Static */
                            list($class, $static_action) = explode(":", $class_path, 2);
                            if( !method_exists($class,$static_action)) throw new \Exception("Static Method, $static_action, not supported.");

                            self::runFilter('before', self::$curPath);
                            call_user_func_array(array($class, $static_action), $matches);
                            self::runFilter('after', self::$curPath);
                        }
                        elseif(strpos($class_path, "@") !== false) {
                            list($class, $action) = explode("@", $class_path, 2);

                            $obj = new $class;
                            if( !method_exists($obj,$action)) throw new \Exception("Method, $action, not supported.");

                            self::runFilter('before', self::$curPath);
                            call_user_func_array(array($obj, $action), $matches);
                            self::runFilter('after', self::$curPath);
                        }
                        else {
                            self::runFilter('before', self::$curPath);
                            call_user_func_array($class_path, $matches);
                            self::runFilter('after', self::$curPath);
                        }
                    }
                }
                if (!$found) {
                    throw new \Exception("URL, $path, not found. ".__FILE__, 404);
                }
            }
            catch (\Exception $ex) {
                if(!isset($config['ErrorView']) ) {
                    $error = new View\Error;
                    $error->displayAction($ex);
                }

/*                new $config['ErrorView'];

                    print("Pleae")
                    print("errcode: ".$ex->getCode()." errmessage: ".$ex->getMessage()." errfile: ".$ex->getFile());
                    exit();
*/                
            }
        }

        /**
         * A utility for passing values between scopes. If $value
         * is passed, $name will be set to $value. If $value is not
         * passed, the value currently mapped against $name will be
         * returned instead (or null if nothing mapped).
         *
         * If $name is null all the store will be cleared.
         *
         * @param string $name name of variable to store.
         * @param mixed $value optional, value to store against $name
         *
         * @return mixed value mapped to $name
         */
        static function store($name = null, $value = null) {

            static $stack = array();

            if (is_string($name) && $value == null) {
                return isset($stack[$name]) ? $stack[$name] : null;
            }

            // if no $name clear $stack
            if (is_null($name)) {
                $stack = array();
                return;
            }

            // set new $value
            if (is_string($name)) {
                return ($stack[$name] = $value);
            }
        }

        /**
         * Function for mapping callbacks to be invoked before each request.
         * If called with two args, with first being regex, callback is only
         * invoked if the regex matches the request URI.
         *
         * @param callable|string $callback_or_regex callable or regex
         * @param callable $callback required if arg 1 is regex
         *
         * @return void
         */
        static function before() {

            $args = func_get_args();
            $func = array_pop($args);
            $rexp = array_pop($args);

            // mapping call
            if (is_callable($func)) {
                if ($rexp)
                    self::$before_callbacks[$rexp] = $func;
                else
                    self::$before_callbacks['*'][] = $func;
            }
        }

        /**
         * Function for mapping callbacks to be invoked after each request.
         * If called with two args, with first being regex, callback is only
         * invoked if the regex matches the request URI.
         *
         *
         * @return void
         */
        static function after() {

            $args = func_get_args();
            $func = array_pop($args);
            $rexp = array_pop($args);

            // mapping call
            if (is_callable($func)) {
                if ($rexp)
                    self::$after_callbacks[$rexp] = $func;
                else
                    self::$after_callbacks['*'][] = $func;
            }
        }

        private static function runFilter ($filter, $path) {
            switch ($filter) {
                case 'before':
                    // let's run regexp callbacks first
                    foreach (self::$before_callbacks as $rexp => $func) {
                        if ($rexp !='*' && preg_match($rexp, $path)) {
                            $func();
                        }
                    }
                    if(isset(self::$before_callbacks['*'])) {
                        // call generic callbacks
                        foreach (self::$before_callbacks['*'] as $func) {
                            $func();
                        }
                    }
                    break;
                case 'after':
                    // let's run regexp callbacks first
                    foreach (self::$after_callbacks as $rexp => $func) {
                        if ($rexp !='*' && preg_match($rexp, $path)) {
                            $func();
                        }
                    }
                    if(isset(self::$after_callbacks['*'])) {
                        // call generic callbacks
                        foreach (self::$after_callbacks['*'] as $func) {
                            $func();
                        }
                    }
                    break;
            };
        }
    }
