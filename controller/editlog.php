<?php
/**
 *
 * Edit Log
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace towen\editlog\controller;
use phpbb\exception\http_exception;

class editlog
{
    /* @var \phpbb\auth\auth */
    protected $auth;

    /* @var \phpbb\config\config */
    protected $config;

    /* @var \phpbb\controller\helper */
    protected $helper;

    /* @var \phpbb\db\driver\driver_interface */
    protected $db;

    /* @var \phpbb\log\log */
    protected $log;

    /* @var \phpbb\request\request */
    private $request;

    /* @var \phpbb\template\template */
    protected $template;

    /* @var \phpbb\user */
    protected $user;

    /* @var string phpBB root path */
    protected $root_path;

    /* @var string phpEx */
    protected $php_ext;

    /* @var string */
    protected $table;

    /**
     * Constructor
     *
     * @param \phpbb\auth\auth $auth
     * @param \phpbb\config\config $config
     * @param \phpbb\controller\helper $helper
     * @param \phpbb\db\driver\driver_interface $db
     * @param \phpbb\log\log $log
     * @param \phpbb\request\request $request
     * @param \phpbb\template\template $template
     * @param \phpbb\user $user
     * @param string $root_path
     * @param string $php_ext
     * @param string $table
     *
     */
    public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\controller\helper $helper,
                                \phpbb\db\driver\driver_interface $db, \phpbb\log\log $log, \phpbb\request\request $request,
                                \phpbb\template\template $template, \phpbb\user $user,
                                $root_path, $php_ext, $table)
    {
        $this->auth = $auth;
        $this->config = $config;
        $this->helper = $helper;
        $this->db = $db;
        $this->log = $log;
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;
        $this->root_path = $root_path;
        $this->php_ext = $php_ext;
        $this->table = $table;
    }

    /**
     *
     * @param $post_id
	 * @throws \phpbb\exception\http_exception
     * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
     */
    public function handle($post_id)
    {
        $post_id = (int) $post_id;

        $sql = 'SELECT forum_id, topic_id, post_subject FROM ' . POSTS_TABLE . "
            WHERE post_edit_count <> 0 AND post_id = {$post_id}";
        $result = $this->db->sql_query_limit($sql, 1);
        $row = $this->db->sql_fetchrow();
        $this->db->sql_freeresult($result);

        if (empty($row))
        {
            throw new http_exception(404, 'NO_TOPIC');
        }

        $forum_id = $row['forum_id'];
        $topic_id = $row['topic_id'];
        $post_subject = censor_text($row['post_subject']);
        $post_url = append_sid("{$this->root_path}viewtopic.{$this->php_ext}", "f={$forum_id}&amp;t={$topic_id}&amp;p={$post_id}#p{$post_id}");
        $u_action = $this->helper->route('towen_editlog_controller', array('post_id' => $post_id));

        if (!$this->auth->acl_get('m_view_editlog', $forum_id))
        {
            throw new http_exception(401, 'EDITLOG_NO_AUTH', array($post_url));
        }

        // ACTION: compare
        if ($this->request->is_set_post('compare'))
        {
            $options = $this->request->variable('option', array(0=>0));

			if (count($options) != 2)
			{
				$content = $this->user->lang['EDITLOG_BAD_OPTIONS_COUNT'];
			}
			else
			{
				// -1 is the message in the posts table
				if (in_array(-1, $options))
				{
					sort($options);

					$sql = 'SELECT post_text, bbcode_uid
							FROM ' . POSTS_TABLE . "
							WHERE post_id = {$post_id}";
					$result = $this->db->sql_query($sql);
					$row = $this->db->sql_fetchrow($result);
					$this->db->sql_freeresult($result);

					decode_message($row['post_text'], $row['bbcode_uid']);
					$new_text = $row['post_text'];
				}
				else
				{
					rsort($options);

					$sql = 'SELECT old_text
							FROM ' . $this->table . "
							WHERE edit_id = {$options[0]} AND post_id = {$post_id}";
					$result = $this->db->sql_query($sql);
					$new_text = $this->db->sql_fetchfield('old_text');
					$this->db->sql_freeresult($result);
				}

				$sql = 'SELECT old_text
						FROM ' . $this->table . "
						WHERE edit_id = {$options[1]} AND post_id = {$post_id}";
				$result = $this->db->sql_query($sql);
				$old_text = $this->db->sql_fetchfield('old_text');
				$this->db->sql_freeresult($result);

				if (!$old_text || !$new_text)
				{
					throw new http_exception(404, 'NO_POST_LOG', array($post_url));
				}

				if ($old_text == $new_text)
				{
					$content = html_entity_decode($old_text);
				}
				else
				{
					include($this->root_path . 'includes/diff/diff.' . $this->php_ext);
					include($this->root_path . 'includes/diff/engine.' . $this->php_ext);
					include($this->root_path . 'includes/diff/renderer.' . $this->php_ext);

					$diff = new \diff($old_text, $new_text);
					$renderer = new \diff_renderer_inline();

					$content = nl2br($renderer->render($diff));
					$content = html_entity_decode($content);
				}

				$this->template->assign_vars(array(
					'OLD_POST' => $options[1],
					'NEW_POST' => $options[0],
				));
			}

            $this->template->assign_var('CONTENT', $content);
        }

        // ACTION: delete
        if ($this->request->is_set_post('delete'))
        {
            if (!$this->auth->acl_get('m_delete_editlog', $forum_id))
            {
                throw new http_exception(401, 'EDITLOG_NO_DELETE_AUTH', array($u_action));
            }
            $edit_id_list = $this->request->variable('option', array(0=>0));

            if (sizeof($edit_id_list))
            {
                if (confirm_box(true))
                {
                    $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->db->sql_in_set('edit_id', $edit_id_list);
                    $this->db->sql_query($sql);

                    $log_array = array(
                        'forum_id' => $forum_id,
                        'topic_id' => $topic_id,
                        $post_url, $post_subject,
                    );
                    $this->log->add('mod', $this->user->data['user_id'], $this->user->data['user_ip'], 'LOG_EDITLOG_DELETE_SUCCESS', false, $log_array);

					$sql = "SELECT count(edit_id) as edit_count FROM {$this->table} WHERE post_id = {$post_id}";
					$result = $this->db->sql_query_limit($sql, 1);
					$edit_count = (int) $this->db->sql_fetchfield('edit_count');
					$this->db->sql_freeresult($result);

					if ($edit_count === 0) {
						$sql = 'UPDATE ' . POSTS_TABLE . " SET post_edit_log = 0 WHERE post_id = {$post_id}";
						$this->db->sql_query($sql);

						$u_action = $post_url;
					}
                    throw new http_exception(200, 'EDITLOG_DELETE_SUCCESS', array($u_action));
                }
                else
                {
                    confirm_box(false, $this->user->lang('CONFIRM_OPERATION'), build_hidden_fields(array(
                        'option'	=> $edit_id_list,
                        'delete'		=> true,
                    )));
                }
            }
        }

		// ACTION: show list

		$sql_array = array(
			'SELECT' => 'p.post_edit_time, p.post_edit_reason, p.post_edit_user, p.post_subject, p.post_time, u.username,
            	u.user_colour, u2.user_id as p_user_id, u2.username as p_username, u2.user_colour as p_user_colour',
			'FROM' => array(
				POSTS_TABLE => 'p',
			),
			'LEFT_JOIN' => array(
				array(
					'FROM' => array(USERS_TABLE => 'u'),
					'ON' => 'p.post_edit_user = u.user_id',
				),
				array(
					'FROM' => array(USERS_TABLE => 'u2'),
					'ON' => 'p.poster_id = u2.user_id',
				),
			),
			'WHERE' => "p.post_id = {$post_id}",
		);

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$original = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		$sql_array = array(
            'SELECT' => 'e.edit_id, e.user_id, e.edit_time, e.edit_reason, e.old_subject, u.username, u.user_colour',
            'FROM' => array(
                POSTS_TABLE => 'p',
                $this->table => 'e',
            ),
            'LEFT_JOIN' => array(
                array(
                    'FROM' => array(USERS_TABLE => 'u'),
                    'ON' => 'e.user_id = u.user_id',
                ),
            ),
            'WHERE' => "e.post_id = {$post_id} AND e.post_id = p.post_id",
        );

        $sql = $this->db->sql_build_query('SELECT', $sql_array);
        $result = $this->db->sql_query($sql);

        $post_have_log = false;

        while ($row = $this->db->sql_fetchrow($result))
        {
			if (!$post_have_log)
			{
				$edit_array = array(
					'EDIT_TIME' => $this->user->format_date($original['post_time']),
					'EDIT_REASON' => "<strong>{$this->user->lang['ORIGINAL_MESSAGE']}</strong>",
					'USERNAME' => get_username_string('full', $original['p_user_id'], $original['p_username'], $original['p_user_colour']),
				);
			}
			else
			{
				$edit_array = array(
					'EDIT_TIME' => $this->user->format_date($row['edit_time']),
					'EDIT_REASON' => $row['edit_reason'],
					'USERNAME' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
				);
			}

            $this->template->assign_block_vars('edit', array_merge($edit_array, array(
                'EDIT_ID' => $row['edit_id'],
				'OLD_SUBJECT'  => $row['old_subject'],
            )));

			$post_have_log = true;
        }
        $this->db->sql_freeresult($result);

		$this->template->assign_block_vars('edit', array(
			'EDIT_ID' => -1,
			'EDIT_TIME' => $this->user->format_date($original['post_edit_time']),
			'EDIT_REASON' => $original['post_edit_reason'],
			'OLD_SUBJECT' => $original['post_subject'],
			'USERNAME' => get_username_string('full', $original['post_edit_user'], $original['username'], $original['user_colour']),
		));

		if (!$post_have_log)
        {
            throw new http_exception(404, 'NO_POST_LOG', array($post_url));
        }

        // build navlinks
        $sql = 'SELECT forum_id, forum_type, forum_name, forum_desc, forum_desc_uid, forum_desc_bitfield,
            forum_desc_options, forum_options, parent_id, forum_parents, left_id, right_id FROM ' . FORUMS_TABLE . "
            WHERE forum_id = {$forum_id}";
        $result = $this->db->sql_query_limit($sql, 1);
        $forum_data = $this->db->sql_fetchrow();
        $this->db->sql_freeresult($result);

        if (!function_exists('generate_forum_nav'))
        {
            include($this->root_path . 'includes/functions_display.' . $this->php_ext);
        }
        \generate_forum_nav($forum_data);

        $this->template->assign_vars(array(
            'POST_SUBJECT' => $post_subject,
            'U_POST' => $post_url,
            'U_ACTION' => $u_action,
            'S_DELETE' => $this->auth->acl_get('m_delete_editlog', $forum_id),
        ));

        return $this->helper->render('editlog_body.html', $this->user->lang['EDIT_LOG']);
    }
}
