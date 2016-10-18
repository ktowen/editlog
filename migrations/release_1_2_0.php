<?php
/**
 *
 * Edit Log
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace towen\editlog\migrations;

class release_1_2_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['towen_editlog_version']) && version_compare($this->config['towen_editlog_version'], '1.2.0', '>=');
	}

	static public function depends_on()
	{
		return array('\towen\editlog\migrations\release_1_1_1');
	}

	public function update_data()
	{
		return array(
			array('config.update', array('towen_editlog_version', '1.2.0')),

			array('permission.permission_set', array('ROLE_MOD_FULL', 'm_delete_editlog')),
			array('permission.permission_set', array('ROLE_MOD_FULL', 'm_view_editlog')),
			array('permission.permission_set', array('ROLE_MOD_QUEUE', 'm_view_editlog')),
			array('permission.permission_set', array('ROLE_MOD_SIMPLE', 'm_view_editlog')),
			array('permission.permission_set', array('ROLE_MOD_STANDARD', 'm_view_editlog')),
			array('permission.permission_set', array('ROLE_USER_FULL', 'u_no_editlog')),

		);
	}
	public function update_schema()
	{
		return array(
			'add_columns'        => array(
				$this->table_prefix . 'posts'        => array(
					'post_edit_log'    => array('BOOL', 0, 'after' => 'post_edit_user'),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns'        => array(
				$this->table_prefix . 'posts'        => array(
					'post_edit_log',
				),
			),
		);
	}
}
