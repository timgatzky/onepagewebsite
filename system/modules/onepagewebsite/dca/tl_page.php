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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_page']['palettes']['regular'] = str_replace('guests','guests,opw_hide',$GLOBALS['TL_DCA']['tl_page']['palettes']['regular']);

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_page']['fields']['opw_hide'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_page']['opw_hide'],
	'exclude'           => true,
	'inputType'         => 'checkbox',
	'eval'              => array('tl_class'=>'w50'),
	'sql'               => "char(1) NOT NULL default ''"
);