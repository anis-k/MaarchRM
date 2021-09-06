<?php

/* 
 * Copyright (C) 2015 Maarch
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
 * Class for archive restitution reply
 *
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class ArchiveRestitutionRequestReply extends abstractMessage
{

    /**
     * Send a new restitution request reply
     * @param string $requestMessage The request message identifier
     * @param string $replyCode      The reply code
     * @param string $comment        Comment of message
     *
     * @return The reply message generated
     */
    public function send($requestMessage, $replyCode = "000", $comment = null)
    {
        if (is_scalar($requestMessage)) {
            $messageId = $requestMessage;
            $requestMessage = $this->sdoFactory->read('medona/message', $messageId);
        }

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->type = "ArchiveRestitutionRequestReply";
        $message->schema = $requestMessage->schema;
        $message->status = "sent";
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;

        if ($comment) {
            $message->comment[] = $comment;
        }

        $message->reference = $requestMessage->reference.'_Reply_'.date("Y-m-d_H-i-s");
        $message->requestReference = $requestMessage->reference;

        $requestMessage->replyReference = $message->reference;

        $this->update($requestMessage);

        $message->senderOrgRegNumber = $requestMessage->recipientOrgRegNumber;
        $message->recipientOrgRegNumber = $requestMessage->senderOrgRegNumber;
        $this->readOrgs($message); // read org names, addresses, communications, contacts

        $message->replyCode = $replyCode;

        $message->unitIdentifier = $requestMessage->unitIdentifier;
        foreach ($message->unitIdentifier as $unitIdentifier) {
            $unitIdentifier->messageId = $message->messageId;
        }

        try {
            mkdir($this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId, 0777, true);

            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $archiveRestitutionRequestReplyController = \laabs::newController("$namespace/ArchiveRestitutionRequestReply");
                $archiveRestitutionRequestReplyController->send($message);
            } else {
                $archiveRestitutionRequestReply = $this->sendMessage($message);
                $message->object = $archiveRestitutionRequestReply;

                $archiveRestitutionRequestReply->requester = $this->sendOrganization($message->senderOrg);
                $archiveRestitutionRequestReply->archivalAgency = $this->sendOrganization($message->recipientOrg);

                $this->generate($message);
                $this->save($message);
            }
            $operationResult = true;
        } catch (\Exception $e) {
            $message->status = "invalid";
            $this->create($message);

            $this->logValidationErrors($message, $e);

            throw $e;
        }

        $this->lifeCycleJournalController->logEvent(
            'medona/sending',
            'medona/message',
            $message->messageId,
            $message,
            $operationResult
        );

        $this->create($message);


        return $message;
    }
}
