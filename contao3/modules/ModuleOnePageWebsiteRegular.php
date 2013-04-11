<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @copyright	Tim Gatzky 2013
 * @author		Tim Gatzky <info@tim-gatzky.de>
 * @package		OnePageWesbite
 * @link		http://contao.org
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


class ModuleOnePageWebsiteRegular extends \Module
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
		$objOnePageWebsite = $this->OnePageWebsite;
		
		$objOnePageWebsite->__set('hardLimit',$this->hardLimit);
		$objOnePageWebsite->__set('showLevel',$this->showLevel);
		
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
		$this->Template->items = $objOnePageWebsite->generatePage($startPage,$level);
	}
}

?>