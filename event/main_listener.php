<?php
/**
 *
 * Edit Log
 * @copyright (c) 2016 towen - [towenpa@gmail.com]
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace towen\editlog\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
{
    static public function getSubscribedEvents()
    {
        return array(
            'core.user_setup' => 'load_language_on_setup',
            'core.permissions' => 'add_permission',
            'core.delete_posts_in_transaction_before' => 'delete_posts',
            'core.modify_submit_post_data' => 'modify_submit_post_data',
            'core.viewtopic_post_rowset_data' => 'viewtopic_post_rowset_data',
            'core.viewtopic_modify_post_row' => 'viewtopic_modify_post_row',
            'core.posting_modify_template_vars' => 'posting_modify_template_vars',
            'core.submit_post_modify_sql_data' => 'post_modify_sql_data',
        );
    }

    /* @var \phpbb\auth\auth */
    protected $auth;

    /* @var \phpbb\controller\helper */
    protected $helper;

    /* @var \phpbb\db\driver\driver_interface */
    protected $db;

    /* @var \phpbb\request\request */
    private $request;

    /* @var \phpbb\user */
    protected $user;

    /* @var string */
    protected $table;

    /**
     * Constructor
     *
     * @param \phpbb\auth\auth          $auth
     * @param \phpbb\controller\helper  $helper
     * @param \phpbb\db\driver\driver_interface   $db
     * @param \phpbb\request\request    $request
     * @param \phpbb\user               $user
     * @param string                    $table
     */
    public function __construct(\phpbb\auth\auth $auth, \phpbb\controller\helper $helper, \phpbb\db\driver\driver_interface $db,
                                \phpbb\request\request $request, \phpbb\user $user, $table)
    {
        $this->auth = $auth;
        $this->helper = $helper;
        $this->db = $db;
        $this->request = $request;
        $this->user = $user;
        $this->table = $table;
    }

    public function load_language_on_setup($event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = array(
            'ext_name' => 'towen/editlog',
            'lang_set' => 'common',
        );
        $event['lang_set_ext'] = $lang_set_ext;
    }

    public function add_permission($event)
    {
        $permissions = $event['permissions'];
        $permissions['u_no_editlog'] = array('lang' => 'ACL_U_NO_EDITLOG', 'cat' => 'misc');
        $permissions['m_view_editlog'] = array('lang' => 'ACL_M_VIEW_EDITLOG', 'cat' => 'post_actions');
        $permissions['m_delete_editlog'] = array('lang' => 'ACL_M_DELETE_EDITLOG', 'cat' => 'post_actions');
        $event['permissions'] = $permissions;
    }

    public function delete_posts($event)
    {
        $table_ary = $event['table_ary'];
        $table_ary[] = $this->table;
        $event['table_ary'] = $table_ary;
    }

    public function modify_submit_post_data($event)
    {
        $data = $event['data'];
        $data['post_edit_reason'] = !empty($data['post_edit_reason']) ? $data['post_edit_reason'] : ' ';
        $event['data'] = $data;
    }

  	public function viewtopic_post_rowset_data($event)
  	{
  		$rowset_data = $event['rowset_data'];
  		$rowset_data['post_edit_log'] = $event['row']['post_edit_log'];
  		$event['rowset_data'] = $rowset_data;
  	}

    public function viewtopic_modify_post_row($event)
    {
        $post_row = $event['post_row'];

        if ($event['row']['post_edit_log'] && $this->auth->acl_get('m_view_editlog', $event['row']['forum_id']))
        {
            $url = $this->helper->route('towen_editlog_controller', array('post_id' => $post_row['POST_ID']));
            $post_row['EDITED_MESSAGE'] .= $this->user->lang('VIEW_EDIT_LOG', $url);
        }

        $event['post_row'] = $post_row;
    }

    public function posting_modify_template_vars($event)
    {
        $page_data = $event['page_data'];
        $page_data['S_NO_EDIT_LOG'] = $this->auth->acl_get('u_no_editlog');
        $event['page_data'] = $page_data;
    }

    public function post_modify_sql_data($event)
    {
        if (in_array($event['post_mode'], array('edit', 'edit_first_post', 'edit_last_post', 'edit_topic')))
        {
            $sql_data = $event['sql_data'];

            if ($this->request->variable('no_edit_log', false) && $this->auth->acl_get('u_no_editlog'))
            {
                unset($sql_data[POSTS_TABLE]['sql']['post_edit_time']);
                unset($sql_data[POSTS_TABLE]['sql']['post_edit_reason']);
                unset($sql_data[POSTS_TABLE]['sql']['post_edit_user']);

                $delete_key = array_search('post_edit_count = post_edit_count + 1', $sql_data[POSTS_TABLE]['stat']);
                unset($sql_data[POSTS_TABLE]['stat'][$delete_key]);

            }
            else
            {
                $sql = 'SELECT post_text, bbcode_uid, post_edit_reason, post_edit_user, post_edit_time, post_subject
                 FROM ' . POSTS_TABLE . "
                 WHERE post_id = {$event['data']['post_id']}";

                $result = $this->db->sql_query($sql);
                $old_post = $this->db->sql_fetchrow($result);
                $this->db->sql_freeresult($result);

                $sql_data[POSTS_TABLE]['sql']['post_edit_reason'] = trim($sql_data[POSTS_TABLE]['sql']['post_edit_reason']);
                $sql_data[POSTS_TABLE]['sql']['post_edit_log'] = true;

  decode_message($old_post['post_text'], $old_post['bbcode_uid']);

  $insert_array = array(
    'post_id'	=> $event['data']['post_id'],
    'user_id'	=> $old_post['post_edit_user'],
    'old_text'	=> $old_post['post_text'],
    'old_subject'	=> $old_post['post_subject'],
    'edit_reason'	=> $old_post['post_edit_reason'],
    'edit_time'	=> $old_post['post_edit_time'],
  );

  $sql = 'INSERT INTO ' . $this->table . ' ' . $this->db->sql_build_array('INSERT', $insert_array);
  $this->db->sql_query($sql);
            }

            $event['sql_data'] = $sql_data;
        }
    }

}
