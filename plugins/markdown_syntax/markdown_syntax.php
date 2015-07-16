<?php

/**
 *  Markdown plugin - Markdown Syntax is easy!
 *
 *  @package Swifost
 *  @subpackage Plugins
 *  @author Jose Pino, @jofpin | jofpin@gmail.com
 *  @copyright 2015 Swifost / Content Management System
 *  @version 1.0.0
 *
 */

include PLUGINS_PATH . "/markdown_syntax/parsedown/Parsedown.php";
include PLUGINS_PATH . "/markdown_syntax/base.php";


// Filtration and markdown relationship with content (function markdown)
Swifost::push()->createFilter("content", "markdown");