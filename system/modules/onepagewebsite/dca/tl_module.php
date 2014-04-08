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

$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] 			= array('OnePageWebsite\Backend\TableModule','modifyDCA');


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['onepagewebsiteregular'] 	= '{title_legend},name,headline,type;{nav_legend},showLevel,hardLimit,showProtected;{reference_legend:hide},defineRoot;{template_legend:hide},opw_template,opw_mod_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['onepagewebsitecustom'] 	= '{title_legend},name,headline,type;{nav_legend},showProtected,pages;{template_legend:hide},opw_template,opw_mod_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['onepagewebsitenavigation']	= '{title_legend},name,headline,type;{nav_legend},showHidden,jumpTo,rootPage;{template_legend:hide},navigationTpl,opw_scrolldetection;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['opw_template'] = array
(
	'label'             => &$GLOBALS['TL_LANG']['tl_module']['opw_template'],
	'default'          	=> 'opw_default',
	'exclude'           => true,
	'inputType'         => 'select',
	'options'			=> $this->getTemplateGroup('opw_'),
	'eval'				=> array('tl_class'=>'w50'),
	'sql'               => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['opw_mod_template'] = array
(
	'label'             => &$GLOBALS['TL_LANG']['tl_module']['opw_mod_template'],
	'default'          	=> 'mod_onepage',
	'exclude'           => true,
	'inputType'         => 'select',
	'options'			=> $this->getTemplateGroup('mod_onepage'),
	'eval'				=> array('tl_class'=>'w50'),
	'sql'               => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['opw_scrolldetection'] = array
(
	'label'             => &$GLOBALS['TL_LANG']['tl_module']['opw_scrolldetection'],
	'exclude'           => true,
	'inputType'         => 'checkbox',
	'eval'				=> array('tl_class'=>'w50'),
	'sql'               => "char(1) NOT NULL default ''"
);


