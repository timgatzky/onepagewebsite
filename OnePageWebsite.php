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

/**
 * core class OnePageWebsite
 * provids various functions
 */
class OnePageWebsite extends Backend
{
	protected $arrPageData = array();
	protected $arrPages = array();
	
	
	public function __set($strKey, $varValue)
	{
		switch($strKey)
		{
			case 'hardLimit':
				$this->hardLimit = $varValue;
				break;
			case 'showLevel':
				$this->showLevel = $varValue;
				break;
		}
	}
	
	public function __get($strKey)
	{
		
	}
	
	/**
	 * Get page data / layout, replace article placeholders with articles and return as array with page id key
	 * @param array
	 * @return array
	 */
	protected function getPageData($arrPages)
	{
		$arrPageData = $this->getModulesInPageLayouts($arrPages);
		
		if(count($arrPageData) < 1)
		{
			return '';
		}
		
		// insert articles in placeholders in modules array
		foreach ($arrPageData as $pageId => $sections)
		{
			foreach($sections as $column => $itemList)
			{
				// replace article placeholders with articles
				foreach($itemList as $index => $item)
				{
					if($item[0] == 'article_placeholder')
					{
						$arrArticles = $this->getArticles($pageId, $column);
						array_insert($arrPageData[$pageId][$column],$index,$arrArticles);
						
						// delete placeholder
						$newIndex = $index + count($arrArticles);
						unset($arrPageData[$pageId][$column][$newIndex]);
					}
				}
			}
		}
		
		return $arrPageData;
	}
	
	
	/**
	 * Shortcut to getPageData: returns just the data as array
	 * @param integer
	 * @return array
	 */
	protected function getSinglePageData($intPage)
	{
		$arrReturn = $this->getPageData(array($intPage));
		return $arrReturn[$intPage];
	}
	
	/**
	 * Shortcut to generatePagesRecursiv
	 * @param integer
	 * @param integer
	 * @return string
	 */
	public function generatePage($pid,$level,$strTemplate='')
	{
		return $this->generatePagesRecursiv($pid,$level,$strTemplate='');
	}
		
