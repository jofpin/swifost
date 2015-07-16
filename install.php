<?php

/**
 * Swifost installation
 *
 *  File for the installation of swifost cms.
 *
 *  Define config.php
 *
 *  @author Jose Pino, @jofpin | jofpin@gmail.com
 *  @copyright 2015 Swifost / Content Management System
 */


// Get server port    
if ($_SERVER["SERVER_PORT"] == "80") $port = ""; else $port = ':'.$_SERVER["SERVER_PORT"];

// Get site URL
$viewUrl = "http://".$_SERVER["SERVER_NAME"].$port.str_replace(array("index.php", "install.php"), "", $_SERVER["PHP_SELF"]);

// Replace last slash in viewUrl
$viewUrl = rtrim($viewUrl, "/");

// Rewrite base
$rewriteBase = str_replace(array("index.php", "install.php"), "", $_SERVER["PHP_SELF"]);

// Fail array
$fail = array();

// Directories to checking
$directoryArray = array(
    "templates", 
    "content"
    );

if (function_exists("apache_get_modules")) {
    if ( ! in_array("mod_rewrite", apache_get_modules())) {
        $fail["mod_rewrite"] = "error";
    } 
}

if (version_compare(PHP_VERSION, "5.3.0", "<")) {
    $fail["php"] = "error";
}


if (!is_writable(__FILE__)) {
    $fail["install"] = "error";
}


if (!is_writable(".htaccess")) {
    $fail["htaccess"] = "error";
}

// Dirs
foreach ($directoryArray as $directory) {
    if (!is_writable($directory.'/')) {
        $fail[$directory] = "error";
    }
}

