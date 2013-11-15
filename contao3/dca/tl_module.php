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


$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('tl_module_onePageWebsite','modifyFieldDca');


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['onepagewebsiteregular'] = '{title_legend},name,headline,type;{nav_legend},showLevel,hardLimit,showProtected;{reference_legend:hide},defineRoot;{template_legend:hide},opw_template,opw_mod_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['onepagewebsitecustom'] = '{title_legend},name,headline,type;{nav_legend},showProtected,pages;{template_legend:hide},opw_template,opw_mod_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['onepagewebsitenavigation'] = '{title_legend},name,headline,type;{nav_legend},jumpTo,rootPage;{template_legend:hide},navigationTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


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
	'sql'               => "varchar(128) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['opw_mod_template'] = array
(
	'label'             => &$GLOBALS['TL_LANG']['tl_module']['opw_mod_template'],
	'default'          	=> 'mod_onepage',
	'exclude'           => true,
	'inputType'         => 'select',
	'options'			=> $this->getTemplateGroup('mod_onepage'),
	'eval'				=> array('tl_class'=>'w50'),
	'sql'               => "varchar(128) NOT NULL default ''"
);



class tl_module_onePageWebsite extends \Backend
{
	/**
	 * Modify field dca depending on loaded module
	 * @param object, DataContainer
	 * @return object, DataContainer
	 */
	public function modifyFieldDca(DataContainer $dc)
	{
		$objDatabase = \Database::getInstance();
		
		$objModule = $objDatabase->execute("SELECT type FROM tl_module WHERE id=" . $dc->id . " LIMIT 1");
		
		if($objModule->type == 'onepagewebsiteregular')
		{
			$GLOBALS['TL_DCA']['tl_module']['fields']['showLevel']['eval']['tl_class'] = ''; 
			$GLOBALS['TL_DCA']['tl_module']['fields']['hardLimit']['eval']['tl_class'] = 'w50'; 
		}
		
		if($objModule->type == 'onepagewebsitenavigation')
		{
			$GLOBALS['TL_DCA']['tl_module']['fields']['rootPage'] = array
			(
				'label'            => &$GLOBALS['TL_LANG']['tl_module']['rootModule'],
				'exclude'          => false,
				'inputType'        => 'radio',
				'options_callback' => array('tl_module_onePageWebsite','getModules'),
			); 
		}
		
		return $dc;		
	}
	
	/**
	 * Get all onepagewebsiteregular, onepagewebsitecustom modules
	 * @param object, DataContainer
	 * @return array
	 */
	public function getModules(DataContainer $dc)
	{
		$arrReturn = array();
		
		$objDatabase = \Database::getInstance();
		
		$objModules = $objDatabase->execute("SELECT * FROM tl_module WHERE type IN('onepagewebsiteregular','onepagewebsitecustom')");
		
		if($objModules->numRows < 1)
		{
			return array(); 
		}
		
		while($objModules->next())
		{
			$arrReturn[$objModules->id] = $objModules->name. ' <span style="color:#b3b3b3">[id:'.$objModules->id.']</span>';
		}
		
		return $arrReturn;
	}

}
