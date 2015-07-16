<?php

/**
 * Swifost
 *
 * Lightweight and awesome CMS without database, using the magic of the files.
 *
 *  @package     Swifost
 *  @version     1.0.0
 *  @link        http://swifost.com
 *  @author      Jose Pino, @jofpin | jofpin@gmail.com
 *  @copyright   2015 Swifost / Content Management System
 *
 * This file is the captain of Swifost.
 * For full copyright information and documentation of this visit: http://swifost.com
 */

class Swifost {
    /**
     * Global data and the cms name swifost!
     * 
     * @example Swifost::SOFTWARE;
     * @var string
     */
    const SOFTWARE = "Swifost";

    /**
     * The developer of Swifost! :)
     * 
     * @example Swifost::AUTHOR;
     * @var string
     */
    const AUTHOR = "Jose Pino";

    /**
     * The description of Swifost! :)
     * 
     * @example Swifost::DESCRIPTION;
     * @var string
     */
    const DESCRIPTION = "Lightweight and awesome CMS without database.";

    /**
     * The version of Swifost
     *
     * @example Swifost::VERSION;
     * @var string
     */
    const VERSION = "1.0.0";

    /**
     * The separator of Swifost
     *
     * @var string
     */
    const SEPARATE = "--separator--";

    /**
     * Config base array. 
     *
     * @var array
     */
    public static $config;

    /**
     * Name for security token - CSRF
     *
     * @var string
     */
    protected static $csrf_token = "security_token";

    /**
     * Page headers
     *
     * @var array
     */
    private $dataPage = array(
        "title" => "Title",
        "description" => "Description",
        "keywords" => "Keywords",
        "author" => "Author",
        "date" => "Date",
        "robots" => "Robots",
        "category" => "Category",
        "tags" => "Tags",
        "image" => "Image",
        "template" => "Template"
    );

    /**
     * Actions
     *
     * @var array
     */
    private static $actions = array();

    /**
     * Filters
     *
     * @var array
     */
    private static $filters = array();

    /**
     * Plugins global 
     *
     * @var array
     */
    private static $plugins = array();


    /**
     * push method making method chaining possible right off the bat.
     *
     * @example Swifost::push();
     *
     * @access  public
     */
    public static function push() {
        return new static();
    }

    /**
     * Running Swifost cms
     *
     * @example Swifost::push()->run($road);
     *
     * @param string $road config > road
     * @access  public
     */
    public function run($road) {

        // Load config file
        $this->loaderConfig($road);

        // Default timezone
        @ini_set("date.timezone", static::$config["timezone"]);
        if (function_exists("date_default_timezone_set")) {
            date_default_timezone_set(static::$config["timezone"]);
        } else {
            putenv("TZ=".static::$config["timezone"]);
        }

        /**
         * Filter URL to prevent Cross-site scripting (XSS)
         */
        $this->runFilterUrl();

        /**
         * Send default setHeader and set internal encoding
         */
        static::setHeader("Content-Type: text/html; charset=".static::$config["charset"]);
        function_exists("mb_language") and mb_language("uni");
        function_exists("mb_regex_encoding") and mb_regex_encoding(static::$config["charset"]);
        function_exists("mb_internal_encoding") and mb_internal_encoding(static::$config["charset"]);

        /**
         * Gets the current configuration setting of magic_quotes_gpc
         * and kill magic quotes
         * Reference: http://stackoverflow.com/questions/30346277/securing-mysql-database-query-against-sql-injections/30347087#30347087
         */
        if (get_magic_quotes_gpc()) {
            function stripSlashesGPC(&$value) { 
                $value = stripslashes($value); 
            }
            array_walk_recursive($_GET, "stripSlashesGPC");
            array_walk_recursive($_POST, "stripSlashesGPC");
            array_walk_recursive($_REQUEST, "stripslashesGPC");
            array_walk_recursive($_COOKIE, "stripSlashesGPC");
        }

        // Start session
        !session_id() and @session_start();        

        // Load all plugins of Swifost
        $this->loaderPlugins();
        $this->runDeed("plugins_loaded");

        // Get current page for requested url
        $page = $this->getPage($this->getUrl());

        // Load page title, description & keywords
        empty($page["title"]) and $page["title"] = static::$config["title"];
        empty($page["description"]) and $page["description"] = static::$config["description"];
        empty($page["keywords"]) and $page["keywords"] = static::$config["keywords"];

        $page = $page;
        $config = self::$config;

        // Loading template
        $this->runDeed("before_render");
        require TEMPLATES_PATH ."/". $config["template"] . "/". ($template = !empty($page["template"]) ? $page["template"] : "index") .".html";
        $this->runDeed("after_render");
    }

