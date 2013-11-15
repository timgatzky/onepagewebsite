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
);


?>