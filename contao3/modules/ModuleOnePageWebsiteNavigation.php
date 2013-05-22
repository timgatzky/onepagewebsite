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


class ModuleOnePageWebsiteNavigation extends \ModuleNavigation
{
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$this->Template = new BackendTemplate('be_wildcard');
			$this->Template->wildcard = '### ONE-PAGE-WEBSITE :: NAVIGATION ###' . "<br>" . $GLOBALS['TL_LANG']['FMD'][$this->type][0];
			$this->Template->title = $this->headline;

			return $this->Template->parse();
		}
		
		return parent::generate();
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		if(!$this->rootPage)
		{
			return '';
		}
		global $objPage;
		
		// I know its not nice but yes I use the rootPage field for the module selection
		$this->rootModule = $this->rootPage;

		$objDatabase = \Database::getInstance();
		
		// fetch reference module
		$objModule = $objDatabase->prepare("SELECT * FROM tl_module WHERE id=?")
		->limit(1)
		->execute($this->rootModule);
		
		if($objModule->numRows < 1)
		{
		   return '';
		}
		
		// set rootPage from module
		$this->rootPage = $objModule->rootPage;
		
		// set new jumpTo page
		if(!$this->jumpTo)
		{
			$this->jumpTo = $objPage->id;
		}
	
		#(issue 1)
		$this->Template->skipId = 'skipNavigation' . $this->id;
		$this->Template->skipNavigation = specialchars($GLOBALS['TL_LANG']['MSC']['skipNavigation']);
	
		$this->Template->items = $this->renderNavigation($this->rootPage);
	}


	/**
	 * Recursively compile the navigation menu and return it as HTML string
	 * @param integer
	 * @param integer
	 * @return string
	 * Taken and modified from Modules.php
	 */
	protected function renderNavigation($pid, $level=1)
	{
		$objDatabase = \Database::getInstance();

		$time = time();
		
		// Get all active subpages
		$objSubpages = $objDatabase->prepare("SELECT p1.*, (SELECT COUNT(*) FROM tl_page p2 WHERE p2.pid=p1.id AND p2.type!='root' AND p2.type!='error_403' AND p2.type!='error_404'" . (!$this->showHidden ? (($this instanceof ModuleSitemap) ? " AND (p2.hide!=1 OR sitemap='map_always')" : " AND p2.hide!=1 AND p2.opw_hide!=1 ") : "") . ((FE_USER_LOGGED_IN && !BE_USER_LOGGED_IN) ? " AND p2.guests!=1" : "") . (!BE_USER_LOGGED_IN ? " AND (p2.start='' OR p2.start<".$time.") AND (p2.stop='' OR p2.stop>".$time.") AND p2.published=1" : "") . ") AS subpages FROM tl_page p1 WHERE p1.pid=? AND p1.type!='root' AND p1.type!='error_403' AND p1.type!='error_404'" . (!$this->showHidden ? (($this instanceof ModuleSitemap) ? " AND (p1.hide!=1 OR sitemap='map_always')" : " AND p1.hide!=1 AND p1.opw_hide!=1") : "") . ((FE_USER_LOGGED_IN && !BE_USER_LOGGED_IN) ? " AND p1.guests!=1" : "") . (!BE_USER_LOGGED_IN ? " AND (p1.start='' OR p1.start<".$time.") AND (p1.stop='' OR p1.stop>".$time.") AND p1.published=1" : "") . " ORDER BY p1.sorting")
		->execute($pid);
		
		if ($objSubpages->numRows < 1)
		{
			return '';
		}
		
		$items = array();
		$groups = array();

		// Get all groups of the current front end user
		if (FE_USER_LOGGED_IN)
		{
			$this->import('FrontendUser', 'User');
			$groups = $this->User->groups;
		}

		// Layout template fallback
		if ($this->navigationTpl == '')
		{
			$this->navigationTpl = 'nav_default';
		}

		$objTemplate = new FrontendTemplate($this->navigationTpl);

		$objTemplate->type = get_class($this);
		$objTemplate->level = 'level_' . $level++;

		// Get page objects
		global $objPage;
		
		// jumpTo page
		$objJumpTo = $objDatabase->prepare("SELECT * FROM tl_page WHERE id=?")->limit(1)->execute($this->jumpTo);

		// Browse subpages
		while($objSubpages->next())
		{
			// Skip hidden sitemap pages
			if ($this instanceof ModuleSitemap && $objSubpages->sitemap == 'map_never')
			{
				continue;
			}

			$subitems = '';
			$_groups = deserialize($objSubpages->groups);

			// Do not show protected pages unless a back end or front end user is logged in
			if (!$objSubpages->protected || BE_USER_LOGGED_IN || (is_array($_groups) && count(array_intersect($_groups, $groups))) || $this->showProtected || ($this instanceof ModuleSitemap && $objSubpages->sitemap == 'map_always'))
			{
				// Check whether there will be subpages
				if ($objSubpages->subpages > 0 && (!$this->showLevel || $this->showLevel >= $level || (!$this->hardLimit && ($objPage->id == $objSubpages->id || in_array($objPage->id, $this->getChildRecords($objSubpages->id, 'tl_page'))))))
				{
					$subitems = $this->renderNavigation($objSubpages->id, $level);
				}

				// href
				$href = $this->generateFrontendUrl($objJumpTo->row()) . '#page' .$objSubpages->id;
				
				$strClass = (($subitems != '') ? 'submenu' : '') . ($objSubpages->protected ? ' protected' : '') . (($objSubpages->cssClass != '') ? ' ' . $objSubpages->cssClass : '') . (in_array($objSubpages->id, $objPage->trail) ? ' trail' : '');

				// Mark pages on the same level (see #2419)
				if ($objSubpages->pid == $objPage->pid)
				{
					$strClass .= ' sibling';
				}

				$row = $objSubpages->row();

				$row['isActive'] = false;
				$row['subitems'] = $subitems;
				$row['class'] = trim($strClass);
				$row['title'] = specialchars($objSubpages->title, true);
				$row['pageTitle'] = specialchars($objSubpages->pageTitle, true);
				$row['link'] = $objSubpages->title;
				$row['href'] = $href;
				$row['nofollow'] = (strncmp($objSubpages->robots, 'noindex', 7) === 0);
				$row['target'] = '';
				$row['description'] = str_replace(array("\n", "\r"), array(' ' , ''), $objSubpages->description);

				// Override the link target
				if ($objSubpages->type == 'redirect' && $objSubpages->target)
				{
					$row['target'] = ($objPage->outputFormat == 'xhtml') ? ' onclick="return !window.open(this.href)"' : ' target="_blank"';
				}

				$items[] = $row;

			}
		}

		// Add classes first and last
		if (!empty($items))
		{
			$last = count($items) - 1;

			$items[0]['class'] = trim($items[0]['class'] . ' first');
			$items[$last]['class'] = trim($items[$last]['class'] . ' last');
		}

		$objTemplate->items = $items;
		return !empty($items) ? $objTemplate->parse() : '';
	}

}

?>