    /**
    * Runnig aplication Swifost (config, install sucess)
    *
    * @example Swifost::push()->ready("config.php", install.php", "index.php", "install", "ready");
    *
    * @access  public
    * @param  string $fileConfig, $fileInstall, $redirect, $param and $result
    *
    */
    public function ready($fileConfig, $fileInstall, $redirect, $param, $result) {
        if (file_exists($fileInstall)) {
            if (isset($_GET[$param]) && $_GET[$param] == $result) {
                // Try to delete install file if not DELETE MANUALLY !!!
                @unlink($fileInstall);

                // Redirect to main page
                Swifost::redirect($redirect);

            } else {
                include $fileInstall;
            }
        } else {
        // Run Swifost Application
            self::push()->run($fileConfig);
        }
    }
    
    /**
     * Insert text or html!
     * Pull "echo or print" in the trash!
     * Add a (go) into your life with php :D
     *
     * @example Swifost::go("content here");
     *
     * @access  public
     * @param  string $string
     * @return string
     */
    public static function go($string) {
        return print($string);
    }

    /**
     * Customization and replacement of header() by setHeader()
     * For the use and support of the redirect() function
     *
     * @example setHeader("X-Frame-Options: DENY");
     * @example setHeader("Location: http://example.com");
     *
     * @access  public
     * @param mixed $hds string or array with headers to send.
     * @param mixed $hds is headers
     */
    public static function setHeader($hds) {
        foreach ((array) $hds as $header) {
            // Run Header
            header((string) $header);
        }
    }

    /**
     * This is simple happy code.
     * This gives you a push requests sexy GET and POST.
     * support for functions get() & post()
     * Returns value from core using dot notation.
     * If the key does not exist in the array, the default value will be returned instead. :D
     *
     *
     * @param  array  $array to extract from (Array)
     * @param  string $path  path (Array)
     * @param  mixed  $core Default (Array)
     */
    private static function request($array, $path, $core = null) {
        // segments > segms Get segments from path
        $segms = explode(".", $path);
        foreach ($segms as $segm) { 
            // Checking
            if ( ! is_array($array) || !isset($array[$segm])) {
                return $core;
            }
            $array = $array[$segm];
        }

        return $array;
    }

    /**
     * POST
     *
     * @example $var = Swifost::post("test");
     *
     * @param string $param (parameter) > Key
     */
    public static function post($param) {
        return static::request($_POST, $param);
    }

    /**
     * GET
     *
     * @example $var = Swifost::get("test");
     *
     * @param string $param (parameter) > Key
     */
    public static function get($param) {
        return static::request($_GET, $param);
    }

    /**
     * Set one or multiple headers.
     * Redirects page sexy to a page specified by the $url argument.
     *
     * @example Swifost::redirect("http://example.com/");
     * @example Swifost::redirect("test");
     *
     * @param string  $url_value The URL
     * @param boolean $new_tab if true, open the url in a new tab <esperimental option> 
     * @param integer $status Status Code
     * @param integer $delay  Delay
     */
    public function redirect($url_value, $new_tab = FALSE, $status = 302, $delay = null) {
        // Redefine Vars
        $status = (int) $status;
        $url_value = (string) $url_value;

        // Values of status code
        $msg_value = array();
        $msg_value[301] = "301 Moved Permanently";
        $msg_value[302] = "302 Found";

        // Send headers game
        if (headers_sent()) {
            if ($new_tab == TRUE) {
                static::go("<script>window.open('" . $url_value . "', '_blank');</script>\n");
            } else {
                static::go("<script>document.location.href='" . $url_value . "';</script>\n");
            }
            
        } else {
            // Redirection header
            static::setHeader("HTTP/1.1 " . $status . " " . static::get($msg_value, $status, 302));
            // Delay execution
            if ($delay !== null) sleep((int) $delay);
            // Redirect ok
            static::setHeader("Location: $url_value");
        }
    }

