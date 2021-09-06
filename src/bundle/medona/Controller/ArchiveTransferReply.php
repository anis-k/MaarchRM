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
 * Trait for archive transfer reply
 *
 * @package Medona
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class ArchiveTransferReply extends abstractMessage
{

    /**
     * Send a new transfer reply
     * @param string $transferMessage The request message identifier
     * @param string $replyCode       The reply code
     * @param string $comment        The comment
     *
     * @return The reply message generated
     */
    public function send($transferMessage, $archives = null, $replyCode = "OK", $comment = null)
    {
        if (is_scalar($transferMessage)) {
            $messageId = $transferMessage;
            $transferMessage = $this->sdoFactory->read('medona/message', $messageId);
        }

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->type = "ArchiveTransferReply";
        $message->schema = $transferMessage->schema;
        $message->status = "sent";
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;
        $message->replyCode = $replyCode;

        $message->reference = $transferMessage->reference.'_Reply_'.date("Y-m-d_H-i-s");

        $lifeCycleEvents = $this->lifeCycleJournalController->getObjectEvents($transferMessage->messageId, 'medona/message');
        foreach ($lifeCycleEvents as $lifeCycleEvent) {
            $message->comment[] = $lifeCycleEvent->description;
        }

        if ($comment) {
            $message->comment[] = $comment;
        }

        $message->requestReference = $transferMessage->reference;
        $message->operationDate = \laabs::newDatetime(null, "UTC");

        $message->senderOrgRegNumber = $transferMessage->recipientOrgRegNumber;
        $message->recipientOrgRegNumber = $transferMessage->senderOrgRegNumber;
        try {
            $this->readOrgs($message); // read org names, addresses, communications, contacts
        } catch (\Exception $e) {
            $recipientOrg = \laabs::getToken("ORGANIZATION");

            $message->recipientOrgRegNumber = $recipientOrg->registrationNumber;
            $message->recipientOrg = $recipientOrg;

            $message->senderOrgRegNumber = $recipientOrg->registrationNumber;
            $message->senderOrg = $recipientOrg;
        }

        if (!is_null($archives)) {
            foreach ($archives as $archive) {
                $unitIdentifier = \laabs::newInstance("medona/unitIdentifier");
                $unitIdentifier->messageId = $message->messageId;
                $unitIdentifier->objectId = (string) $archive->archiveId;
                $unitIdentifier->objectClass = "recordsManagement/archive";

                $message->unitIdentifier[] = $unitIdentifier;
            }
        }

        $message->archive = $archives;

        /*$message->lifeCycleEventId = \laabs::newTokenList();
        foreach ($archives as $archive) {
            foreach ($archives as $archive) {
                if ($archive->lifeCycleEvent) {
                    foreach ($archive->lifeCycleEvent as $event) {
                        $message->lifeCycleEventId[] = (string) $event->eventId;
                    }
                }
            }
        }*/

        try {
            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $archiveTransferReplyController = \laabs::newController("$namespace/ArchiveTransferReply");
                $archiveTransferReplyController->send($message);
            } else {
                $archiveTransferReply = $this->sendMessage($message);
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

                $this->save($message);
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
