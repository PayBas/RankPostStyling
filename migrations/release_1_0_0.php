<?php
/**
 *
 * @package Rank Post Styling Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace paybas\rankpoststyling\migrations;

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
