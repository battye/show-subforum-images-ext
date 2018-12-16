<?php
/**
 *
 * Show Subforum Images. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, battye
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace battye\subforumimages\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\db\driver\driver;
use phpbb\db\driver\driver_interface;

/**
 * Show Subforum Images listener.
 */
class main_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.display_forums_modify_template_vars' => 'display_forums_modify_template_vars',
		);
	}

	/**
	 * @var driver
	 */
	private $db;

	/**
	 * Constructor
	 *
	 * @param driver_interface $db
	 */
	public function __construct(driver_interface $db)
	{
		$this->db = $db;
	}

	/**
	 * Assign subforum.U_SUBFORUM_IMAGE template variable
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function display_forums_modify_template_vars($event)
	{
		if (is_array($event['subforums_row']) && sizeof($event['subforums_row']))
		{
			$forum_ids = array();
			$subforums = $event['subforums_row'];

			// Save references so we don't have to do a double loop
			$subforum_references = array();

			// Extract the forum id from the subforum URL (the ?f=x part of the string)
			foreach ($subforums as $subforum)
			{
				$query_string = parse_url($subforum['U_SUBFORUM'], PHP_URL_QUERY);
				parse_str($query_string, $output);
				$forum_id = (int) $output['f'];

				$subforum_references[$forum_id] = $subforum;
				$forum_ids[] = $forum_id;
			}

			// Get the images
			$forum_images = $this->get_subforum_image($forum_ids);

			// Add the subforum image to the template array
			foreach ($forum_images as $forum_id => $forum_image)
			{
				$subforum_references[$forum_id]['U_SUBFORUM_IMAGE'] = $forum_image;
			}

			$event['subforums_row'] = array_values($subforum_references);
		}
	}

	/**
	 * Get the forum images associated with a forum id
	 * @param $forum_ids
	 * @return array
	 */
	private function get_subforum_image($forum_ids)
	{
		$forum_images = array();

		$sql = 'SELECT forum_id, forum_image
				FROM ' . FORUMS_TABLE . '
				WHERE ' . $this->db->sql_in_set('forum_id', $forum_ids);

		$result = $this->db->sql_query($sql);

		while ($row = $this->db->sql_fetchrow($result))
		{
			// Save the forum image as a key value pair
			$forum_images[$row['forum_id']] = $row['forum_image'];
		}

		$this->db->sql_freeresult($result);

		// Return the images
		return $forum_images;
	}
}
