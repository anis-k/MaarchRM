<?php
/*
 * Copyright (C) 2015 Maarch
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
 * Class for Modification Notification message handling
 *
 * @author Arnaud PAUGET <arnaud.pauget@maarch.org>
 */
class ArchiveDeliveryRequestReply extends abstractMessage
{

    /**
     * Send message with all contents embedded
     * @param string $message The message
     */
    public function send($message)
    {
        $this->message = $message;

        $archiveDeliveryRequestReply = abstractMessage::send($message);

        $archiveDeliveryRequestReply->replyCode = $this->sendReplyCode($message->replyCode);

        $archiveDeliveryRequestReply->messageRequestIdentifier = $message->requestReference;

        $archiveDeliveryRequestReply->messageRequestReplyIdentifier = $message->reference;

        $archiveDeliveryRequestReply->authorizationRequestReplyIdentifier = $message->authorizationReference;

        $archiveDeliveryRequestReply->archivalAgency = $this->sendOrganization($message->senderOrg);

        $archiveDeliveryRequestReply->requester = $this->sendOrganization($message->recipientOrg);

        $archiveDeliveryRequestReply->unitIdentifier = [];

        $this->sendDataObjectPackage($message, $withAttachment = true);
        if (isset($message->archive)) {
            foreach ($message->archive as $archive) {
                $archiveDeliveryRequestReply->unitIdentifier[] = $archive->archiveId;
            }
        }
        
        $this->sendJSON($message);
    }
}
