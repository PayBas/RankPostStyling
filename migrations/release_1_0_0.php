<?php

/**
*
* @package Breadcrumb Menu Extension
* @copyright (c) 2014 PayBas
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace paybas\rankpoststyling\migrations;

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
    exit;
}

class release_1_0_0 extends \phpbb\db\migration\migration
{
	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'ranks' => array(
					'rank_style' => array('VCHAR:255', ''),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'ranks' => array(
					'rank_style',
				),
			),
		);
	}
}