	/**
	 * Render recursiv pages and return content as string
	 * @param integer
	 * @param integer
	 * @return string
	 */
	public function generatePagesRecursiv($pid,$level=1,$strTemplate='')
	{
		global $objPage;
		$time = time();
		$level++;
		
		$strWhereP1="p1.published=1 AND p1.opw_hide!=1 AND p1.type='regular' AND (p1.start='' OR p1.start<".$time.") AND (p1.stop='' OR p1.stop>".$time.")";
		$strWhereP2="p2.published=1 AND p2.opw_hide!=1 AND p2.type='regular' AND (p2.start='' OR p2.start<".$time.") AND (p2.stop='' OR p2.stop>".$time.")";

		// fetch subpages
		$objSubpages = $this->Database->prepare("SELECT p1.*, (SELECT COUNT(*) FROM tl_page p2 WHERE p2.pid=p1.id AND ".$strWhereP2.") AS subpages FROM tl_page p1 WHERE p1.pid=? AND ".$strWhereP1." ORDER BY p1.sorting")
										->execute($pid);
		
		if($objSubpages->numRows < 1)
		{
			return '';
		}
		else if($this->hardLimit && $this->showLevel > 0 && $level > $this->showLevel)
		{
			return '';
		}
		
		if($strTemplate == '')
		{
			$strTemplate = 'opw_default';
		}
		
		$objTemplate = new FrontendTemplate($strTemplate);
		$objTemplate->type = get_class($this);
		$objTemplate->level = 'level_' . $level;
		
		$items = array();
		$count = 0;
		
		// walk subpages
		while($objSubpages->next())
		{
			// Skip hidden sitemap pages
			if ($this instanceof ModuleSitemap && $objSubpages->sitemap == 'map_never')
			{
				continue;
			}
			
			$subpages = '';
			
			// do the same as the navigation here
			if ($objSubpages->subpages > 0 && (!$this->showLevel || $this->showLevel >= $level || (!$this->hardLimit && ($objPage->id == $objSubpages->id || in_array($objPage->id, $this->getChildRecords($objSubpages->id, 'tl_page'))))))
			{
				$subpages = $this->generatePagesRecursiv($objSubpages->id, $level);
			}
			
			$strClass = ' page page_' . $count;
			$strClass .= (($subpages != '') ? ' subpage' : '') . ($objSubpages->protected ? ' protected' : '') . (($objSubpages->cssClass != '') ? ' ' . $objSubpages->cssClass : '');
			$strCssId = 'page'.$objSubpages->id;
			
			$items[] = array
			(
				'id'			=> $objSubpages->id,
				'cssId'			=> 'id="'.$strCssId.'"',
				'class'			=> trim($strClass),
				'subpages'		=> $subpages,
				'content'		=> $this->getSinglePageData($objSubpages->id),#$this->arrPageData[$objSubpages->id],
				'row'			=> $objSubpages->row()
			);
			
			$count++;
		}
		
		if(empty($items))
		{
			return '';
		}
		
		// add class first and last
		$last = count($items) - 1;
		$items[0]['class'] = trim($items[0]['class'] . ' first');
		$items[$last]['class'] = trim($items[$last]['class'] . ' last');
		
		
		// HOOK allow custom page data
		if (isset($GLOBALS['TL_HOOKS']['ONE_PAGE_WEBSITE']['generatePage']) && count($GLOBALS['TL_HOOKS']['ONE_PAGE_WEBSITE']['generatePage']))
		{
			foreach ($GLOBALS['TL_HOOKS']['ONE_PAGE_WEBSITE']['generatePage'] as $callback)
			{
				$this->import($callback[0]);
				$items = $this->$callback[0]->$callback[1]($items, $this);
			}
		}
		
		$objTemplate->entries = $items;
				
		// parse template
		$strBuffer = '';
		$strBuffer = $objTemplate->parse();
		
		return $strBuffer;
	}
	
	
	/**
	 * Get ids of parent records and return as array
	 * @param string
	 * @param integer
	 * @return array
	 */
	protected function getParentRecords($strTable, $intId)
	{
		$arrParent = array();
	
		do
		{
			// Get the pid
			$objParent = $this->Database->prepare("SELECT pid FROM " . $strTable . " WHERE id=?")
										->limit(1)
										->execute($intId);
	
			if ($objParent->numRows < 1)
			{
				break;
			}
	
			$intId = $objParent->pid;
	
			// store id
			$arrParent[] = $intId;
	
		}
		while ($intId);
	
		if (empty($arrParent))
		{
			return array();
		}
		
		return $arrParent;
	}
	
	/**
	 * Get layout object
	 * @param integer
	 * @return object
	 */
	protected function getPageLayout($intPage)
	{
		// global page object
		global $objPage;	
		
		
		// fetch layout, either selected manually or by fallback (default layout) 
		$objLayout = $this->Database->prepare("SELECT * FROM tl_layout WHERE id=(SELECT layout FROM tl_page WHERE id=? AND includeLayout=1)")
									->limit(1)
									->execute($objPages->id);
		
		// fix: #1
		// if neither one is available search parent pages for manually selected layouts
		if($objLayout->numRows < 1)
		{
			// get parent ids
			$arrParents = $this->getParentRecords('tl_page',$intPage);
			
			$tmp = array();
			foreach($arrParents as $id)
			{
				if($id > 0 && $id != $objPage->rootId)
				{
					$tmp[] = $id;
				}
			}
			$arrParents = $tmp;
			unset($tmp);
			
			// move on to next page
			if(count($arrParents) < 1)
			{
				continue;
			}
			
			// walk parents backwards to find an inherited layout
			$arrParents = array_reverse($arrParents);
			
			// fetch parent pages
			$objParents = $this->Database->prepare("SELECT * FROM tl_page WHERE id IN(".implode(',',$arrParents).")")
							->execute();
			if($objParents->numRows < 1)
			{
				continue;
			}
			
			while($objParents->next())
			{
				$objLayout = $this->Database->prepare("SELECT * FROM tl_layout WHERE id=(SELECT layout FROM tl_page WHERE id=? AND includeLayout=1)")
									->limit(1)
									->execute($objParents->id);
				if($objLayout->numRows < 1)
				{
					// check next parent
					continue;
				}
			}
		}
		
		
		// return if no layout is selected or inherited to this page
		if($objLayout->numRows < 1)
		{
			// try the default page layout
			$objLayout = $this->Database->prepare("SELECT * FROM tl_layout WHERE fallback=1")
									->limit(1)
									->execute();
			
			if($objLayout->numRows < 1)
			{
				return '';
			}
		}
				
		
		return $objLayout;
	}
	
	
	
