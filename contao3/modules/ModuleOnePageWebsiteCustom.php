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

class ModuleOnePageWebsiteCustom extends \Module
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
		
		$arrPages = deserialize($this->pages);
		
		if(!is_array($arrPages))
		{
			$arrPages = array($arrPages);
		}
		
		if(count($arrPages) < 1)
		{
			return '';
		}
		
		foreach($arrPages as $pid)
		{
			// add pages to template
			#fix 4
			$this->Template->items .= $this->OnePageWebsite->generatePage($pid,0,$this->opw_template);
		}
				
		
	}
}

?>