<?php
namespace chat\data\room;
use \chat\util\ChatUtil;
use \wcf\system\WCF;

/**
 * Executes chatroom-related actions.
 * 
 * @author 	Tim Düsterhus
 * @copyright	2010-2013 Tim Düsterhus
 * @license	Creative Commons Attribution-NonCommercial-ShareAlike <http://creativecommons.org/licenses/by-nc-sa/3.0/legalcode>
 * @package	be.bastelstu.chat
 * @subpackage	data.room
 */
class RoomAction extends \wcf\data\AbstractDatabaseObjectAction implements \wcf\data\ISortableAction {
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$className
	 */
	protected $className = '\chat\data\room\RoomEditor';
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsDelete
	 */
	protected $permissionsDelete = array('admin.chat.canDeleteRoom');
	
	/**
	 * @see	\wcf\data\AbstractDatabaseObjectAction::$permissionsUpdate
	 */
	protected $permissionsUpdate = array('admin.chat.canEditRoom');
	
	/**
	 * Resets cache if any of the listed actions is invoked
	 * @var	array<string>
	 */
	protected $resetCache = array('create', 'delete', 'toggle', 'update', 'updatePosition', 'prune');
	
	/**
	 * Fixes create to append new rooms.
	 */
	public function create() {
		$room = parent::create();
		
		WCF::getDB()->beginTransaction();
		$sql = "SELECT	MAX(position)
			FROM	".call_user_func(array($this->className, 'getDatabaseTableName'))."
			FOR UPDATE";
		$stmt = WCF::getDB()->prepareStatement($sql);
		$stmt->execute();

		$editor = new RoomEditor($room);
		$editor->update(array('position' => ($stmt->fetchColumn() + 1)));
		WCF::getDB()->commitTransaction();
		
		return $room;
	}
	
	/**
	 * Deletes temporary rooms that are unused.
	 * 
	 * @return	integer		Number of deleted rooms
	 */
	public function prune() {
		$sql = "SELECT
				".call_user_func(array($this->className, 'getDatabaseTableIndexName'))."
			FROM
				".call_user_func(array($this->className, 'getDatabaseTableName'))."
			WHERE
					permanent = ?
				AND 	roomID NOT IN (
					SELECT
						fieldValue AS roomID 
					FROM
						wcf".WCF_N."_user_storage
					WHERE
							field = ?
						AND	fieldValue IS NOT NULL
				)";
		$stmt = \wcf\system\WCF::getDB()->prepareStatement($sql);
		$stmt->execute(array(0, 'roomID'));
		$objectIDs = array();
		
		while ($objectIDs[] = $stmt->fetchColumn());
		
		return call_user_func(array($this->className, 'deleteAll'), $objectIDs);
	}
	
	/**
	 * @see wcf\data\ISortableAction::validateUpdatePosition()
	 */
	public function validateUpdatePosition() {
		// validate permissions
		if (is_array($this->permissionsUpdate) && count($this->permissionsUpdate)) {
			WCF::getSession()->checkPermissions($this->permissionsUpdate);
		}
		else {
			throw new \wcf\system\exception\PermissionDeniedException();
		}
		
		if (!isset($this->parameters['data']['structure'])) {
			throw new \wcf\system\exception\UserInputException('structure');
		}
	}
	
	/**
	 * @see wcf\data\ISortableAction::updatePosition()
	 */
	public function updatePosition() {
		$roomList = new RoomList();
		$roomList->readObjects();
		
		$i = 0;
		WCF::getDB()->beginTransaction();
		foreach ($this->parameters['data']['structure'][0] as $roomID) {
			$room = $roomList->search($roomID);
			if ($room === null) continue;
			
			$editor = new RoomEditor($room);
			$editor->update(array('position' => $i++));
		}
		WCF::getDB()->commitTransaction();
	}
	
