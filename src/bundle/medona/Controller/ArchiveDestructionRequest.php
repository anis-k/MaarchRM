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
 * Destruction request message
 *
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class ArchiveDestructionRequest extends abstractMessage
{
     /**
     * Get received archive delivery message
     *
     * @return array Array of medona/message object
     */
    public function listReception()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts = [];
        $queryParts[] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts[] = "type='ArchiveDestructionRequest'";
        $queryParts[] = "active=true";
        $queryParts[] = "status != 'processed'
        AND status != 'error'
        AND status != 'sent'
        AND status != 'validated'
        AND status != 'rejected'";

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
     * Get transfer history
     *
     * @param string $reference         Reference
     * @param string $archiver          Archiver
     * @param string $originator        Originator
     * @param string $depositor         Depositor
     * @param string $archivalAgreement Archival agreement
     * @param date   $fromDate          From date
     * @param date   $toDate            To date
     * @param string $status            Status
     *
     * @return array Array of medona/message object
     */
    public function history(
        $reference = null,
        $archiver = null,
        $originator = null,
        $depositor = null,
        $archivalAgreement = null,
        $fromDate = null,
        $toDate = null,
        $status = null
    ) {
        return $this->search(
            "ArchiveDestruction",
            $reference,
            $archiver,
            $originator,
            $depositor,
            $archivalAgreement,
            $fromDate,
            $toDate,
            $status
        );
    }

    /**
     * Count archive delivery message
     *
     * @return array Number of received and sent messages
     */
    public function count()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();
        $res = array();
        $queryParts = array();

        $queryParts["type"] = "type='ArchiveDestructionRequest'";
        $queryParts["registrationNumber"] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts["active"] = "active=true";
        $res['received'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        $queryParts["registrationNumber"] = "senderOrgRegNumber=$registrationNumber";
        $res['sent'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        return $res;
    }

    /**
     * Send a new a new delivery request
     * @param string $reference              The message identifier
     * @param array  $archives               An array of archives
     * @param string $comment                The request comment
     * @param string $requesterOrgRegNumber  The requesting org reg number
     * @param string $archiverOrgRegNumber   The recipient archiver org reg number
     * @param string $originatorOrgRegNumber The originator org reg number
     *
     * @return The request message generated
     */
    public function send(
        $reference,
        $archives,
        $comment = null,
        $requesterOrgRegNumber = null,
        $archiverOrgRegNumber = null,
        $originatorOrgRegNumber = null,
        $format = null
    ) {
        if (!is_array($archives)) {
            $archives = array($archives);
        }

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();

        $schema = "mades";
        if ($format) {
            $schema = $format;
        } elseif ($archives[0]->descriptionClass === 'seda2') {
            $schema = 'seda2';
        } elseif (\laabs::hasBundle('seda')) {
            $schema = "seda";
        }
        $message->schema = $schema;
        $message->type = "ArchiveDestructionRequest";
        $message->status = 'sent';
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;
        $message->reference = $reference;

        $message->comment[] = $comment;

        $message->senderOrgRegNumber = $requesterOrgRegNumber;
        $message->recipientOrgRegNumber = $archiverOrgRegNumber;
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
                $archiveDestructionRequestController = \laabs::newController(
                    "$namespace/ArchiveDestructionRequest"
                );
                $archiveDestructionRequestController->send($message);
            } else {
                $archiveDestructionRequest = $this->sendMessage($message);
                $message->object = $archiveDestructionRequest;

                $archiveDestructionRequest->originatingAgency = $this->sendOrganization($message->senderOrg);
                $archiveDestructionRequest->archivalAgency = $this->sendOrganization($message->recipientOrg);

                $message->object->unitIdentifier = $message->unitIdentifier;

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
        $senderOrg = \laabs::getToken('ORGANIZATION');

        // Requested by archiver: send auth request to originator
        if ($senderOrg->registrationNumber == $message->recipientOrgRegNumber) {
            $authorizationOriginatingAgencyRequestController = \laabs::newController('medona/AuthorizationOriginatingAgencyRequest');
            $authorizationOriginatingAgencyRequestController->send($message, $originatorOrgRegNumber);
            $message->status = "originator_authorization_wait";
        } else {
            // Requested by originator
            $controlAuthorities = $this->orgController->getOrgsByRole('controlAuthority');

            // Check if control authority is set on system
            if (count($controlAuthorities)) {
                $message->status = "control_authorization_wait";
                $authorizationControlAuthorityRequestController = \laabs::newController('medona/AuthorizationControlAuthorityRequest');
                $authorizationControlAuthorityRequestController->send($message, $originatorOrgRegNumber);
            }
        }

        return $message;
    }

    /**
     * Validate
     * @param object $messageId
     */
    public function validate($messageId)
    {
        if (is_scalar($messageId)) {
            $message = $this->read($messageId);
        } else {
            $message = $messageId;
        }

        $message->status = "validated";

        foreach ($message->unitIdentifier as $unitIdentifier) {
            $this->archiveController->eliminate((string) $unitIdentifier->objectId);
        }

        $this->update($message);
    }

    /**
     * Accept
     * @param object $message
     */
    public function accept($message)
    {
        if (is_scalar($message)) {
            $message = $this->read($message);
        }

        $message->status = "accepted";

        $event = $this->lifeCycleJournalController->logEvent(
            'medona/acceptance',
            'medona/message',
            $message->messageId,
            $message,
            true
        );

        $this->update($message);
    }

    /**
     * Reject the message identifier
     * @param string $messageId The message identifier
     * @param string $comment   A comment
     */
    public function reject($messageId, $comment = null)
    {
        $this->changeStatus($messageId, "rejected");

        $message = $this->read($messageId);

        $archiveIds = [];
        foreach ($message->unitIdentifier as $unitIdentifier) {
            $archiveIds[] = (string) $unitIdentifier->objectId;
        }
        $this->archiveController->setStatus($archiveIds, 'preserved');

        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));

        $this->lifeCycleJournalController->logEvent(
            'medona/rejection',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }

    /**
     * Process all archive destructions
     *
     * @return the result of process
     */
    public function processAll()
    {
        $results = array();

        $messageIds = $this->sdoFactory->index(
            'medona/message',
            ['messageId'],
            '(
                type = "ArchiveDestructionRequest"
                OR type = "ArchiveRestitution"
                OR (
                    type = "ArchiveTransfer"
                    && isIncoming = false
                )
            )
             AND status = "validated"'
        );

        foreach ($messageIds as $messageId) {
            // Avoid parallel processing
            $message = $this->sdoFactory->read('medona/message', (string) $messageId);
            if ($message->status != 'validated') {
                continue;
            }
            $this->changeStatus($message->messageId, "processing");

            $results[(string) $messageId] = $this->process($messageId);
        }

        return $results;
    }

    /**
     * Process archive destruction
     * @param medona/message $messageId
     *
     * @return the result of process
     */
    public function process($messageId)
    {
        if (is_scalar($messageId)) {
            $message = $this->read($messageId);
        } else {
            $message = $messageId;
        }

        $this->changeStatus($message->messageId, "processing");
        $message->unitIdentifier = $this->sdoFactory->readChildren('medona/unitIdentifier', $message);

        $archiveIds = [];
        foreach ($message->unitIdentifier as $unitIdentifier) {
            $archiveIds[] = (string) $unitIdentifier->objectId;
        }

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $archives = $this->archiveController->destruct($archiveIds);

            $logMessage = ["message" => "%s archives are deleted", "variables"=> count($archives['success'])];
            \laabs::notify(\bundle\audit\AUDIT_ENTRY_OUTPUT, $logMessage);

            $message->status = "processed";
            $message->operationDate = \laabs::newDatetime(null, "UTC");

            $operationResult = true;
            $this->update($message);
        } catch (\Exception $e) {
            $operationResult = false;
            throw $e;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        $this->lifeCycleJournalController->logEvent(
            'medona/processing',
            'medona/message',
            $message->messageId,
            $message,
            $operationResult
        );

        $archiveDestructionNotificationController = \laabs::newController("medona/ArchiveDestructionNotification");

        try {
            if (count($archives['success']) > 0) {
                $replyMessage = $archiveDestructionNotificationController->send($message, $archives['success']);
            } else {
                return;
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $replyMessage->messageId;
    }
}
