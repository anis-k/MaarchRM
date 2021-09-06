<?php
/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of bundle mades.
 *
 * Bundle mades is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle mades is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle mades.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\mades\Controller;

/**
 * Class for ArchiveDestructionNotification message handling
 */
class ArchiveDestructionRequest extends abstractMessage
{
    /**
     * Send message with all contents embedded
     * @param string $message The message
     */
    public function send($message)
    {
        $archiveDestructionRequest = abstractMessage::send($message);

        $archiveDestructionRequest->messageRequestIdentifier = $message->reference;

        $archiveDestructionRequest->archivalAgency = $this->sendOrganization($message->recipientOrg);

        $archiveDestructionRequest->requester = $this->sendOrganization($message->senderOrg);

        $this->sendUnitIdentifiers($message);
    }
}
