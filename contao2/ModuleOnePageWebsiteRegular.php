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



class ModuleOnePageWebsiteRegular extends Module
{
	/**
	 * @var
	 */
	protected $strTemplate = 'mod_onepage';
	
	
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$this->Template = new BackendTemplate('be_wildcard');
			$this->Template->wildcard = '### ONE-PAGE-WEBSITE :: PAGE BUILDER ###' . "<br>" . $GLOBALS['TL_LANG']['FMD'][$this->type][0];
			$this->Template->title = $this->headline;
			
			return $this->Template->parse();
		}
		
		return parent::generate();
	}

	/**
	 * Generate
	 */
	protected function compile()
	{
		$this->import('OnePageWebsite');
		$this->OnePageWebsite->__set('hardLimit',$this->hardLimit);
		$this->OnePageWebsite->__set('showLevel',$this->showLevel);
		
		global $objPage;

		$startPage = '';
		$trail = $objPage->trail;
		$level = 0;
		
		if(!$this->defineRoot || $this->rootPage == $trail[0])
		{
			$startPage = $objPage->id;
		}
		// reference page is selected
		else if ($this->defineRoot && $this->rootPage > 0)
		{
			$startPage = $this->rootPage;
		}
								
		// add pages to template
		#fix 4
		$this->Template->items = $this->OnePageWebsite->generatePage($startPage,$level,$this->opw_template);
	}
}

?>