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
class ArchiveRestitution extends abstractMessage
{

    /**
     * Get validation restitution message
     *
     * @return array Array of medona/message object
     */
    public function validationList()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts = [];
        $queryParts[] = "type='ArchiveRestitution'";
        $queryParts[] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts[] = "status=['sent', 'received']";
        $queryParts[] = "active=true";

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
     * Get process restitution message
     *
     * @return array Array of medona/message object
     */
    public function processList()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts = [];
        $queryParts[] = "type='ArchiveRestitution'";
        $queryParts[] = "senderOrgRegNumber=$registrationNumber";
        $queryParts[] = "status='acknowledge'";
        $queryParts[] = "active=true";

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
            "ArchiveRestitution",
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
     * Export archive restitution
     * @param string $messageId The message identifier
     *
     * @return the result of process
     */
    public function export($messageId)
    {
        $message = $this->sdoFactory->read('medona/message', $messageId);

        $exportResult = \laabs::newController("medona/message")->export($messageId);

        $this->lifeCycleJournalController->logEvent(
            'medona/reception',
            'medona/message',
            $message->messageId,
            $message,
            true
        );

        $this->changeStatus($messageId, "received");

        return $exportResult;
    }

    /**
     * Count archive restitution message
     *
     * @return array Number of received
     */
    public function count()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts = [];
        $queryParts["type"] = "type='ArchiveRestitution'";
        $queryParts["registrationNumber"] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts["active"] = "active=true";

