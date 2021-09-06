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
 * Class for archiveDelivery
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class ArchiveDeliveryRequestReply extends abstractMessage
{

    /**
     * Get received archive delivery message
     *
     * @return array Array of medona/message object
     */
    public function listReception()
    {
        $currentOrg = \laabs::getToken('ORGANIZATION');
        if (!$currentOrg) {
            $this->view->addContentFile("recordsManagement/welcome/noWorkingOrg.html");

            return $this->view->saveHtml();
        }

        $queryParts = array();
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts[] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts[] = "type='ArchiveDeliveryRequestReply'";
        $queryParts[] = "status = 'sent'";
        $queryParts[] = "active=true";

        $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        return $this->sdoFactory->find(
            'medona/message',
            implode(' and ', $queryParts),
            null,
            false,
            false,
            $maxResults);
    }

    /**
     * Get deliveries request reply messages
     *
     * @return array Array of delivery request reply message
     */
    public function getDeliveries()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts[] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts[] = "type='ArchiveDeliveryRequestReply'";
        $queryParts[] = "active=true";
        $queryParts[] = "status = ['sent']";

        $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        return $this->sdoFactory->find(
            'medona/message',
            implode(' and ', $queryParts),
            null,
            false,
            false,
            $maxResults
        );
    }

    /**
     * Send a new delivery request reply
     * @param string $requestMessage The request message identifier
     * @param object $archives       The archives to deliver
     * @param string $replyCode      The reply code
     * @param string $comment        A comment
     *
     * @return The reply message generated
     */
    public function send($requestMessage, $archives = null, $replyCode = "000", $comment = null)
    {
        if (is_scalar($requestMessage)) {
            $messageId = $requestMessage;
            $requestMessage = $this->sdoFactory->read('medona/message', $messageId);
        }

        $authorizationMessage = $this->sdoFactory->find(
            'medona/message',
            "authorizationReference='$requestMessage->reference'"
        );

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->type = "ArchiveDeliveryRequestReply";
        $message->schema = $requestMessage->schema;
        $message->status = "sent";
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;

        if ($authorizationMessage) {
            $message->authorizationReference = $authorizationMessage[0]->reference;
            $message->authorizationRequesterOrgRegNumber = $authorizationMessage[0]->recipientOrgRegNumber;
            $message->authorizationReason = $authorizationMessage[0]->type;
        }

        $message->reference = $requestMessage->reference.'_Reply_'.date("Y-m-d_H-i-s");
        $message->requestReference = $requestMessage->reference;

        $message->senderOrgRegNumber = $requestMessage->recipientOrgRegNumber;
        $message->recipientOrgRegNumber = $requestMessage->senderOrgRegNumber;
        $this->readOrgs($message); // read org names, addresses, communications, contacts
        $message->replyCode = $replyCode;

        if ($message->replyCode == "000" || $message->replyCode == "001") {
            foreach ($archives as $archive) {
                $archive->lifeCycleEvent = $this->lifeCycleJournalController->getObjectEvents($archive->archiveId, 'recordsManagement/archive');
            }

            $message->archive = $archives;
            $message->dataObjectCount = count($archives);
        }

        foreach ($archives as $archive) {
            $unitIdentifier = \laabs::newInstance("medona/unitIdentifier");
            $unitIdentifier->messageId = $message->messageId;
            $unitIdentifier->objectClass = "recordsManagement/archive";
            $unitIdentifier->objectId = (string) $archive->archiveId;

            $message->unitIdentifier[] = $unitIdentifier;
        }

        if ($comment) {
            $message->comment[] = $comment;
        }

        try {
            mkdir($this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId, 0777, true);

            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $archiveDeliveryRequestReplyController = \laabs::newController("$namespace/ArchiveDeliveryRequestReply");
                $archiveDeliveryRequestReplyController->send($message);
            } else {
                $archiveDeliveryRequestReply = $this->sendMessage($message);
                $message->object = $archiveDeliveryRequestReply;

                $archiveDeliveryRequestReply->requester = $this->sendOrganization($message->senderOrg);
                $archiveDeliveryRequestReply->archivalAgency = $this->sendOrganization($message->recipientOrg);

                $this->generate($message);
                $this->save($message);
            }
            $operationResult = true;
        } catch (\Exception $e) {
            $message->status = "invalid";
            $operationResult = false;

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
