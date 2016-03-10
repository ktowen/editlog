<?php
/**
 *
 * Edit Log
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace towen\editlog\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['towen_editlog_version']);
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\alpha2');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('towen_editlog_version', '1.0.0')),

            array('permission.add', array('u_no_editlog')),
            array('permission.add', array('m_view_editlog')),
            array('permission.add', array('m_view_editlog', false)),
            array('permission.add', array('m_delete_editlog')),
            array('permission.add', array('m_delete_editlog', false)),
		);
	}

    public function update_schema()
    {
        return array(
            'add_tables'	=> array(
                $this->table_prefix . 'editlog' => array(
                    'COLUMNS' => array(
                        'edit_id'           => array('UINT', NULL, 'auto_increment'),
                        'post_id'	        => array('UINT', 0),
                        'user_id'	        => array('UINT', 0),
                        'old_text'	        => array('MTEXT_UNI', ''),
                        'edit_time'	        => array('TIMESTAMP', 0),
                        'edit_reason'	    => array('STEXT_UNI', ''),
                    ),
                    'PRIMARY_KEY' => 'edit_id',
                    'KEYS' => array(
                        'post_id'   => array('INDEX', 'post_id'),
                    ),
                ),
            ),
        );
    }

    public function revert_schema()
    {
        return array(
            'drop_tables'    => array(
                $this->table_prefix . 'editlog',
            ),
        );
    }
}
