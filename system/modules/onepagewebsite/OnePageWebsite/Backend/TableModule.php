<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		onepagewebsite
 * @link		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Namespace
 */
namespace OnePageWebsite\Backend;

/**
 * Class file for tl_module
 * TableModule
 */
class TableModule extends \Backend
{
	/**
	 * Modify field dca depending on loaded module
	 * @param object, DataContainer
	 * @return object, DataContainer
	 */
	public function modifyDCA(\DataContainer $objDC)
	{
		if(\Input::getInstance()->get('act') != 'edit')
		{
			return $objDC;
		}
		
		$objActiveRecord = \Database::getInstance()->prepare("SELECT * FROM ".$objDC->table." WHERE id=?")->limit(1)->execute($objDC->id);

		if($objActiveRecord->type == 'onepagewebsiteregular')
		{
			$GLOBALS['TL_DCA']['tl_module']['fields']['showLevel']['eval']['tl_class'] = ''; 
			$GLOBALS['TL_DCA']['tl_module']['fields']['hardLimit']['eval']['tl_class'] = 'w50'; 
		}
		
		if($objActiveRecord->type == 'onepagewebsitenavigation')
		{
			$GLOBALS['TL_DCA']['tl_module']['fields']['rootPage'] = array
			(
				'label'            => &$GLOBALS['TL_LANG']['tl_module']['rootModule'],
				'exclude'          => false,
				'inputType'        => 'radio',
				'options_callback' => array('OnePageWebsite\Backend\TableModule','getModules'),
			); 
		}
		
		return $objDC;		
	}
	
	/**
	 * Get all onepagewebsiteregular, onepagewebsitecustom modules
	 * @param object, DataContainer
	 * @return array
	 */
	public function getModules(\DataContainer $objDC)
	{
		$objModules = \Database::getInstance()->execute("SELECT * FROM ".$objDC->table." WHERE type IN('onepagewebsiteregular','onepagewebsitecustom')");
		
		if($objModules->numRows < 1)
		{
			return array(); 
		}
		
		$arrReturn = array();
		while($objModules->next())
		{
			$arrReturn[$objModules->id] = $objModules->name. ' <span style="color:#b3b3b3">[id:'.$objModules->id.']</span>';
		}
		
		return $arrReturn;
	}

}