	/**
	 * Get modules included in pages and return as array with page id as key
	 * @param array
	 * @return array
	 */
	protected function getModulesInPageLayouts($arrPages)
	{
		if(!count($arrPages))
		{
			return array();
		}
		else if(!is_array($arrPages))
		{
			$arrPages = array($arrPages);
		}
		
		// global page object
		global $objPage;	
		
		// get Database Result object for all pages
		$objPages = $this->Database->execute("SELECT * FROM tl_page WHERE id IN(".implode(',',$arrPages).")");

		if($objPages->numRows < 1)
		{
			return array();
		}

		// walk pages
		while($objPages->next())
		{
			$objLayout = $this->getPageLayout($objPages->id);
						
			$index = $objPages->id;
			while($objLayout->next())
			{
				foreach(deserialize($objLayout->modules) as $module)
				{
					$id = $module['mod'];
					$col = $module['col'];

					// make sure no modules of type one-page-website will be registered
					$objModule = $this->Database->prepare("SELECT * FROM tl_module WHERE id=? AND type NOT IN(?)")
												->limit(1)
												->execute($id, implode(',',array_keys($GLOBALS['FE_MOD']['onepagewebsite'])) );

					if($id == 0 || $objModule->numRows < 1)
					{
						// add a placeholder for articles
						$arrModules[$index][$col][] = array('article_placeholder', $col);
						continue;
					}
					
					#$strHtml = $this->getFrontendModule($module['mod'], $module['col']);
					$strHtml = $this->replaceInsertTags('{{insert_module::'.$id.'}}');
					
					$arrModules[$index][$col][] = array
					(
						'id' 		=> $id,
						'col'		=> $col,
						'page'		=> $objPages->id,
						'layout'	=> $objLayout->id,
						'html'  	=> $strHtml,
						'row'  		=> $objModule->row(),
					);

				}
			}
		}
		
		return $arrModules;
	}


	/**
	 * Get articles on pages and return as array with page id as key
	 * @param array
	 * @return array
	 */
	public function getArticles($arrPages,$strColumn='')
	{
		if(!is_array($arrPages))
		{
			$arrPages = array($arrPages);
		}

		$time = time();
		$strWhere="published=1 AND (start='' OR start<".$time.") AND (stop='' OR stop>".$time.")" . ($strColumn ? " AND inColumn='".$strColumn."'" : "");

		$objArticles = $this->Database->execute("SELECT * FROM tl_article WHERE pid IN(".implode(',', $arrPages).") AND " . $strWhere . " ORDER BY sorting");

		if($objArticles->numRows < 1)
		{
			return array();
		}

		$arrReturn = array();
		while($objArticles->next())
		{
			// fix 2: generate the whole article section. The inserttag only generates the content. 
			#$strHtml = $this->replaceInsertTags('{{insert_article::'.$objArticles->id.'}}');
			
			$objRow = $this->Database->prepare("SELECT * FROM tl_article WHERE id=?")->limit(1)->execute($objArticles->id);
			
			// mimic module article
			$tmp = new ModuleArticle($objRow);
			$strHtml = $tmp->generate(false);
			
			// handle empty articles
			if(!strlen($strHtml))
			{
				// generate an empty article
				$objArticleTpl = new FrontendTemplate('mod_article');
				$objArticleTpl->class = 'mod_article';
				$objArticleTpl->elements = array();
				$strHtml = $objArticleTpl->parse();
			}

			$arrReturn[] = array
			(
				'id'   => $objArticles->id,
				'pid'   => $objArticles->pid,
				'col'  => $objArticles->inColumn,
				'html'   => $strHtml,
			);
		}

		return $arrReturn;
	}
	
	
	/**
	 * Shortcut: Get subpages recursiv
	 * @param integer
	 * @return array
	 */
	public function getSubpages($pid)
	{
		return $this->getSubpagesRecursiv($pid);
	}
	
