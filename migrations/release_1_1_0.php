<?php
/**
 *
 * Edit Log
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace towen\editlog\migrations;

class release_1_1_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['towen_editlog_version']);
	}

	static public function depends_on()
	{
		return array('\towen\editlog\migration\relase_1_0_0');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('towen_editlog_version', '1.1.0')),
		);
	}
}