        return $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));
    }

    /**
     * Send a new restitution request reply
     * @param string $requestMessage The request message identifier
     * @param object $archives       The archives to deliver
     * @param string $replyCode      The reply code
     *
     * @return The reply message generated
     */
    public function send($requestMessage, $archives, $replyCode = "000")
    {
        if (is_scalar($requestMessage)) {
            $messageId = $requestMessage;
            $requestMessage = $this->sdoFactory->read('medona/message', $messageId);

            //$this->load($message);
        }

        $replyMessage = $this->sdoFactory->find(
            'medona/message',
            "type='".$requestMessage->type."Reply' AND recipientOrgRegNumber='".$requestMessage->senderOrgRegNumber."' AND senderOrgRegNumber='".$requestMessage->recipientOrgRegNumber."' AND requestReference='".$requestMessage->reference."'");
        if (count($replyMessage) > 0) {
            $replyMessage = $replyMessage[0];
        } else {
            $this->sendError('104', "The reply message was not found.");
            $exception = \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            $exception->errors = $this->errors;
        }

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->type = "ArchiveRestitution";
        $message->schema = $requestMessage->schema;
        $message->status = "sent";
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;

        $message->reference = 'ArchiveRestitution_'.\laabs::newId();
        $message->relatedReference = $replyMessage->reference;

        $message->senderOrgRegNumber = $replyMessage->senderOrgRegNumber;
        $message->recipientOrgRegNumber = $replyMessage->recipientOrgRegNumber;
        $this->readOrgs($message); // read org names, addresses, communications, contacts

        $message->unitIdentifier = $requestMessage->unitIdentifier;
        foreach ($message->unitIdentifier as $unitIdentifier) {
            $unitIdentifier->messageId = $message->messageId;
        }

        if ($replyMessage->replyCode == "000" || $replyMessage->replyCode == "001") {
            foreach ($archives as $archive) {
                $archive->lifeCycleEvent = $this->lifeCycleJournalController->getObjectEvents($archive->archiveId, 'recordsManagement/archive');
            }

            $message->archive = $archives;
            $message->dataObjectCount = count($archives);
        }

        try {
            mkdir($this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId, 0777, true);

            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $archiveRestitutionController = \laabs::newController("$namespace/ArchiveRestitution");
                $archiveRestitutionController->send($message);
            } else {
                $archiveRestitution = $this->sendMessage($message);
                $message->object = $archiveRestitution;

                $archiveRestitution->requester = $this->sendOrganization($message->senderOrg);
                $archiveRestitution->archivalAgency = $this->sendOrganization($message->recipientOrg);

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

    /**
     * Flag for restitution
     * @param array  $archiveIds Array of archive identifier
     * @param string $identifier The medona message reference
     * @param string $comment    The message comment
     * @param string $format     The message format
     *
     * @return array The result of the operation
     */
    public function setForRestitution($archiveIds, $identifier = null, $comment = null, $format = null)
    {
        $senderOrg = \laabs::getToken('ORGANIZATION');
        if (!$senderOrg) {
            throw \laabs::newException('medona/invalidMessageException', "No current organization choosen");
        }

        $senderOrgRegNumber = $senderOrg->registrationNumber;
        $user = \laabs::getToken('AUTH');
        $userName = false;
        if ($user) {
            $userAccountController = \laabs::newController('auth/userAccount');
            $user = $userAccountController->edit($user->accountId);
            $userName = $user->accountName;
        }

        $archivesByOriginator = [];
        $archivesIdsByOriginator = [];

        foreach ($archiveIds as $archiveId) {
            $archive = $this->archiveController->retrieve($archiveId);
            if (!isset($archivesByOriginator[$archive->originatorOrgRegNumber])) {
                $archivesByOriginator[$archive->originatorOrgRegNumber] = [];
            }

            $archivesByOriginator[$archive->originatorOrgRegNumber][] = $archive;
            $archivesIdsByOriginator[$archive->originatorOrgRegNumber][] = $archive->archiveId;
        }

        $result = array('success' => array(), 'error' => array());
        $archiveRestitutionRequestController = \laabs::newController("medona/ArchiveRestitutionRequest");

        if (!$identifier) {
            $identifier = "archiveRestitutionRequest_".date("Y-m-d_H-i-s");
        }

        $reference = $identifier;
        foreach ($archivesIdsByOriginator as $originatorOrgRegNumber => $archiveIds) {
            $candidates = $this->archiveController->setForRestitution($archiveIds);
            $result["success"] = array_merge($result["success"], $candidates["success"]);
            $result["error"] = array_merge($result["error"], $candidates["error"]);

            $archivesForRestitution = [];

            foreach ($archivesByOriginator[$originatorOrgRegNumber] as $archive) {
                if (in_array($archive->archiveId, $candidates["success"])) {
                    $archivesForRestitution[] = $archive;
                }
            }

            if (empty($archivesForRestitution)) {
                continue;
            }

            $i = 1;
            $unique = array(
                'type' => 'ArchiveRestitutionRequest',
                'senderOrgRegNumber' => $senderOrgRegNumber,
                'reference' => $reference,
            );

            while ($this->sdoFactory->exists("medona/message", $unique)) {
                $i++;
                $unique['reference'] = $reference = $identifier.'_'.$i;
            }

            $recipientOrgRegNumber = $archivesForRestitution[0]->archiverOrgRegNumber;

            $archiveRestitutionRequestController->send(
                $reference,
                $archivesForRestitution,
                $comment,
                $senderOrgRegNumber,
                $recipientOrgRegNumber,
                $userName,
                $format
            );
        }

        return $result;
    }

    /**
     * Validate archive restitution message
     * @param string $messageId The message identifier
     */
    public function validate($messageId)
    {
        $this->changeStatus($messageId, "accepted");

        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));
        $this->lifeCycleJournalController->logEvent(
            'medona/acceptance',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }

    /**
     * Validate archive restitution message
     * @param string $messageId The message identifier
     */
    public function acknowledge($messageId)
    {
        $this->changeStatus($messageId, "acknowledge");

        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));
        $requestMessage = $this->sdoFactory->find(
            'medona/message',
            "type='ArchiveRestitutionRequest' AND senderOrgRegNumber='".$message->recipientOrgRegNumber."' AND replyReference='".$message->relatedReference."'")[0];

        $requestMessage->unitIdentifier = $this->sdoFactory->readChildren('medona/unitIdentifier', $requestMessage);

        foreach ($requestMessage->unitIdentifier as $unitIdentifier) {
            $this->archiveController->setStatus($unitIdentifier->objectId, 'restituted');
        }

        $this->lifeCycleJournalController->logEvent(
            'medona/acknowledgement',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }

    /**
     * Reject the archive restitution
     * @param string $messageId The message identifier
     * @param string $comment   The comment
     *
     * @return object The reply message
     */
    public function reject($messageId, $comment)
    {
        $this->changeStatus($messageId, "rejected");

        $message = $this->sdoFactory->read('medona/message', $messageId);
        $requestMessage = $this->sdoFactory->find(
            'medona/message',
            "type='ArchiveRestitutionRequest' AND senderOrgRegNumber='".$message->recipientOrgRegNumber."' AND replyReference='".$message->relatedReference."'"
        )[0];
        $requestMessage->unitIdentifier = $this->sdoFactory->readChildren('medona/unitIdentifier', $requestMessage);

        foreach ($requestMessage->unitIdentifier as $unitIdentifier) {
            $this->archiveController->setStatus((string) $unitIdentifier->objectId, "preserved");
        }
        
        $this->lifeCycleJournalController->logEvent(
            'medona/rejection',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }

    /**
     * Validate archive restitution message
     * @param string $messageId The message identifier
     */
    public function process($messageId)
    {
        $this->changeStatus($messageId, "validating");
        $message = $this->sdoFactory->read('medona/message', array("messageId" => $messageId));
        $message->active = false;

        $this->update($message);

        $this->changeStatus($messageId, "validated");

        $uri = $this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId;
        if (is_dir($uri)) {
            \laabs\rmdir($uri, true);
        }

        $this->lifeCycleJournalController->logEvent(
            'medona/processing',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }
}