	/**
	 * Recursivley get all subpages of a given pages
	 * @param array
	 * @param string
	 * @param integer
	 * @param array
	 * @return array
	 */
	protected function getSubpagesRecursiv($pid,$level=1,$arrReturn=array())
	{
		global $objPage;
		$time = time();
		$level++;

		$strWhereP1="p1.published=1 AND p1.opw_hide!=1 AND p1.type='regular' AND (p1.start='' OR p1.start<".$time.") AND (p1.stop='' OR p1.stop>".$time.")";
		$strWhereP2="p2.published=1 AND p2.opw_hide!=1 AND p2.type='regular' AND (p2.start='' OR p2.start<".$time.") AND (p2.stop='' OR p2.stop>".$time.")";

		// fetch subpages
		$objSubpages = $this->Database->prepare("SELECT p1.*, (SELECT COUNT(*) FROM tl_page p2 WHERE p2.pid=p1.id AND ".$strWhereP2.") AS subpages FROM tl_page p1 WHERE p1.pid=? AND ".$strWhereP1." ORDER BY p1.sorting")
										->execute($pid);
			
		if($objSubpages->numRows < 1)
		{
			return array();
		}
		
		if($this->hardLimit && $this->showLevel > 0 && $level > $this->showLevel)
		{
			return array();
		}
		
		// walk subpages
		while($objSubpages->next())
		{
			// Skip hidden sitemap pages
			if ($this instanceof ModuleSitemap && $objSubpages->sitemap == 'map_never')
			{
				continue;
			}
			
			$this->arrPages[] = $objSubpages->id;
			$this->getSubpagesRecursiv($objSubpages->id, $level);
			
		}
		return $this->arrPages;
	}


#	/**
#	 * Filter pages
#	 * @param array
#	 * @return array
#	 */
#	private function getFilteredPageRecords($arrPages)
#	{
#		if(!count($arrPages))
#		{
#			return array();
#		}
#		
#		if(!is_array($arrPages))
#		{
#			$arrPages = array($arrPages);
#		}
#		
#		$time = time();
#		$strWhereP1="p1.published=1 AND p1.opw_hide!=1 AND p1.type='regular' AND (p1.start='' OR p1.start<".$time.") AND (p1.stop='' OR p1.stop>".$time.")";
#		$strWhereP2="p2.published=1 AND p2.opw_hide!=1 AND p2.type='regular' AND (p2.start='' OR p2.start<".$time.") AND (p2.stop='' OR p2.stop>".$time.")";
#		
#		// get unnested parent pages
#		$arrParents = $this->eliminateNestedPages($arrPages, 'tl_page', true);
#		
#		$arrReturn = array();
#		$arrSkipChilds = array();
#		
#		foreach($arrPages as $i => $id)
#		{
#			if(in_array($id, $arrSkipChilds))
#			{
#				continue;
#			}
#			
#			// filter page and check for published subpages
#			$objPage = $this->Database->prepare("SELECT p1.* FROM tl_page p1 WHERE p1.id=? AND " . $strWhereP1)
#							->limit(1)
#							->execute($id);
#			
#			if($objPage->numRows > 0)
#			{
#				// store current page
#				$arrReturn[] = $objPage->id;
#				// check for published subpages
#				$objSubpage = $this->Database->prepare("SELECT p1.*, (SELECT p2.id FROM tl_page p2 WHERE p2.pid=p1.id AND ".$strWhereP2.") AS subpage FROM tl_page p1 WHERE ".$strWhereP1)
#											->limit(1)
#											->execute($id);
#				if($objSubpage->subpage)
#				{
#					$next = $arrPages[$i+1];
#					// skip all the following childs if the next page is not a level_0 page (parent page)
#					if(!in_array($next, $arrParents))
#					{
#						$arrSkipChilds = array_merge(array($next), $this->getChildRecords($next,'tl_page'));
#					}
#				}
#			}
#		}
#
#		return $arrReturn;
#	}



}

?>