	/**
	 * Validates parameters and permissions.
	 */
	public function validateGetRoomList() {
		if (!CHAT_ACTIVE) throw new \wcf\system\exception\IllegalLinkException();
		
		$rooms = Room::getCache();
		$roomID = ChatUtil::readUserData('roomID');
		if (!isset($rooms[$roomID])) {
			throw new \wcf\system\exception\IllegalLinkException();
		}
		$this->parameters['room'] = $rooms[$roomID];
	}
	
	/**
	 * Returns the available rooms.
	 */
	public function getRoomList() {
		$rooms = Room::getCache();
		
		$result = array();
		foreach ($rooms as $room) {
			if (!$room->canEnter()) continue;
			
			$result[] = array(
				'title' => (string) $room,
				'link' => \wcf\system\request\LinkHandler::getInstance()->getLink('Chat', array(
					'application' => 'chat',
					'object' => $room
				)),
				'active' => $room->roomID == $this->parameters['room']->roomID
			);
		}
		
		return $result;
	}
	
	/**
	 * Validates parameters and permissions.
	 */
	public function validateLeave() {
		if (!CHAT_ACTIVE) throw new \wcf\system\exception\IllegalLinkException();
		
		unset($this->parameters['user']);
		
		$rooms = Room::getCache();
		$roomID = ChatUtil::readUserData('roomID');
		if (!isset($rooms[$roomID])) throw new \wcf\system\exception\IllegalLinkException();
	}
	
	/**
	 * Leaves the room.
	 */
	public function leave() {
		// user cannot be set during an AJAX request may be set by the chat itself
		if (!isset($this->parameters['user'])) {
			$this->parameters['user'] = WCF::getUser();
		}
		
		$rooms = Room::getCache();
		
		$roomID = ChatUtil::readUserData('roomID', $this->parameters['user']);
		if (!isset($rooms[$roomID])) throw new \wcf\system\exception\UserInputException();
		$activeRoom = $rooms[$roomID];
		
		if (CHAT_DISPLAY_JOIN_LEAVE) {
			$userData['color'] = ChatUtil::readUserData('color', $this->parameters['user']);
			
			// leave message
			$messageAction = new \chat\data\message\MessageAction(array(), 'create', array(
				'data' => array(
					'roomID' => $activeRoom->roomID,
					'sender' => $this->parameters['user']->userID,
					'username' => $this->parameters['user']->username,
					'time' => TIME_NOW,
					'type' => \chat\data\message\Message::TYPE_LEAVE,
					'message' => '',
					'color1' => $userData['color'][1],
					'color2' => $userData['color'][2]
				)
			));
			$messageAction->executeAction();
		}
		
		// set current room to null
		ChatUtil::writeUserData(array('roomID' => null), $this->parameters['user']);
		
		\wcf\system\nodePush\NodePushHandler::getInstance()->sendMessage('be.bastelstu.chat.join');
	}
	
	/**
	 * Forces dead users to leave the chat.
	 */
	public function removeDeadUsers() {
		$deadUsers = Room::getDeadUsers();
		
		foreach ($deadUsers as $deadUser) {
			$roomAction = new self(array(), 'leave', array(
				'user' => $deadUser
			));
			$roomAction->executeAction();
		}
	}
	
	/**
	 * Validates permissions.
	 */
	public function validateGetDashboardRoomList() {
		if (!CHAT_ACTIVE) throw new \wcf\system\exception\IllegalLinkException();
	}
	
	/**
	 * Returns dashboard roomlist.
	 */
	public function getDashboardRoomList() {
		$rooms = Room::getCache();
		
		foreach ($rooms as $key => $room) {
			if (!$room->canEnter()) unset($rooms[$key]);
		}
		
		\wcf\system\WCF::getTPL()->assign(array(
			'rooms' => $rooms,
			'onlyList' => true
		));
		
		return array(
			'template' => \wcf\system\WCF::getTPL()->fetch('dashboardBoxOnlineList', 'chat')
		);
	}
}
