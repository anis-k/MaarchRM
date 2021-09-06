<?php

/* 
 * Copyright (C) 2016 Maarch
 *
 * This file is part of bundle medona
 *
 * Bundle medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\medona\Controller;

/**
 * Archive modification request reply
 *
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class ArchiveModificationRequestReply extends abstractMessage
{
    /**
     * Send a new transfer reply
     * @param string $requestMessage The modification request message
     * @param string $code           The reply code
     * @param string $comment        The motivation
     *
     * @return The message generated
     */
    public function send($requestMessage, $code, $comment)
    {
        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->type = "ArchiveModificationRequestReply";

        $schema = "medona";
        $message->schema = $schema;

        $message->status = "sent";
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;

        $message->reference = $requestMessage->reference."_Reply";
        $message->requestReference = $requestMessage->reference;

        $message->senderOrgRegNumber = $requestMessage->recipientOrgRegNumber;
        $message->recipientOrgRegNumber = $requestMessage->senderOrgRegNumber;
        $this->readOrgs($message); // read org names, addresses, communications, contacts

        $event = $this->lifeCycleJournalController->logEvent(
            'medona/sending',
            'medona/message',
            $message->messageId,
            $message,
            true
        );

        $this->create($message);

        return $message;
    }
}