    /**
     * Get Url
     *
     * @example $var = Swifost::push()->getUrl();
     *
     * @access  public
     * @return string
     */
    public function getUrl() {
        // Get request url and script url
        $url = "";
        $script  = (isset($_SERVER["PHP_SELF"])) ? $_SERVER["PHP_SELF"] : "";
        $request = (isset($_SERVER["REQUEST_URI"])) ? $_SERVER["REQUEST_URI"] : "";

        // Get our url path and trim the / of the left and the right
        if ($request != $script) $url = trim(preg_replace("/". str_replace("/", "\/", str_replace("index.php", "", $script)) ."/", "", $request, 1), "/");
        $url = preg_replace("/\?.*/", "", $url); // Strip query string

        return $url;
    }

    /**
     * Get Uri Segments (Url segs)
     *
     * @example $var = Swifost::push()->getUriSegs();
     *
     * @access  public
     * @return array
     */
    public function getUriSegs() {
        return explode("/", $this->getUrl());
    }

    /**
     * Get Uri Segment
     *
     * @example $var = Swifost::push()->segetUrl(1);
     *
     * @access  public
     * @return string
     */
    public function segetUrl($segment)  {
        $segments = $this->getUriSegs();
        return isset($segments[$segment]) ? $segments[$segment] : null;
    }

    /**
     * Create safe url, to sanitize (Cross-site scripting <XSS>). Secure secure..! <3
     *
     * @example $var = Swifost::push()->filterUrl($url);
     *
     * @return string
     */
    public function filterUrl($string) {
        $string = str_replace("/","", $string);
        $string = str_replace("\\","", $string);
        $string = preg_replace("/[^-a-zA-Z0-9_]/", "", $string);
        $string = trim($string);
        $string = rawurldecode($string);
        $string = str_replace(array('--','&quot;','!','@','#','$','%','^','*','(',')','+','{','}','|',':','"','<','>',
                                  '[',']','\\',';',"'",',','*','+','~','`','laquo','raquo',']>','&#8216;','&#8217;','&#8220;','&#8221;','&#8211;','&#8212;'),
                            array('-','-','','','','','','','','','','','','','','','','','','','','','','','','','','',''),
                            $string);
        $string = str_replace("--", "-", $string);
        $string = rtrim($string, "-");
        $string = str_replace("..", "", $string);
        $string = str_replace("//", "", $string);
        $string = preg_replace("/^\//", "", $string);
        $string = preg_replace("/^\./", "", $string);

        // Return string
        return $string;
     }

    /**
     * Sanitize URL to prevent XSS - Cross-site scripting
     *
     * @example Swifost::push()->runFilterUrl();
     *
     * @access  public
     * @return void
     */
    public function runFilterUrl() {
        $_GET = array_map(array($this, "filterUrl"), $_GET);
    }


