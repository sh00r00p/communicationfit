<?php

/**
 *
 * Main file
 *
 * @version             3.0.0
 * @package             Gavern Framework
 * @copyright			Copyright (C) 2010 - 2012 GavickPro. All rights reserved.
 *               
 */
 
// No direct access.
if(!defined('DS')){ define('DS',DIRECTORY_SEPARATOR); }
defined('_JEXEC') or die;
// Add JavaScript Frameworks
JHtml::_('bootstrap.framework');
// enable showing errors in PHP

// include framework classes and files
require_once('lib/gk.framework.php');
require_once('lib/framework/gk.const.php');
// run the framework
$tpl = new GKTemplate($this, $GK_TEMPLATE_MODULE_STYLES);

// EOF