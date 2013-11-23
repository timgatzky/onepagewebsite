<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		OnePageWebsite
 * @link		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Front end modules
 */
$GLOBALS['FE_MOD']['onepagewebsite'] = array
(
	'onepagewebsiteregular' 	=> "OnePageWebsite\Frontend\ModuleOnePageWebsiteRegular",
	'onepagewebsitecustom' 		=> "OnePageWebsite\Frontend\ModuleOnePageWebsiteCustom",
	'onepagewebsitenavigation' 	=> "OnePageWebsite\Frontend\ModuleOnePageWebsiteNavigation",
);