    /**
     * Get - page
     *
     * @example $var = Swifost::push()->getPage("about");
     *
     * @access  public
     * @param  string $url Url
     * @return array
     */
    public function getPage($url) {
        // Page headers
        $dataPage = $this->dataPage;

        // Get the file path
        if($url) $file = CONTENT_PATH . "/" . $url; else $file = CONTENT_PATH . "/" ."index";

        // Load the file
        if(is_dir($file)) $file = CONTENT_PATH . "/" . $url ."/index.md"; else $file .= ".md";

        if (file_exists($file)) {
            $content = file_get_contents($file);
        } else {
            $content = file_get_contents(CONTENT_PATH . "/" . "404.md");
            static::setHeader($_SERVER["SERVER_PROTOCOL"]." 404 Not Found");
        }

        $_dataPage = explode(self::SEPARATE, $content);

        foreach ($dataPage as $field => $regex) {
            if (preg_match('/^[ \t\/*#@]*' . preg_quote($regex, "/") . ":(.*)$/mi", $_dataPage[0], $match) && $match[1]) {
                $page[$field] = trim($match[1]);
            } else {
                $page[$field] = "";
            }            
        }

        $url = str_replace(CONTENT_PATH, static::$config["url"], $file);
        $url = str_replace("index.md", "", $url);
        $url = str_replace(".md", "", $url);
        $url = str_replace("\\", "/", $url);
        $url = rtrim($url, "/");
        $pages["url"] = $url;

        $_content = $this->parser($content);        
        if(is_array($_content)) {
            $page["content-summary"] = $_content["content-summary"];
            $page["content"] = $_content["content-all"];
        } else {
            $page["content-summary"] = $_content;
            $page["content"] = $_content;
        }

        $page["slug"] = basename($file, ".md");

        return $page;
    }
    
   /**
     * Get pages
     *
     * @example $var = Swifost::push()->getPages(CONTENT_PATH . "/profile/");
     *
     * @access  public
     * @param  string  $url         > Url
     * @param  int     $limit       > Limit of pages
     * @param  array   $ignore      > Pages to ignore
     * @param  string  $byOrder     > Order by
     * @param  string  $typeOrder   > Order type
     * @return array
     */
    public function getPages($url, $byOrder = "date", $typeOrder = "DESC", $ignore = array("404"), $limit = null) {

        // Page headers
        $dataPage = $this->dataPage;

        $pages = $this->getFiles($url);

        foreach($pages as $key => $page) {
            
            if (!in_array(basename($page, ".md"), $ignore)) {            

                $content = file_get_contents($page);

                $_dataPage = explode(self::SEPARATE,  $content);

                foreach ($dataPage as $field => $regex) {
                    if (preg_match("/^[ \t\/*#@]*" . preg_quote($regex, "/") . ":(.*)$/mi", $_dataPage[0], $match) && $match[1]) {
                        $_pages[$key][ $field ] = trim($match[1]);
                    } else {
                        $_pages[$key][$field] = "";
                    }
                }

                $url = str_replace(CONTENT_PATH, self::$config["url"], $page);
                $url = str_replace("index.md", "", $url);
                $url = str_replace(".md", "", $url);
                $url = str_replace("\\", "/", $url);
                $url = rtrim($url, "/");
                $_pages[$key]["url"] = $url;

                $_content = $this->parser($content);        
                if(is_array($_content)) {
                    $_pages[$key]["content-summary"] = $_content["content-summary"];
                    $_pages[$key]["content"] = $_content["content-all"];
                } else {
                    $_pages[$key]["content-summary"] = $_content;
                    $_pages[$key]["content"] = $_content;
                }

                $_pages[$key]["slug"] = basename($page, ".md");

            }
        }

        $_pages = $this->subvalSort($_pages, $byOrder, $typeOrder);

        if($limit != null) $_pages = array_slice($_pages, null, $limit);

        //  Return pages
        return $_pages;
    }

    /**
     * Get list of files in directory recursive
     *
     * @example $var = Swifost::push()->getFiles("directory");
     * @example $var = Swifost::push()->getFiles("directory", "txt");
     * @example $var = Swifost::push()->getFiles("directory", array("txt", "log"));
     *
     * @access  public
     * @param  string $folder Folder
     * @param  mixed $type Files types
     * @return array
     */
    public static function getFiles($folder, $type = null) {
        $data = array();
        if (is_dir($folder)) {
            $iterator = new RecursiveDirectoryIterator($folder);
            foreach (new RecursiveIteratorIterator($iterator) as $file) {
                if ($type !== null) {
                    if (is_array($type)) {
                        $file_ext = substr(strrchr($file->getFilename(), '.'), 1);
                        if (in_array($file_ext, $type)) {
                            if (strpos($file->getFilename(), $file_ext, 1)) {
                                $data[] = $file->getPathName();
                            }
                        }
                    } else {
                        if (strpos($file->getFilename(), $type, 1)) {
                            $data[] = $file->getPathName();
                        }
                    }
                } else {
                    if ($file->getFilename() !== "." && $file->getFilename() !== "..") $data[] = $file->getPathName();
                }
            }

            return $data;
        } else {
            return false;
        }
    }

