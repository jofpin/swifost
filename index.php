<?php

/**
 *  I establish the route of all base directories.
 *
 *  Define: libraries, templates, plugins and content.
 *
 *  @author Jose Pino, @jofpin | jofpin@gmail.com
 *  @copyright 2015 Swifost / Content Management System
 */

// libraries
define("LIBRARIES_PATH", __DIR__ ."/libraries");
// templates
define("TEMPLATES_PATH", __DIR__ ."/templates");
// plugins
define("PLUGINS_PATH", __DIR__ ."/plugins");
// content
define("CONTENT_PATH", __DIR__ ."/content");


// Load main library Swifost
require LIBRARIES_PATH . "/Swifost/Swifost.php";

// First check for installer then go
Swifost::push()->ready(
    "config.php", // File of configuration
    "install.php", // File of install
    "index.php", // File base
    "install", // Parameter GET > install
    "ready" // request done of parameter GET > install
);
