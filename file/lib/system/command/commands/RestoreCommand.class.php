<?php
namespace chat\system\command\commands;
use \wcf\data\user\User;
use \wcf\system\WCF;
use \wcf\util\ChatUtil;

/**
 * Resets the color of a user
 *
 * @author 	Tim Düsterhus
 * @copyright	2010-2013 Tim Düsterhus
 * @license	Creative Commons Attribution-NonCommercial-ShareAlike <http://creativecommons.org/licenses/by-nc-sa/3.0/legalcode>
 * @package	be.bastelstu.chat
 * @subpackage	system.chat.command.commands
 */
class RestoreCommand extends \chat\system\command\AbstractRestrictedCommand {
	public $enableHTML = 1;
	public $user = null;
	public $link = '';
	
	public function __construct(\chat\system\command\CommandHandler $commandHandler) {
		parent::__construct($commandHandler);
		
		$this->user = User::getUserByUsername(rtrim($commandHandler->getParameters(), ','));
		if (!$this->user->userID) throw new \chat\system\command\UserNotFoundException(rtrim($commandHandler->getParameters(), ','));
		
		$this->link = '<span class="userLink" data-user-id="'.$this->user->userID.'" />';
		
		$this->didInit();
	}
	
	/**
	 * @see	\chat\system\command\IRestrictedChatCommand::checkPermission()
	 */
	public function checkPermission() {
		parent::checkPermission();
		
		WCF::getSession()->checkPermissions(array('mod.chat.canRestore'));
	}
	
	/**
	 * @see	\chat\system\command\ICommand::getType()
	 */
	public function getType() {
		return \chat\data\message\Message::TYPE_MODERATE;
	}
	
	/**
	 * @see	\chat\system\command\ICommand::getMessage()
	 */
	public function getMessage() {
		return serialize(array(
			'link' => $this->link,
			'type' => str_replace(array('chat\system\command\commands\\', 'command'), '', strtolower(get_class($this)))
		));
	}
}
