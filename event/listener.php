<?php
/**
 *
 * @package Rank Post Styling Extension
 * @copyright (c) 2015 PayBas
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace paybas\rankpoststyling\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\cache\service */
	protected $cache;

	/** @var \phpbb\request\request_interface */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	protected $ranks;

	public function __construct(\phpbb\cache\service $cache, \phpbb\request\request_interface $request, \phpbb\user $user)
	{
		$this->cache = $cache;
		$this->request = $request;
		$this->user = $user;

		$this->ranks = $this->cache->obtain_ranks();
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_ranks_save_modify_sql_ary'   => 'acp_ranks_save_modify_sql_ary',
			'core.acp_ranks_edit_modify_tpl_ary'   => 'acp_ranks_edit_modify_tpl_ary',
			'core.acp_ranks_list_modify_rank_row'  => 'acp_ranks_list_modify_rank_row',

			'core.viewtopic_cache_guest_data'      => 'viewtopic_cache_user',
			'core.viewtopic_cache_user_data'       => 'viewtopic_cache_user',
			'core.viewtopic_modify_post_row'       => 'viewtopic_modify_post',

			'core.memberlist_prepare_profile_data' => 'memberlist_prepare_profile',

			'core.search_get_posts_data'           => 'search_get_posts_data',
			'core.search_modify_tpl_ary'           => 'search_modify_tpl_ary',
		);
	}

	/* ACP */
	public function acp_ranks_save_modify_sql_ary($event)
	{
		$sql_ary = $event['sql_ary'];
		$sql_ary['rank_style'] = $this->request->variable('rank_style', '');
		$event['sql_ary'] = $sql_ary;
	}

	public function acp_ranks_edit_modify_tpl_ary($event)
	{
		$this->user->add_lang_ext('paybas/rankpoststyling', 'rankpoststyling');

		$tpl_ary = $event['tpl_ary'];
		$tpl_ary['RANK_STYLE'] = (isset($event['ranks']['rank_style'])) ? $event['ranks']['rank_style'] : '';
		$event['tpl_ary'] = $tpl_ary;
	}

	public function acp_ranks_list_modify_rank_row($event)
	{
		$this->user->add_lang_ext('paybas/rankpoststyling', 'rankpoststyling');

		$rank_row = $event['rank_row'];
		$rank_row['RANK_STYLE'] = (isset($event['row']['rank_style'])) ? $event['row']['rank_style'] : '';
		$event['rank_row'] = $rank_row;
	}

	/* Viewtopic */
	public function viewtopic_cache_user($event)
	{
		$user_cache_data = $event['user_cache_data'];
		$user_cache_data['rank_style'] = $this->get_rank_style($event['row']['user_rank']);
		$event['user_cache_data'] = $user_cache_data;
	}

	public function viewtopic_modify_post($event)
	{
		$post_row = $event['post_row'];
		$post_row['RANK_STYLE'] = $event['user_poster_data']['rank_style'];
		$event['post_row'] = $post_row;
	}

	/* Memberlist */
	public function memberlist_prepare_profile($event)
	{
		$template_data = $event['template_data'];
		$template_data['RANK_STYLE'] = $this->get_rank_style($event['data']['user_rank']);
		$event['template_data'] = $template_data;
	}

	/* Search */
	public function search_get_posts_data($event)
	{
		$array = $event['sql_array'];
		$array['SELECT'] .= ', u.user_rank';
		$event['sql_array'] = $array;
	}

	public function search_modify_tpl_ary($event)
	{
		if ($event['show_results'] == 'posts')
		{
			$tpl_ary = $event['tpl_ary'];
			$tpl_ary['RANK_STYLE'] = $this->get_rank_style($event['row']['user_rank']);
			$event['tpl_ary'] = $tpl_ary;
		}
	}

	/* Get the rank style */
	public function get_rank_style($user_rank)
	{
		$rank_style = '';

		if (!empty($user_rank))
		{
			$rank_style = (isset($this->ranks['special'][$user_rank]['rank_style'])) ? $this->ranks['special'][$user_rank]['rank_style'] : '';
		}

		return $rank_style;
	}
}