    /**
     * obEval
     */
    protected static function obEval($value) {
        ob_start();
        eval($value[1]);
        $value = ob_get_contents();
        ob_end_clean();

        return $value;
    }
    
    /**
     * evalPHP
     */
    protected static function evalPHP($string) { 
        return preg_replace_callback("/\{php\}(.*?)\{\/php\}/ms","self::obEval", $string); 
    }

    /**
     * Content Parser
     *
     * @param  string $content Content to parse
     * @return string $content Formatted content
     */
    protected function parser($content) {       
        // Parse Content after Headers
        $_content = "";
        $i = 0;
        foreach (explode(self::SEPARATE, $content) as $c) {
            ($i++!=0) and $_content .= $c;
        }

        $content = $_content;

        // Parse --url-- 
        $content = str_replace("--url--", static::$config["url"], $_content);

        // Parse --version--
        $content = str_replace("--version--", self::VERSION, $content);

        // Parse --software--
        $content = str_replace("--software--", self::SOFTWARE, $content);

        // Parse --author--
        $content = str_replace("--author--", self::AUTHOR, $content);

        // Parse --summary--
        $summary = strpos($content, "--summary--");
        if ($summary === false) {
            $content = $this->applyFilter("content", $content);
        } else {
            $content = explode("--summary--", $content);
            $content["content-summary"] = $this->applyFilter("content", $content[0]);
            $content["content-all"]  = $this->applyFilter("content", $content[0].$content[1]);                    
        }

        $content = self::evalPHP($content);

        // Return content
        return $content;
    }

    /**
     * Load Config
     */
    protected function loaderConfig($pathFile) {
        if (file_exists($pathFile)) static::$config = require $pathFile; 
        else 
            die("Hey, where is the configuration file ?");
    }

    /**
     * Load Plugins
     */
    protected function loaderPlugins() {
        foreach (static::$config["plugins"] as $plugin) {
            include_once PLUGINS_PATH ."/". $plugin."/".$plugin.".php";
        }
    }

    /**
     *  Hooks a function on to a specific action.
     *
     *      // Hooks a function "newLink" on to a "footer" action.
     *      Swifost::push()->createDeed('footer', "href", 10);
     *
     *      function href() {
     *          echo '<a href="#">My link</a>';
     *      }
     *
     * @access  public
     * @param string  $action_name    Action name
     * @param mixed   $added_function Added function
     * @param integer $priority       Priority. Default is 10
     * @param array   $args           Arguments
     */
    public function createDeed($action_name, $added_function, $priority = 10, array $args = null) {
        // Hooks a function on to a specific action.
        static::$actions[] = array(
            "action_name" => (string) $action_name,
            "function" => $added_function,
            "priority" => (int) $priority,
            "args" => $args
        );
    }

    /**
     * Run functions hooked on a specific action hook.
     *
     *      // Run functions hooked on a "footer" action hook.
     * @example Swifost::push()->runDeed('footer');
     *
     * @access  public
     * @param  string  $action_name Action name
     * @param  array   $args        Arguments
     * @param  boolean $return      Return data or not. Default is false
     * @return mixed
     */
    public function runDeed($action_name, $args = array(), $return = false) {
        // Redefine arguments
        $action_name = (string) $action_name;
        $return = (bool) $return;

        // Run action
        if (count(static::$actions) > 0) {

            // Sort actions by priority
            $actions = $this->subvalSort(static::$actions, "priority");

            // Loop through $actions array
            foreach ($actions as $action) {

                // Execute specific action
                if ($action["action_name"] == $action_name) {

                    // isset arguments ?
                    if (isset($args)) {

                        // Return or Render specific action results ?
                        if ($return) {
                            return call_user_func_array($action["function"], $args);
                        } else {
                            call_user_func_array($action["function"], $args);
                        }

                    } else {

                        if ($return) {
                            return call_user_func_array($action["function"], $action["args"]);
                        } else {
                            call_user_func_array($action["function"], $action["args"]);
                        }

                    }

                }

            }

        }

    }

