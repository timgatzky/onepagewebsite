<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package OnePageWebsite
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'OnePageWebsite',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'OnePageWebsite\OnePageWebsite'                  			=> 'system/modules/onepagewebsite/OnePageWebsite/OnePageWebsite.php',
	'OnePageWebsite\Backend\TableModule'                  		=> 'system/modules/onepagewebsite/OnePageWebsite/Backend/TableModule.php',
	'OnePageWebsite\Frontend\ModuleOnePageWebsiteCustom'      	=> 'system/modules/onepagewebsite/OnePageWebsite/Frontend/ModuleOnePageWebsiteCustom.php',
	'OnePageWebsite\Frontend\ModuleOnePageWebsiteNavigation'	=> 'system/modules/onepagewebsite/OnePageWebsite/Frontend/ModuleOnePageWebsiteNavigation.php',
	'OnePageWebsite\Frontend\ModuleOnePageWebsiteRegular' 		=> 'system/modules/onepagewebsite/OnePageWebsite/Frontend/ModuleOnePageWebsiteRegular.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_onepage'					=> 'system/modules/onepagewebsite/templates',
	'mod_onepagewebsitenavigation'	=> 'system/modules/onepagewebsite/templates',
	'moo_smoothScroll'				=> 'system/modules/onepagewebsite/templates',
	'opw_default'					=> 'system/modules/onepagewebsite/templates',
));
