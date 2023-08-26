<?php
/**
 * Blugento Feeds
 * Model Class
 *
 * Copyright (C) 2015-2016 Blugento <contact@blugento.com>
 * LICENSE: GNU General Public License for more details <http://opensource.org/licenses/gpl-license.php>
 *
 * @package Blugento_Adminmenu
 * @author Simona Trifan <simona.plesuvu@mindmagnetsoftware.com>
 * @link http://www.blugento.com
 */

class Blugento_Feeds_Model_Feed_Save_Provider_GaCsv extends Blugento_Feeds_Model_Feed_Save_Abstract
{
	/**
	 * Definition filename
	 * @var string
	 */
	protected $_definitionFilename = 'ga.csv';
	
	/**
	 * Save CSV file
	 * @var bool
	 */
	protected $_saveAsCSV = true;
}
