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
 * Trait for archive restitution
 *
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class ArchiveRestitutionRequest extends abstractMessage
{
    /**
     * Get received archive resititution message
     *
     * @return array Array of medona/message object
     */
    public function listReception()
    {
        $queryParts = array();
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts[] = "type='ArchiveRestitutionRequest'";
        $queryParts[] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts[] = "active=true";
        $queryParts[] = "status != 'processed' AND status != 'error' AND status != 'rejected'";

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
     * Get received  restitution message validation
     *
     * @return array Array of medona/message object
     */
    public function requestValidationList()
    {
        $queryParts = array();
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts[] = "type='ArchiveRestitutionRequest'";
        $queryParts[] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts[] = "active=true";
        $queryParts[] = "status=['sent', 'accepted']";

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
     * Get sending archive resitution message
     * @param string $sender
     * @param string $recipient
     * @param string $fromDate
     * @param string $toDate
     * @param string $reference
     *
     * @return array Array of medona/message object
     */
    public function listSending(
        $sender = false,
        $recipient = false,
        $fromDate = false,
        $toDate = false,
        $reference = false
    ) {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts[] = "type='ArchiveRestitutionRequest'";
        $queryParts[] = "senderOrgRegNumber=$registrationNumber";
        $queryParts[] = "active=true";

        $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        return $this->sdoFactory->find(
            'medona/message',
            '('.implode(') and (', $queryParts).')',
            null,
            false,
            false,
            $maxResults
        );
    }

    /**
     * Count archive restitution message
     *
     * @return array Number of received and sent messages
     */
    public function count()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $res = array();
        $queryParts = array();

        $queryParts["type"] = "type='ArchiveRestitutionRequest'";
        $queryParts["registrationNumber"] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts["active"] = "active=true";

        $res['received'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        $queryParts["registrationNumber"] = "senderOrgRegNumber=$registrationNumber";
        $res['sent'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        return $res;
    }

    /**
     * Send a new a new restitution request
     * @param string $reference    The message identifier
     * @param array  $archives     An array of archives
     * @param string $comment      The request comment
     * @param string $senderOrg    The requesting org
     * @param string $recipientOrg The requesting org
     * @param string $userName     The requester user name
     * @param string $format       The message format
     *
     * @return The reply message generated
     */
    public function send(
        $reference,
        $archives,
        $comment = false,
        $senderOrg = false,
        $recipientOrg = false,
        $userName = false,
        $format = null
    ) {
        if (!is_array($archives)) {
            $archives = array($archives);
        }

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();

        $message->schema = "medona";
        if ($format) {
            $message->schema = $format;
        } elseif ($archives[0]->descriptionClass === 'seda2') {
            $message->schema = 'seda2';
        } elseif (\laabs::hasBundle('seda')) {
            $message->schema = "seda";
        }

        $message->type = "ArchiveRestitutionRequest";
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;
        $message->reference = $reference;
        $message->status = "sent";

        $message->comment[] = $comment;

        $message->senderOrgRegNumber = $senderOrg;
        $message->recipientOrgRegNumber = $recipientOrg;
        $this->readOrgs($message); // read org names, addresses, communications, contacts

        try {
            $message->unitIdentifier = array();
            foreach ($archives as $archive) {
                $unitIdentifier = \laabs::newInstance("medona/unitIdentifier");
                $unitIdentifier->messageId = $message->messageId;
                $unitIdentifier->objectClass = "recordsManagement/archive";
                $unitIdentifier->objectId = (string) $archive->archiveId;

                $message->unitIdentifier[] = $unitIdentifier;
            }

            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $archiveRestitutionRequestController = \laabs::newController("$namespace/ArchiveRestitutionRequest");
                $archiveRestitutionRequestController->send($message);
            } else {
                $archiveRestitutionRequest = $this->sendMessage($message);
                $message->object = $archiveRestitutionRequest;

                $archiveRestitutionRequest->requester = $this->sendOrganization($message->senderOrg);
                if ($userName) {
                    $archiveRestitutionRequest->requester->userName = $userName;
                }
                
                $archiveRestitutionRequest->archivalAgency = $this->sendOrganization($message->recipientOrg);

                $this->generate($message);
                $this->save($message);
            }
        } catch (\Exception $e) {
            $message->status = "invalid";
            $this->create($message);

            $this->logValidationErrors($message, $e);

            throw $e;
        }

        $this->create($message);

        $this->lifeCycleJournalController->logEvent(
            'medona/sending',
            'medona/message',
            $message->messageId,
            $message,
            true
        );

        return $message;
    }

    /**
     * Send a new a new restitution request
     * @param string         $reference      The message reference
     * @param medona/message $requestMessage The request message
     *
     * @return The reply message generated
     */
    public function sendFromRequestMessage($reference, $requestMessage)
    {
        $this->readOrgs($requestMessage);

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();

        $message->schema = $requestMessage->schema;
        $message->type = "ArchiveRestitutionRequest";
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->reference = $reference;
        $message->status = "sent";

        $message->senderOrgRegNumber = $requestMessage->senderOrgRegNumber;
        $message->recipientOrgRegNumber = $requestMessage->recipientOrgRegNumber;
        $this->readOrgs($message); // read org names, addresses, communications, contacts

        try {
            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $archiveRestitutionRequestController = \laabs::newController("$namespace/ArchiveRestitutionRequest");
                $archiveRestitutionRequestController->send($message);
            } else {
                $archiveRestitutionRequest = $this->sendMessage($message);
                $message->object = $archiveRestitutionRequest;

                $archiveRestitutionRequest->requester = $this->sendOrganization($message->senderOrg);
                $archiveRestitutionRequest->archivalAgency = $this->sendOrganization($message->recipientOrg);

                $this->generate($message);
                $this->save($message);
            }

            $message->unitIdentifier = $this->sdoFactory->readChildren('medona/unitIdentifier', $requestMessage);
            foreach ($message->unitIdentifier as $unitIdentifier) {
                $unitIdentifier->messageId = $message->messageId;
            }
            $operationResult = true;

            $message->object->unitIdentifier = $message->unitIdentifier;
            $this->create($message);
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
    }

    /**
     * Accept archive restitution request message
     * @param string $messageId The message identifier
     *
     * @return medona/message The medon reply message
     */
    public function accept($messageId)
    {
        $this->changeStatus($messageId, "accepted");

        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));
        $message->unitIdentifier = $this->sdoFactory->readChildren('medona/unitIdentifier', $message);

        $this->lifeCycleJournalController->logEvent(
            'medona/acceptance',
            'medona/message',
            $message->messageId,
            $message,
            true
        );

        $archiveRestitutionRequestReplyController = \laabs::newController('medona/ArchiveRestitutionRequestReply');
        $replyMessage = $archiveRestitutionRequestReplyController->send($message, "000");

        return $replyMessage;
    }

    /**
     * Reject the message identifier
     * @param string $messageId The message identifier
     * @param string $comment   The comment
     *
     * @return object The reply message
     */
    public function reject($messageId, $comment = null)
    {
        $this->changeStatus($messageId, "rejected");

        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));
        $message->unitIdentifier = $this->sdoFactory->readChildren('medona/unitIdentifier', $message);

        foreach ((array) $message->unitIdentifier as $unitIdentifier) {
            $this->archiveController->setStatus((string) $unitIdentifier->objectId, "preserved");
        }


        $this->lifeCycleJournalController->logEvent(
            'medona/rejection',
            'medona/message',
            $message->messageId,
            $message,
            true
        );

        $archiveRestitutionRequestReplyController = \laabs::newController('medona/ArchiveRestitutionRequestReply');
        $replyMessage = $archiveRestitutionRequestReplyController->send($message, "400", $comment);

        return $replyMessage;
    }

    /**
     * Process the messages
     *
     * @return medona/message $message
     */
    public function processBatch()
    {
        $results = array();

        $messageIds = $this->sdoFactory->index(
            "medona/message",
            ["messageId"],
            "status='accepted' AND type='ArchiveRestitutionRequest' AND active=true"
        );

        foreach ($messageIds as $messageId) {
            // Avoid parallel processing
            $message = $this->sdoFactory->read('medona/message', (string) $messageId);
            if ($message->status != 'accepted') {
                continue;
            }
            $this->changeStatus($message->messageId, "processing");

            try {
                $results[(string) $message->messageId] = $this->process((string)$message->messageId);
            } catch (\Exception $e) {
                $results[(string) $message->messageId] = $e;
            }
        }

        return $results;
    }

    /**
     * Validate message against schema and rules
     * @param medona/message $message
     *
     * @return the result of process
     */
    public function process($message)
    {
        if (is_scalar($message)) {
            $messageId = $message;
            $message = $this->read($messageId);
        }

        $this->changeStatus($message->messageId, "processing");

        $archives = array();

        foreach ((array) $message->unitIdentifier as $unitIdentifier) {
            $archive = $this->archiveController->restitute($unitIdentifier->objectId);

            if ($archive == null) {
                continue;
            }

            $archives[] = $archive;
        }

        $logMessage = ["message" => "%s archives are restituted", "variables"=> count($archives)];
        \laabs::notify(\bundle\audit\AUDIT_ENTRY_OUTPUT, $logMessage);

        try {
            $archiveRestitutionController = \laabs::newController('medona/ArchiveRestitution');
            $archiveRestitutionController->send($message, $archives);

            $operationResult = true;
        } catch (\Exception $e) {
            $message->status = "error";
            $operationResult = false;
            $this->update($message);
            throw $e;
        }

        $this->lifeCycleJournalController->logEvent(
            'medona/processing',
            'medona/message',
            $message->messageId,
            $message,
            $operationResult
        );

        $this->changeStatus($message->messageId, "processed");

        return true;
    }

    /**
     * Destruct all restitued archives of restitution message
     *
     * @return the result of process
     */
    public function destructAll()
    {
        $results = array();

        $restitutionIds = $this->sdoFactory->index(
            'medona/message',
            array('messageId'),
            'type = "ArchiveRestitution" AND status = "validated"'
        );

        foreach ($restitutionIds as $restitutionId) {
            $this->changeStatus($restitutionId, "processing");
            $results[(string) $restitutionId] = $this->destruct($restitutionId);
            $this->changeStatus($restitutionId, "processed");
        }

        return $results;
    }

    /**
     * Destruction of restitued archive of message
     * @param string $messageId The restitution message identifier
     *
     * @return array array of archive
     */
    public function destruct($messageId)
    {
        $restitution = $this->sdoFactory->read('medona/message', array("messageId" => $messageId));
        $restitutionRequest = $this->sdoFactory->find(
            'medona/message',
            "type='ArchiveRestitutionRequest' AND senderOrgRegNumber='".$restitution->recipientOrgRegNumber."' AND replyReference='".$restitution->relatedReference."'"
        )[0];

        $restitutionRequest->unitIdentifier = $this->sdoFactory->readChildren(
            'medona/unitIdentifier',
            $restitutionRequest
        );

        $removedArchiveIds = [];
        foreach ($restitutionRequest->unitIdentifier as $unitIdentifier) {
            $archive = $this->archiveController->destruct($unitIdentifier->objectId);
            if (count($archive["success"]) > 0) {
                $removedArchiveIds[] = (string)$archive["success"][0]->archiveId;
            }
        }

        return $removedArchiveIds;
    }
}
