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
 * Trait for acknowledgement
 *
 * @package Medona
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class Acknowledgement 
    extends abstractMessage
{

    /**
     * Send an aknowledgement
     * @param string $receivedMessage The received message
     *
     * @return The ack generated
     */
    public function send($receivedMessage)
    {
        if (is_scalar($receivedMessage)) {
            $messageId = $receivedMessage;
            $receivedMessage = $this->sdoFactory->read('medona/message', $messageId);
        }

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->type = "Acknowledgement";
        $message->schema = $receivedMessage->schema;
        $message->status = "sent";
        $message->date = \laabs::newDatetime(null, "UTC");

        $message->reference = $receivedMessage->reference.'_Ack';

        $message->requestReference = $receivedMessage->reference;
        
        $message->senderOrgRegNumber = $receivedMessage->recipientOrgRegNumber;
        $message->recipientOrgRegNumber = $receivedMessage->senderOrgRegNumber;
        
        try {
            $this->readOrgs($message); // read org names, addresses, communications, contacts
        } catch (\Exception $e) {
            $recipientOrg = \laabs::getToken("ORGANIZATION");

            $message->recipientOrgRegNumber = $recipientOrg->registrationNumber;
            $message->recipientOrg = $recipientOrg;

            $message->senderOrgRegNumber = $recipientOrg->registrationNumber;
            $message->senderOrg = $recipientOrg;
        }

        try {
            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $archiveTransferReplyController = \laabs::newController("$namespace/Acknowledgement");
                
                $archiveTransferReplyController->send($message);
            } else {
                /*$archiveTransferReply = $this->sendMessage($message);
                $message->object = $archiveTransferReply;

                $archiveTransferReply->replyCode = $this->sendReplyCode($message->replyCode);

                if (isset($message->requestReference)) {
                    $archiveTransferReply->messageRequestIdentifier = \laabs::newInstance('medona/Identifier', $message->requestReference);
                }

                if (isset($message->operationDate)) {
                    $archiveTransferReply->grantDate = (string) $message->operationDate;
                }

                $archiveTransferReply->archivalAgency = $this->sendOrganization($message->senderOrg);

                $archiveTransferReply->transferringAgency = $this->sendOrganization($message->recipientOrg);

                // Generate XML
                $this->generate($message);

                $this->save($message);*/
            }
            $operationResult = true;

        } catch (\Exception $e) {
            $message->status = "error";
            $operationResult = false;

            throw $e;
        }

        $event = $this->lifeCycleJournalController->logEvent(
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
