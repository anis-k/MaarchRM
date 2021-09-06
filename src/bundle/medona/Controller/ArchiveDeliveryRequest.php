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
 * Archive delivery request
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class ArchiveDeliveryRequest extends abstractMessage
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
        $queryParts[] = "type='ArchiveDeliveryRequest'";
        $queryParts[] = "status = 'sent'";
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
            "ArchiveDelivery",
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
        $res = array();
        $queryParts = array();

        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts["registrationNumber"] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts["type"] = "type='ArchiveDeliveryRequest'";
        $queryParts["active"] = "active=true";
        $res['received'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        $queryParts["registrationNumber"] = "senderOrgRegNumber=$registrationNumber";
        $res['sent'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        return $res;
    }

    /**
     * Deliver an archive
     * @param mixed  $archiveIds    The identifier of archive or a list of identifiers
     * @param string $identifier    The medona message reference
     * @param boolean $derogation   Ask for an authorization
     * @param string $comment       The message comment
     * @param string $format        The message format
     *
     * @return array Array of message
     *
     * @throws \bundle\recordsManagement\Exception\notCommunicableException
     */
    public function requestDelivery($archiveIds, $identifier = null, $derogation = false, $comment = null, $format = null)
    {
        $requesterOrg = \laabs::getToken('ORGANIZATION');
        if (!$requesterOrg) {
            throw \laabs::newException('medona/invalidMessageException', "No current organization choosen");
        }
        $requesterOrgRegNumber = $requesterOrg->registrationNumber;

        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $archivesByOriginator = array();
        $messages = array();

        foreach ($archiveIds as $archiveId) {
            $archive = $this->archiveController->retrieve(
                $archiveId,
                $withBinary = false,
                $checkAccess = true,
                $isCommunication = true
            );

            if (!isset($archivesByOriginator[$archive->originatorOrgRegNumber])) {
                $archivesByOriginator[$archive->originatorOrgRegNumber] = array();
            }

            $archivesByOriginator[$archive->originatorOrgRegNumber][] = $archive;
        }

        if (!$identifier) {
            $identifier = "archiveDeliveryRequest_".date("Y-m-d_H-i-s");
        }

        $reference = $identifier;
        foreach ($archivesByOriginator as $originatorOrgRegNumber => $archives) {
            $i = 1;

            $unique = array(
                'type' => 'ArchiveDeliveryRequest',
                'senderOrgRegNumber' => $requesterOrgRegNumber,
                'reference' => $reference,
            );


            $archiverOrgRegNumber = $archives[0]->archiverOrgRegNumber;

            $communicableArchives = [];
            foreach ($archives as $archive) {
                if ($this->isCommunicable($archive)) {
                    $communicableArchives['communicable'] = $archive;
                } else {
                    $communicableArchives['notCommunicable'] = $archive;
                }
            }

            foreach ($communicableArchives as $key => $archives) {
                while ($this->sdoFactory->exists("medona/message", $unique)) {
                    $i++;
                    $unique['reference'] = $reference = $identifier.'_'.$i;
                }
                $message = $this->send(
                    $reference,
                    $archives,
                    $derogation,
                    $comment,
                    $requesterOrgRegNumber,
                    $archiverOrgRegNumber,
                    null,
                    $format
                );
                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * Chexk wether an archive is communicable or not
     *
     * @param recordsManagement/archive $archive object archive
     *
     * @return boolean
     */
    protected function isCommunicable($archive)
    {
        if ($archive->accessRuleComDate) {
            $communicationDelay = $archive->accessRuleComDate->diff(\laabs::newTimestamp());
            if ($communicationDelay->invert != 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Send a new a new delivery request
     * @param string  $reference             The message identifier
     * @param array   $archives              An array of archives
     * @param boolean $derogation            Ask for an authorization
     * @param string  $comment               The request comment
     * @param object  $requesterOrgRegNumber The requesting org reg number
     * @param string  $archiverOrgRegNumber  The archiver org registration number
     * @param string  $userName              The requester user name
     * @param string  $format                The message format
     *
     * @return The reply message generated
     */
    public function send(
        $reference,
        $archives,
        $derogation = false,
        $comment = false,
        $requesterOrgRegNumber = false,
        $archiverOrgRegNumber = false,
        $userName = false,
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
            $schema = "seda2";
        } elseif (\laabs::hasBundle('seda')) {
            $schema = "seda";
        }
        $message->schema = $schema;
        $message->type = "ArchiveDeliveryRequest";
        $message->status = 'new';
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;
        $message->reference = $reference;

        $message->comment[] = $comment;

        $message->senderOrgRegNumber = $requesterOrgRegNumber;
        $message->recipientOrgRegNumber = $archiverOrgRegNumber;

        // read org names, addresses, communications, contacts
        $this->readOrgs($message);

        $message->derogation = $derogation;

        if ($derogation) {
            $message->status = "sent";
        } else {
            $message->status = "accepted";
        }

        foreach ($archives as $archive) {
            $unitIdentifier = \laabs::newInstance("medona/unitIdentifier");
            $unitIdentifier->messageId = $message->messageId;
            $unitIdentifier->objectClass = "recordsManagement/archive";
            $unitIdentifier->objectId = (string) $archive->archiveId;

            $message->unitIdentifier[] = $unitIdentifier;
        }

        try {
            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $archiveDeliveryRequestController = \laabs::newController("$namespace/ArchiveDeliveryRequest");
                $archiveDeliveryRequestController->send($message);
            } else {
                $archiveDeliveryRequest = $this->sendMessage($message);
                $message->object = $archiveDeliveryRequest;

                $archiveDeliveryRequest->requester = $this->sendOrganization($message->senderOrg);
                if ($userName) {
                    $archiveDeliveryRequest->requester->userName = $userName;
                }

                $archiveDeliveryRequest->archivalAgency = $this->sendOrganization($message->recipientOrg);

                $message->object->unitIdentifier = $message->unitIdentifier;
            }
            $operationResult = true;

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

        return $message;
    }

    /**
     * Send an authorization request
     * @param string $messageId The message identifier
     */
    public function sendAuthorizationRequest($messageId)
    {
        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));

        if ($message->derogation) {
            $unitIdentifier = $this->sdoFactory->readChildren('medona/unitIdentifier', $message);
            $originatorOrgRegNumber = $this->archiveController->read($unitIdentifier[0]->objectId)->originatorOrgRegNumber;

            $authorizationControlAuthorityRequestController = \laabs::newController('medona/AuthorizationControlAuthorityRequest');
            $authorizationControlAuthorityRequestController->send($messageId, $originatorOrgRegNumber);
        }
    }

    /**
     * Derogation archive delivery request message
     * @param string $messageId The message identifier
     */
    public function derogation($messageId)
    {
        $this->changeStatus($messageId, "derogation");

        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));

        $message->derogation = "true";
        $this->update($message);

        $event = $this->lifeCycleJournalController->logEvent(
            'medona/authorization',
            'medona/message',
            $message->messageId,
            $message,
            true
        );

        $controlAutorityControler = \laabs::newController("medona/ControlAuthority");
        if (count($controlAutorityControler->index()) > 0) {
            $this->sendAuthorizationRequest((string) $message->messageId);
        } else {
            $this->accept((string) $message->messageId);
        }
    }

    /**
     * Accept archive delivery request message
     * @param string $messageId The message identifier
     */
    public function accept($messageId)
    {
        $this->changeStatus($messageId, "accepted");

        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));

        $event = $this->lifeCycleJournalController->logEvent(
            'medona/acceptance',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }

    /**
     * Reject the message identifier
     * @param string $messageId The message identifier
     * @param string $comment   A comment
     *
     * @return object The reply message
     */
    public function reject($messageId, $comment = null)
    {
        $this->changeStatus($messageId, "rejected");

        //$archiveDeliveryRequestReplyController = \laabs::newController('medona/ArchiveDeliveryRequestReply');

        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));

        $event = $this->lifeCycleJournalController->logEvent(
            'medona/rejection',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
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
            "status='accepted' AND type='ArchiveDeliveryRequest' AND active=true"
        );

        foreach ($messageIds as $messageId) {
            // Avoid parallel processing
            $message = $this->sdoFactory->read('medona/message', (string) $messageId);
            if ($message->status != 'accepted') {
                continue;
            }
            $this->changeStatus($message->messageId, "processing");
            $this->readOrgs($message);

            if (!empty($message->data)) {
                $message->object = json_decode($message->data);
            }

            $message->unitIdentifier = $this->sdoFactory->readChildren("medona/unitIdentifier", $message);

            try {
                $results[(string) $message->messageId] = $this->process($message);
            } catch (\Exception $e) {
                $results[(string) $message->messageId] = $e;
            }
        }

        return $results;
    }

    /**
     * Validate message against schema and rules
     * @param medona/message $message
     * @param string         $replyCode
     *
     * @return the result of process
     */
    public function process($message, $replyCode = "000")
    {
        if (is_scalar($message)) {
            $messageId = $message;
            $message = $this->sdoFactory->read('medona/message', $messageId);
            $message->unitIdentifier = $this->sdoFactory->readChildren("medona/unitIdentifier", $message);
        }

        $this->changeStatus($message->messageId, "processing");
        $archives = array();

        foreach ((array) $message->unitIdentifier as $unitIdentifier) {
            $archives[] = $this->archiveController->communicate($unitIdentifier->objectId);
        }

        $logMessage = ["message" => "%s archives are communicated", "variables"=> count($archives)];
        \laabs::notify(\bundle\audit\AUDIT_ENTRY_OUTPUT, $logMessage);

        try {
            $archiveDeliveryRequestReplyController = \laabs::newController('medona/ArchiveDeliveryRequestReply');
            $archiveDeliveryRequestReplyController->send($message, $archives, $replyCode);
            $operationResult = true;
        } catch (\Exception $e) {
            $message->status = "error";
            $this->update($message);

            $this->lifeCycleJournalController->logEvent(
                'medona/processing',
                'medona/message',
                $message->messageId,
                $message,
                false
            );

            throw $e;
        }

        $this->lifeCycleJournalController->logEvent(
            'medona/processing',
            'medona/message',
            $message->messageId,
            $message,
            true
        );

        $message->status = "processed";

        return $this->update($message);
    }

    /**
     * Export archive delivery
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
     * Get process delivery message
     *
     * @return array Array of medona/message object
     */
    public function processList()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts = [];
        $queryParts[] = "type='ArchiveDeliveryRequest'";
        $queryParts[] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts[] = "status='accepted'";
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
}
