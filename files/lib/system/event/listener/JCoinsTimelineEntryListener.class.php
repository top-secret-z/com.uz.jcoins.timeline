<?php
namespace timeline\system\event\listener;
use timeline\data\entry\Entry;
use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * JCoins listener for timeline entries.
 *
 * @author		2017-2022 Zaydowicz
 * @license		GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 * @package		com.uz.jcoins.timeline
 */
class JCoinsTimelineEntryListener implements IParameterizedEventListener {
	/**
	 * @inheritdoc
	 */
	public function execute($eventObj, $className, $eventName, array &$parameters) {
		if (!MODULE_JCOINS) return;
		
		switch ($eventObj->getActionName()) {
			case 'addEntry':
				if (!WCF::getUser()->userID) return;
				
				UserJCoinsStatementHandler::getInstance()->create('com.uz.jcoins.statement.timeline.entry', null, ['userID' => WCF::getUser()->userID]);
				break;
				
			case 'delete':
				foreach ($eventObj->getObjects() as $object) {
					// only users
					if (!$object->userID) continue;
					UserJCoinsStatementHandler::getInstance()->revoke('com.uz.jcoins.statement.timeline.entry', null, ['userID' => $object->userID]);
				}
				break;
		}
	}
}