    /**
     * Apply filters
     *
     * @example Swifost::push()->applyFilter("content", $content);
     *
     * @access  public
     * @param  string $nameFilter The name of the filter hook.
     * @param  mixed  $value The value on which the filters hooked.
     * @return mixed
     */
    public function applyFilter($nameFilter, $value) {
        // Redefine arguments
        $nameFilter = (string) $nameFilter;

        $args = array_slice(func_get_args(), 2);

        if (!isset(static::$filters[$nameFilter])) {
            return $value;
        }

        foreach (static::$filters[$nameFilter] as $priority => $functions) {
            if ( ! is_null($functions)) {
                foreach ($functions as $function) {
                    $allArgs = array_merge(array($value), $args);
                    $functionName = $function["function"];
                    $acceptArgs = $function["acceptArgs"];
                    if ($acceptArgs == 1) {
                        $theArgs = array($value);
                    } elseif ($acceptArgs > 1) {
                        $theArgs = array_slice($allArgs, 0, $acceptArgs);
                    } elseif ($acceptArgs == 0) {
                        $theArgs = null;
                    } else {
                        $theArgs = $allArgs;
                    }
                    $value = call_user_func_array($functionName, $theArgs);
                }
            }
        }

        // Return value
        return $value;
    }

    /**
     * Create filter
     *
     * @example Swifost::push()->createFilter("content", "replace");
     *
     * ------------------------------------------------
     *
     *  function replace($content) {
     *    return preg_replace(array('/\[b\](.*?)\[\/b\]/ms'), array('<strong>\1</strong>'), $content);
     *  }
     *
     * @access  public
     * @param  integer $priority  >  Function to add priority, default is 10.
     * @param  integer $acceptArgs >  The number of arguments the function accept default is 1.
     * @param  string  $nameFilter  > The name of the filter to hook the $createFunction to.
     * @param  string  $createFunction > The name of the function to be called when the filter is applied.
     * @return boolean
     */
    public function createFilter($nameFilter, $addFunc, $priority = 10, $acceptArgs = 1) {
        $nameFilter = (string) $nameFilter;
        $addFunc = $addFunc;
        $priority = (int) $priority;
        $acceptArgs = (int) $acceptArgs;

        // Thanks to WordPress
        if (isset(static::$filters[$nameFilter]["$priority"])) {
            foreach (static::$filters[$nameFilter]["$priority"] as $filter) {
                if ($filter["function"] == $addFunc) {
                    return true;
                }
            }
        }

        static::$filters[$nameFilter]["$priority"][] = array("function" => $addFunc, "acceptArgs" => $acceptArgs);

        // Sorting
        ksort(static::$filters[$nameFilter]["$priority"]);

        return true;
    }

    /**
     * HashToken Random generator for all.
     *
     * @example Swifost::hashToken();
     *
     * @access  public
     * @return string
     */
    public static function hashToken($numberValue) {
        // hash pernalize value $numberValue
        $string = substr(md5(uniqid(mt_rand(), true)), 0, $numberValue);

         // Return hash
        return $string;
    }

