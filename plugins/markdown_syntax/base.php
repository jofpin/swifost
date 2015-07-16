<?php

/**
 *  Markdown plugin - Markdown Syntax is easy!
 *
 *  @package Swifost
 *  @subpackage Plugins
 *  @author      Jose Pino, @jofpin | jofpin@gmail.com
 *  @copyright   2015 Swifost / Content Management System
 *  @version 1.0.0
 *
 */

// relate Using Parsedown to relate markdown syntax
function markdown($content) {
	$Parsedown = new Parsedown();
	return $Parsedown->text($content);
}
