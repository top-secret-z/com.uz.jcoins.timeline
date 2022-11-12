<?php

/*
 * Copyright by Udo Zaydowicz.
 * Modified by SoftCreatR.dev.
 *
 * License: http://opensource.org/licenses/lgpl-license.php
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software Foundation,
 * Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
namespace timeline\system\event\listener;

use wcf\system\event\listener\IParameterizedEventListener;
use wcf\system\user\jcoins\UserJCoinsStatementHandler;
use wcf\system\WCF;

/**
 * JCoins listener for timeline entries.
 */
class JCoinsTimelineEntryListener implements IParameterizedEventListener
{
    /**
     * @inheritdoc
     */
    public function execute($eventObj, $className, $eventName, array &$parameters)
    {
        if (!MODULE_JCOINS) {
            return;
        }

        switch ($eventObj->getActionName()) {
            case 'addEntry':
                if (!WCF::getUser()->userID) {
                    return;
                }

                UserJCoinsStatementHandler::getInstance()->create('com.uz.jcoins.statement.timeline.entry', null, ['userID' => WCF::getUser()->userID]);
                break;

            case 'delete':
                foreach ($eventObj->getObjects() as $object) {
                    // only users
                    if (!$object->userID) {
                        continue;
                    }
                    UserJCoinsStatementHandler::getInstance()->revoke('com.uz.jcoins.statement.timeline.entry', null, ['userID' => $object->userID]);
                }
                break;
        }
    }
}