if (isset($_POST["swifost_install"])) {

    // Insert data via POST
    $add_timezone = isset($_POST["timezone"]) ? $_POST["timezone"] : "";
    $add_title = isset($_POST["title"]) ? $_POST["title"] : "";
    $add_description = isset($_POST["description"]) ? $_POST["description"] : "";
    $add_keywords = isset($_POST["keywords"]) ? $_POST["keywords"] : "";
    $add_url = isset($_POST["url"]) ? $_POST["url"] : "";
    $add_email = isset($_POST["email"]) ? $_POST["email"] : "";
    $add_facebook = isset($_POST["facebook"]) ? $_POST["facebook"] : "";
    $add_twitter = isset($_POST["twitter"]) ? $_POST["twitter"] : "";
    $add_linkedin = isset($_POST["linkedin"]) ? $_POST["linkedin"] : "";
    $add_googleplus = isset($_POST["googleplus"]) ? $_POST["googleplus"] : "";
    $add_youtube = isset($_POST["youtube"]) ? $_POST["youtube"] : "";
    $add_github = isset($_POST["github"]) ? $_POST["github"] : "";

    file_put_contents("config.php", '<?php
    return array(
        "charset" => "UTF-8",
        "timezone" => "'.$add_timezone.'",
        "title" => "'.$add_title.'",
        "description" => "'.$add_description.'",
        "keywords" => "'.$add_keywords.'",
        "url" => "'.$add_url.'",
        "email" => "'.$add_email.'",
        "template" => "sharper",
        "social" => array(
            "facebook" => "'.$add_facebook.'",
            "twitter" => "'.$add_twitter.'",
            "instagram" => "",
            "googleplus" => "'.$add_googleplus.'",
            "youtube" => "'.$add_youtube.'",
            "souncloud" => "",
            "linkedin" => "'.$add_linkedin.'",
            "dribbble" => "",
            "behance" => "",
            "codepen" => "",
            "github" => "'.$add_github.'",
            "bitbucket" => "",
            "stackoverflow" => ""
        ),
        "plugins" => array(
            "markdown_syntax"
        ),
    );
    ');

        // Write in file .htaccess
        $htaccess = file_get_contents(".htaccess");
        $savedChanges = str_replace("/%url%/", $rewriteBase, $htaccess);

        $writeHtaccess = fopen (".htaccess", "w");
        fwrite($writeHtaccess, $savedChanges);
        fclose($writeHtaccess);

        // Redirect for install done
        Swifost::redirect("index.php?install=ready");
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Swifost &#8250; Installation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="<?php Swifost::go(Swifost::AUTHOR); ?>"/>
    <meta name="description" content="<?php Swifost::go(Swifost::SOFTWARE." is ".Swifost::DESCRIPTION); ?>"/>
    <link rel="shortcut icon" type="image/png" href="http://swifost.com/assets/img/favicon.png">
    <link rel="shortcut icon" type="image/x-icon" href="http://swifost.com/assets/img/favicon.ico">

    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <style>
    @import "http://fonts.googleapis.com/css?family=Raleway|Open+Sans:400|RobotoDraft:400,500,700,300|Roboto:400,300,500,600,100";@import "http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css";*{margin:0;padding:0;box-sizing:border-box;outline:none}body{font-family:Roboto;background:#f5f5f5;-webkit-touch-callout:none;-webkit-tap-highlight-color:transparent;border-top:.3em solid #e77249}::-webkit-scrollbar{-webkit-appearance:none;width:.3em;background-color:#cbcbcb}::-webkit-scrollbar-thumb{background:#E9573F} a {text-decoration: none;font-family: "Roboto" ,sans-serif;color: #3498db;}a:hover {color: #2980b9;}.Swifost-install{margin:0 auto;width:41em}.Swifost-install .Swifost-install--load{position:fixed;background:#e77249;text-align:center;top:0;left:0;right:0;bottom:0;height:100%;width:100%;font-size:100%;overflow:hidden !important;z-index:99999}.Swifost-install .Swifost-install--load---over{margin:9.23em auto}.Swifost-install .Swifost-install--load .Swifost-install--load---box{margin:2em auto}.Swifost-install .Swifost-install--load a{text-decoration:none}.Swifost-install .Swifost-install--load .Swifost-install--load---description,.Swifost-install--load .Swifost-install--load---url{color:#fff;font-family:"Roboto", sans-serif;font-weight:400;font-size:1.37em;margin-bottom:30px;letter-spacing:.1em}.Swifost-install .Swifost-install--load .Swifost-install--load---url{background-color:#fff;font-family:"RobotoDraft", sans-serif;font-size:1em;color:#e77249;padding:10px;border-radius:3px}.Swifost-install .Swifost-install--load .Swifost-install--load---url:hover{background-color:#E9573F;color:#fff;-webkit-transition:all .2s;-moz-transition:all .2s;-ms-transition:all .2s;-o-transition:all .2s;transition:all .2s}.Swifost-install--alert---body{margin:2.7em auto;max-width:27em;padding:20px;height:36.23em;border-radius:2px;background-color:#fff;box-shadow:0px 1px 2px 0px rgba(0, 0, 0, 0.15)}.Swifost-install--alert{position:relative;margin:1em auto;width:25em;color:#fff;border-radius:2px;-webkit-transition:all .2s ease;-moz-transition:all .2s ease;-ms-transition:all .2s ease;-o-transition:all .2s ease;transition:all .2s ease}.Swifost-install--alert.true{background-color:#19dd89}.Swifost-install--alert.false{background-color:#e74c3c}.Swifost-install--alert--icon{display:table-cell;vertical-align:middle;width:30px;padding:13px;text-align:center;background-color:rgba(255,255,255,0.2)}.Swifost-install--alert--icon > i{width:20px;font-size:20px}.Swifost-install--alert--text{display:table-cell;vertical-align:middle;font-family:"Raleway",sans-serif;line-height:1.2;padding:7px 10px 5px 13px}.Swifost-install--alert--text strong{font-family:"Roboto",sans-serif;font-weight:500}.hide{display:none !important}.Swifost-install--form:after{content:"";display:table;clear:both}.Swifost-install--form{max-width:27em;margin:2.7em auto;padding:20px;border-radius:2px;background-color:#fff;box-shadow:0px 1px 2px 0px rgba(0, 0, 0, 0.15)}.Swifost-install--form---title{padding:20px 0;text-align:center}.Swifost-install--form---title h1{position:relative;font-family:"RobotoDraft",sans-serif;font-weight:700;font-size:.8rem;text-transform:uppercase;letter-spacing:3px;margin-bottom:20px}.Swifost-install--form---title h1:after{position:absolute;bottom:-13px;left:0;right:0;margin:auto;width:40px;height:4px;background:#f5f5f5;content:""}.Swifost-install--form---title p,p.alert-error{font-size:14px;margin:0 auto;color:#bbb;text-align:center;font-family:"Roboto",sans-serif;font-weight:400}p.alert-error{color:#444;font-size:1em}.Swifost-install--form---input{display:block;width:100%;border:0;background:#f5f5f5;border-radius:3px;font-family:"RobotoDraft",sans-serif;padding:15px;margin-bottom:10px;outline:0;font-size:11px;color:#555;-webkit-transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-ms-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;transition:all .2s ease-in-out}.Swifost-install--form---input:focus{background:#fff;box-shadow:#f5f5f5 4px 4px}::-webkit-input-placeholder{text-transform:uppercase}:-moz-placeholder{color:#ccc;letter-spacing:2px;text-transform:uppercase}::-moz-placeholder{color:#ccc;letter-spacing:2px;text-transform:uppercase}:-ms-input-placeholder{color:#ccc;letter-spacing:2px;text-transform:uppercase}.Swifost-install--form---body .btn,.Swifost-install--alert---body .btn{display:block;width:48%;margin-top:10px;float:left;background:#DA4453;padding:15px;border:0;font-size:.7rem;color:#fff;cursor:pointer;letter-spacing:2px;text-decoration:none;border-radius:2px;text-transform:uppercase;border-bottom:2px solid #BD3E31;-webkit-transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-ms-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;transition:all .2s ease-in-out}.Swifost-install--form---body .btn:hover{background:#ED5565}.Swifost-install--form---body .btn:active{background:#BD3E31;border-bottom:1px solid}.Swifost-install--form---body .btn.install,.Swifost-install--form---body a.btn.install,.Swifost-install--alert---body .btn{float:right;background:#4A89DC;color:#fff;border-bottom:2px solid #2980B9}.Swifost-install--form---body a.btn.install.continue-two,.Swifost-install--alert---body .btn{width:11em !important} a.btn.install.continue-two {  margin-top: 37px;}.Swifost-install--form---body .btn.install:hover,.Swifost-install--alert---body .btn:hover{background:#5D9CEC}.Swifost-install--form---body .btn.install:active,.Swifost-install--alert---body .btn:active{background:#2980B9;border-bottom:1px solid}::-webkit-input-placeholder{color:#ccc;letter-spacing:2px;text-transform:uppercase}.Swifost-install--form---select{position:relative;width:100%;margin:0 0 10px;overflow:hidden;font-family:"RobotoDraft",sans-serif;outline:none;background:#fff;box-shadow:#f5f5f5 4px 4px;font-size:.7em;border-radius:3px;user-select:none}.Swifost-install--form---select:after{font-family:"FontAwesome";font-weight:700;content:"\f107";position:absolute;top:0;right:12px;z-index:1;color:#bbb;cursor:pointer;line-height:40px;font-size:15px}.Swifost-install--form---select:active:after{-webkit-transform:rotate(180deg);-moz-transform:rotate(180deg);-ms-transform:rotate(180deg);-o-transform:rotate(180deg);transform:rotate(180deg);-webkit-transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-ms-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;transition:all .2s ease-in-out}.Swifost-install--form---select select{position:relative;background:transparent;display:block;width:100%;font-family:"RobotoDraft",sans-serif;font-size:.7rem;color:#bbb;cursor:pointer;z-index:2;border:none;padding:15px;-moz-appearance:none;-webkit-appearance:none}.Swifost-install--form---select:focus,.Swifost-install--form---select:active{background:#f7f7f7;-webkit-transition:all .2s ease-in-out;-moz-transition:all .2s ease-in-out;-ms-transition:all .2s ease-in-out;-o-transition:all .2s ease-in-out;transition:all .2s ease-in-out}.Swifost-install--form---select select option{font-family:"RobotoDraft",sans-serif;color:#666;font-size:1em}.Swifost-install--form---select select option[disabled]{color:#eee;text-shadow:none;border:none}
    </style>
    <script>
        $(document).ready(function() {

            // var loader
            var loadSwifost = $(".Swifost-install--load");

            $('[data-box-install="contine"]').click(function() {
               $('[data-box="1"]').addClass("hide");
               $('[data-box="2"]').removeClass("hide");
           });

            $('[data-box-install="contine-two"]').click(function() {
                $('[data-box="2"]').addClass("hide");
                $('[data-box="3"]').removeClass("hide");
            });

            $(window).load(function() {
                loadSwifost.fadeIn(); 
                loadSwifost.queue(function(){ 
                    setTimeout(function(){ 
                        loadSwifost.dequeue(); 
                        }, 2400); // time
                });

                loadSwifost.fadeOut("slow");
            });
        });
    </script>
    <!--[if (gte IE 6) & (lte IE 9)]>
      <script src="http://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7/html5shiv.min.js"></script>
      <script src="http://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.js"></script>
    <![endif]-->
  </head>
  <body>

<div class="Swifost-install">

<div class="Swifost-install--load">
    <div class="Swifost-install--load---over">
    <svg width="118px" height="114px" viewBox="0 0 128 124" version="1.1"> 
    <g  stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
        <g sketch:type="MSLayerGroup" transform="translate(64.094438, 62.027151) rotate(90.000000) translate(-64.094438, -62.027151) translate(2.594438, -1.472849)">
            <path d="M56.4728486,0.594437594 L82.4728486,0.594437594 C104.552849,0.594437594 122.472849,18.5144376 122.472849,40.5944376 L122.472849,67.5944376 L46.4728486,67.5944376 L46.4728486,10.5944376 C46.4728486,5.07443759 50.9528486,0.594437594 56.4728486,0.594437594"  fill="#FFF" sketch:type="MSShapeGroup"></path>
            <path d="M84.4728486,33.0944376 C88.8888486,33.0944376 92.4728486,36.6784376 92.4728486,41.0944376 L92.4728486,93.0944376 L76.4728486,93.0944376 L76.4728486,41.0944376 C76.4728486,36.6784376 80.0568486,33.0944376 84.4728486,33.0944376" fill="#E77249" sketch:type="MSShapeGroup"></path>
            <path d="M0.472848643,66.5944376 L76.4728486,66.5944376 L76.4728486,86.5944376 C76.4728486,108.674438 58.5528486,126.594438 36.4728486,126.594438 L10.4728486,126.594438 C4.95284864,126.594438 0.472848643,122.114438 0.472848643,116.594438 L0.472848643,66.5944376" id="Shape" fill="#FFF" sketch:type="MSShapeGroup"></path>
            <path d="M30.4728486,64.3500366 L46.4728486,64.3500366 L46.4728486,91.5944376 C46.4728486,96.0104376 42.8888486,99.5944376 38.4728486,99.5944376 C34.0568486,99.5944376 30.4728486,96.0104376 30.4728486,91.5944376 L30.4728486,66.5944376" fill="#E77249" sketch:type="MSShapeGroup"></path>
        </g>
    </g>
</svg>
  <div class="Swifost-install--load---box">
    <h3 class="Swifost-install--load---description"><?php Swifost::go(Swifost::DESCRIPTION); ?></h3>
    <a class="Swifost-install--load---url" href="http://swifost.com" target="_blank" title="Visit Swifost">http://swifost.com</a>
    </div>
</div>
</div>

  <div data-box="1" class="Swifost-install--alert---body">  
    <div class="Swifost-install--form---title"> 
    <h1>Swifost Installation</h1>  
    <p>State permits required</p> 
  </div> 
            <?php
                if (is_writable(__FILE__)) {
                      echo '
                      <div class="Swifost-install--alert true"> 
                        <div class="Swifost-install--alert--icon"> 
                          <i class="fa fa-check"></i> 
                        </div>
                          <div class="Swifost-install--alert--text">
                            <p>Install script writable</p> 
                          </div>
                        </div>';

                } else {
                      echo '
                          <div class="Swifost-install--alert false"> 
                          <div class="Swifost-install--alert--icon"> 
                          <i class="fa fa-exclamation-circle"></i> 
                          </div>
                          <div class="Swifost-install--alert--text">
                          <p>Install script not writable</p> 
                          </div>
                          </div>';
                }

                if (version_compare(PHP_VERSION, "5.3.0", "<")) {
                        echo '
                          <div class="Swifost-install--alert false"> 
                          <div class="Swifost-install--alert--icon"> 
                          <i class="fa fa-exclamation-circle"></i> 
                          </div>
                          <div class="Swifost-install--alert--text">
                          <p>PHP 5.3 or greater is required</p> 
                          </div>
                          </div>';
                } else {
                        echo '
                          <div class="Swifost-install--alert true"> 
                          <div class="Swifost-install--alert--icon"> 
                          <i class="fa fa-check"></i> 
                          </div>
                          <div class="Swifost-install--alert--text">
                          <p>PHP Version <strong>'.PHP_VERSION.'</strong></p> 
                          </div>
                          </div>';
                }

                if (function_exists("apache_get_modules")) {
                    if (!in_array("mod_rewrite", apache_get_modules())) {
                        echo '
                          <div class="Swifost-install--alert false"> 
                          <div class="Swifost-install--alert--icon"> 
                          <i class="fa fa-exclamation-circle"></i> 
                          </div>
                          <div class="Swifost-install--alert--text">
                          <p>The module Apache <strong>Mod Rewrite</strong> available</p> 
                          </div>
                          </div>';
                    } else {
                       echo '
                          <div class="Swifost-install--alert true"> 
                          <div class="Swifost-install--alert--icon"> 
                          <i class="fa fa-check"></i> 
                          </div>
                          <div class="Swifost-install--alert--text">
                          <p>The Module <strong>Mod Rewrite</strong> is installed</p> 
                          </div>
                          </div>';
                    }
                } else {
                      echo '
                          <div class="Swifost-install--alert true"> 
                          <div class="Swifost-install--alert--icon"> 
                          <i class="fa fa-check"></i> 
                          </div>
                          <div class="Swifost-install--alert--text">
                          <p>Module <strong>Mod Rewrite</strong> is installed</p> 
                          </div>
                          </div>';
                }

                if (is_writable(".htaccess")) {
                      echo '
                          <div class="Swifost-install--alert true"> 
                          <div class="Swifost-install--alert--icon"> 
                          <i class="fa fa-check"></i> 
                          </div>
                          <div class="Swifost-install--alert--text">
                          <p>You can run <strong>.htaccess</strong> successfully</p> 
                          </div>
                          </div>';
                } else {
                      echo '
                          <div class="Swifost-install--alert false"> 
                          <div class="Swifost-install--alert--icon"> 
                          <i class="fa fa-exclamation-circle"></i> 
                          </div>
                          <div class="Swifost-install--alert--text">
                          <p>You can not run <strong>.htaccess</strong> error</p> 
                          </div>
                          </div>';
                }

                foreach ($directoryArray as $dir) {
                    if (is_writable($dir."/")) {
                      echo '
                          <div class="Swifost-install--alert true"> 
                          <div class="Swifost-install--alert--icon"> 
                          <i class="fa fa-check"></i> 
                          </div>
                          <div class="Swifost-install--alert--text">
                          <p>The permissions on <strong> '.$dir.' </strong> are available</p> 
                          </div>
                          </div>';
                    } else {
                      echo '
                          <div class="Swifost-install--alert false"> 
                          <div class="Swifost-install--alert--icon"> 
                          <i class="fa fa-exclamation-circle"></i> 
                          </div>
                          <div class="Swifost-install--alert--text">
                          <p>You can not read anything <strong> '.$dir.' </strong> not writable</p> 
                          </div>
                          </div>';
                    }
                }

            ?>
            <?php
                if (count($fail) == 0) {
            ?>
            <a href="#" class="btn install" data-box-install="contine">Continue <i class="fa fa-chevron-right"></i></a> 
            <?php
                } else {
            ?>
            <p class="alert-error">Do not meet the requirements to install Swifost.</p>
            <?php } ?>
        </div>

<div data-box="2" class="Swifost-install--form hide" action="" method="post">  

  <div class="Swifost-install--form---title"> 
    <h1>Information</h1> 
    <p>Read this information before or after</p>
  </div> 
  <div class="Swifost-install--form---body">   

    <p style="padding: 26px 5px;">For more information have a look at the Swifost documentation at <a href="http://swifost.com/documentation.html">http://swifost.com/documentation.html</a>
 
    <a href="#" class="btn install continue-two" data-box-install="contine-two">Continue <i class="fa fa-chevron-right"></i></a>  
  </div>
</div>      

<form data-box="3" class="Swifost-install--form hide" action="" method="post">  
 
  <div class="Swifost-install--form---title"> 
    <h1>Facts page</h1>  
    <p>This information is important</p> 
  </div> 
  <div class="Swifost-install--form---body">
<div class="Swifost-install--form---select">      
        <select name="timezone"> 
          <option value="none" disabled="disabled" selected="selected">TIME ZONE</option>
                <option value="Kwajalein">(GMT-12:00) International Date Line West</option>
                <option value="Pacific/Samoa">(GMT-11:00) Midway Island, Samoa</option>
                <option value="Pacific/Honolulu">(GMT-10:00) Hawaii</option>
                <option value="America/Anchorage">(GMT-09:00) Alaska</option>
                <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US &amp; Canada)</option>
                <option value="America/Tijuana">(GMT-08:00) Tijuana, Baja California</option>
                <option value="America/Denver">(GMT-07:00) Mountain Time (US &amp; Canada)</option>
                <option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                <option value="America/Phoenix">(GMT-07:00) Arizona</option>
                <option value="America/Regina">(GMT-06:00) Saskatchewan</option>
                <option value="America/Tegucigalpa">(GMT-06:00) Central America</option>
                <option value="America/Chicago">(GMT-06:00) Central Time (US &amp; Canada)</option>
                <option value="America/Mexico_City">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                <option value="America/New_York">(GMT-05:00) Eastern Time (US &amp; Canada)</option>
                <option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
                <option value="America/Indiana/Indianapolis">(GMT-05:00) Indiana (East)</option>
                <option value="America/Caracas">(GMT-04:30) Caracas</option>  
                <option value="America/Halifax">(GMT-04:00) Atlantic Time (Canada)</option>
                <option value="America/Manaus">(GMT-04:00) Manaus</option>
                <option value="America/Santiago">(GMT-04:00) Santiago</option>
                <option value="America/La_Paz">(GMT-04:00) La Paz</option>
                <option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
                <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
                <option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
                <option value="America/Godthab">(GMT-03:00) Greenland</option>
                <option value="America/Montevideo">(GMT-03:00) Montevideo</option>
                <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Georgetown</option>
                <option value="Atlantic/South_Georgia">(GMT-02:00) Mid-Atlantic</option>
                <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
                <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
                <option value="Europe/London">(GMT) Greenwich Mean Time : Dublin, Edinburgh, Lisbon, London</option>
                <option value="Atlantic/Reykjavik">(GMT) Monrovia, Reykjavik</option>
                <option value="Africa/Casablanca">(GMT) Casablanca</option>
                <option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
                <option value="Europe/Sarajevo">(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb</option>
                <option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
                <option value="Africa/Algiers">(GMT+01:00) West Central Africa</option>
                <option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
                <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
                <option value="Europe/Helsinki">(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius</option>
                <option value="Europe/Athens">(GMT+02:00) Athens, Bucharest, Istanbul</option>
                <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
                <option value="Asia/Amman">(GMT+02:00) Amman</option>
                <option value="Asia/Beirut">(GMT+02:00) Beirut</option>
                <option value="Africa/Windhoek">(GMT+02:00) Windhoek</option>
                <option value="Africa/Harare">(GMT+02:00) Harare, Pretoria</option>
                <option value="Asia/Kuwait">(GMT+03:00) Kuwait, Riyadh</option>
                <option value="Asia/Baghdad">(GMT+03:00) Baghdad</option>
                <option value="Europe/Minsk">(GMT+03:00) Minsk</option>
                <option value="Africa/Nairobi">(GMT+03:00) Nairobi</option>
                <option value="Asia/Tbilisi">(GMT+03:00) Tbilisi</option>
                <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
                <option value="Asia/Muscat">(GMT+04:00) Abu Dhabi, Muscat</option>
                <option value="Asia/Baku">(GMT+04:00) Baku</option>
                <option value="Europe/Moscow">(GMT+04:00) Moscow, St. Petersburg, Volgograd</option>
                <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
                <option value="Asia/Karachi">(GMT+05:00) Islamabad, Karachi</option>
                <option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
                <option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
                <option value="Asia/Colombo">(GMT+05:30) Sri Jayawardenepura</option>
                <option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
                <option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
                <option value="Asia/Yekaterinburg">(GMT+06:00) Ekaterinburg</option>
                <option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
                <option value="Asia/Novosibirsk">(GMT+07:00) Almaty, Novosibirsk</option>
                <option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                <option value="Asia/Beijing">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
                <option value="Asia/Krasnoyarsk">(GMT+08:00) Krasnoyarsk</option>
                <option value="Asia/Ulaanbaatar">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
                <option value="Asia/Kuala_Lumpur">(GMT+08:00) Kuala Lumpur, Singapore</option>
                <option value="Asia/Taipei">(GMT+08:00) Taipei</option>
                <option value="Australia/Perth">(GMT+08:00) Perth</option>
                <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
                <option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
                <option value="Australia/Darwin">(GMT+09:30) Darwin</option>
                <option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
                <option value="Australia/Sydney">(GMT+10:00) Canberra, Melbourne, Sydney</option>
                <option value="Australia/Brisbane">(GMT+10:00) Brisbane</option> 
                <option value="Australia/Hobart">(GMT+10:00) Hobart</option>
                <option value="Asia/Yakutsk">(GMT+10:00) Yakutsk</option>
                <option value="Pacific/Guam">(GMT+10:00) Guam, Port Moresby</option>
                <option value="Asia/Vladivostok">(GMT+11:00) Vladivostok</option>
                <option value="Pacific/Fiji">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
                <option value="Asia/Magadan">(GMT+12:00) Magadan, Solomon Is., New Caledonia</option>
                <option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
                <option value="Pacific/Tongatapu">(GMT+13:00) Nukualofa</option> 
        </select>
      </div>
    <input class="Swifost-install--form---input" type="text" name="title" placeholder="Site name" required/>
    <input class="Swifost-install--form---input" type="text" name="description" placeholder="Site description" required/>
    <input class="Swifost-install--form---input" type="text" name="keywords" placeholder="Site keywords" required/>
    <input class="Swifost-install--form---input" type="text" name="url" placeholder="Site Url" value="<?php echo $viewUrl; ?>" required/>
    <input class="Swifost-install--form---input" type="email" name="email" placeholder="Email" required/>
    <input class="Swifost-install--form---input" type="text" name="facebook" placeholder="Facebook username" />
    <input class="Swifost-install--form---input" type="text" name="twitter" placeholder="Twitter username" />
    <input class="Swifost-install--form---input" type="text" name="linkedin" placeholder="Linkedin username" />
    <input class="Swifost-install--form---input" type="text" name="googleplus" placeholder="GooglePlus url" />
    <input class="Swifost-install--form---input" type="text" name="youtube" placeholder="Youtube username" />
    <input class="Swifost-install--form---input" type="text" name="github" placeholder="Github Username" />  
    <button type="reset" class="btn">Reset</button>
    <input type="submit" class="btn install" name="swifost_install" value="Install">
  </div> 
</form>  

</div>

</body>
</html>