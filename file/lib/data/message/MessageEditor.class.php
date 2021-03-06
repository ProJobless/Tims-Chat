<?php
namespace chat\data\message;

/**
 * Provides functions to edit chat messages.
 *
 * @author 	Tim Düsterhus
 * @copyright	2010-2013 Tim Düsterhus
 * @license	Creative Commons Attribution-NonCommercial-ShareAlike <http://creativecommons.org/licenses/by-nc-sa/3.0/legalcode>
 * @package	be.bastelstu.chat
 * @subpackage	chat.message
 */
class MessageEditor extends \wcf\data\DatabaseObjectEditor {
	/**
	 * @see	\wcf\data\DatabaseObjectDecorator::$baseClass
	 */
	protected static $baseClass = '\chat\data\message\Message';
	
	/**
	 * Notify the Push-Server.
	 */
	public static function create(array $parameters = array()) {
		\wcf\system\nodePush\NodePushHandler::getInstance()->sendMessage('be.bastelstu.chat.newMessage');
		
		return parent::create($parameters);
	}
}
