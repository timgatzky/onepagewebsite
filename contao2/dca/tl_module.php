<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Tim Gatzky 2012 
 * @author     Tim Gatzky <info@tim-gatzky.de>
 * @package    OnePageWebsite
 * @license    LGPL 
 * @filesource
 */

$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('tl_module_onePageWebsite','modifyFieldDca');


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['onepagewebsiteregular'] = '{title_legend},name,headline,type;{nav_legend},showLevel,hardLimit,showProtected;{reference_legend:hide},defineRoot;{template_legend:hide},opw_template,opw_mod_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['onepagewebsitecustom'] = '{title_legend},name,headline,type;{nav_legend},showProtected,pages;{template_legend:hide},opw_template,opw_mod_template;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
$GLOBALS['TL_DCA']['tl_module']['palettes']['onepagewebsitenavigation'] = '{title_legend},name,headline,type;{nav_legend},rootPage;{template_legend:hide},navigationTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


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
	'eval'				=> array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['opw_mod_template'] = array
(
	'label'             => &$GLOBALS['TL_LANG']['tl_module']['opw_mod_template'],
	'default'          	=> 'mod_onepage',
	'exclude'           => true,
	'inputType'         => 'select',
	'options'			=> $this->getTemplateGroup('mod_onepage'),
	'eval'				=> array('tl_class'=>'w50')
);



class tl_module_onePageWebsite extends Backend
{
	/**
	 * Modify field dca depending on loaded module
	 * @param object, DataContainer
	 * @return object, DataContainer
	 */
	public function modifyFieldDca(DataContainer $dc)
	{
		$objModule = $this->Database->execute("SELECT type FROM tl_module WHERE id=" . $dc->id . " LIMIT 1");
		
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
		
		$objModules = $this->Database->execute("SELECT * FROM tl_module WHERE type IN('onepagewebsiteregular','onepagewebsitecustom')");
		
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

?>