    /**
     * Generate and store a unique token which can be used to help prevent
     * Information: (http://wikipedia.org/wiki/Cross_Site_Request_Forgery).
     *
     *
     * You can insert this token into your forms as a hidden field:
     *
     * @example $token = Swifost::push()->createToken();
     * @example <input type="hidden" name="token" value="<?php Swifost::go(Swifost::push()->createToken()); ?>">
     * @example use formToken();
     *
     * This provides a basic, but effective, method of preventing CSRF attacks.
     *
     * @param  boolean $newToken force a new token to be generated?. Default is false
     * @return string
     */
    public function createToken($newToken = false) {
        // Get the current token
        if (isset($_SESSION[(string) self::$csrf_token])) $token = $_SESSION[(string) self::$csrf_token]; else $token = null;

        // Generate a new token (random)
        if ($newToken === true or ! $token) {

            // Generate a new unique token
            $token = static::hashToken(16);

            // Store the new token
            $_SESSION[(string) self::$csrf_token] = $token;
        }

        // Return
        return $token;
    }

    /**
     * Add input to implement CSRF
     *
     * Hidden field:
     *
     * @example $var = Swifost::push()->formToken();
     *
     * True is token automatic:
     * @example $var = Swifost::push()->formToken(true);
     *
     * False is token static:
     * @example $var = Swifost::push()->formToken(false);  
     *
     * This provides a basic, but effective, method of preventing CSRF attacks.
     *
     * @param  $stateToken
     * @return string
     */
    public function formToken($stateToken) {

        // Generate option Token
        $csrf_token = self::push()->createToken($stateToken);

        // CSRF name for default
        $csrf_name = static::$csrf_token;

        // CSRF input 
        $string = '<input type="hidden" name="'.$csrf_name.'" value="'.$csrf_token.'">';

        return $string;
    }

    /**
     * Check that the given token matches the currently stored security token.
     *
     *  @example Implementation
     * ------------------------------------------------- 
     *     if (Swifost::push()->checkToken($token)) {
     *         // So so so so...
     *     }
     * -------------------------------------------------
     *
     * @param  string  $token token to check
     * @return boolean
     */
    public function checkToken($token) {
        return self::push()->createToken() === $token;
    }


    /**
     * This is to prevent (null) character between ascii characters.
     * remove Invisible Characters!
     * Seriously this help too!
     * function support for xssClean();
     *
     * @param string $string
     */
    private static function cleanChar($string) {
        // no displayables
        $noyables = array('/\x0b/','/\x0c/','/%1[0-9a-f]/','/[\x00-\x08]/','/[\x0e-\x1f]/','/%0[0-8bcef]/');
        
        do {
            $clean = $string;
            $string = preg_replace($noyables, "", $string);
        } while ($clean != $string);
        return $string;
    }

    /**
     * Convert special characters to HTML entities.
     * Trick to avoid the injections and XSS execution (Sanitize).
     * This function is xssClean() support!
     *
     *
     * @param  boolean $encode Encode existing entities
     * @param  string  $value  String to convert
     * @return string
     */
    private static function cleanString($value, $encode = true) {
        return htmlentities((string) $value, ENT_QUOTES, "utf-8", $encode);
    }

    /**
     * Sanitize data to prevent (XSS) - Cross-site scripting!
     * XSS information: (http://wikipedia.org/wiki/Cross-site_scripting)
     *
     * @example Swifost::push()->xssClean("<script>alert(1337);</script>");
     *
     * @param string $string
     */
    public function xssClean($string) {
        // Convert html to plain text & Remove invisible characters
        $string = static::cleanChar($string);
        $string = static::cleanString($string); 

        return $string;
    }

    /**
     * Subval sort
     *
     * @example $var = Swifost::push()->subvalSort($old_array, "sort");
     *
     * @access  public
     * @param  array  $a Array
     * @param  string $subkey Key
     * @param  string $order  Order type DESC or ASC
     * @return array
     */
    public function subvalSort($a, $subkey, $order = null) {
        if (count($a) != 0 || (!empty($a))) {
            foreach ($a as $k => $v) $b[$k] = function_exists("mb_strtolower") ? mb_strtolower($v[$subkey]) : strtolower($v[$subkey]);
            if ($order == null || $order == "ASC") asort($b); else if ($order == "DESC") arsort($b);
            foreach ($b as $key => $val) $c[] = $a[$key];

            return $c;
        }
    }

}