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
use core\Encoding\json;

/**
 * Trait for archive restitution
 *
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 **/

class ArchiveTransferSending extends abstractMessage
{
    /**
     * Get received archive tranfer message
     *
     * @return array Array of medona/message object
     */
    public function listReception()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts = [];
        $queryParts[] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts[] = "type='ArchiveTransfer'";
        $queryParts[] = "active=true";
        $queryParts[] = "isIncoming=false";
        $queryParts[] = "status != 'processed' AND status != 'error' AND status != 'invalid' AND status !='draft' AND status !='template' AND status !='rejected' AND status !='acknowledge'";

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
        $queryParts[] = "type='ArchiveTransfer'";
        $queryParts[] = "senderOrgRegNumber=$registrationNumber";
        $queryParts[] = "status='acknowledge'";
        $queryParts[] = "active=true";
        $queryParts[] = "isIncoming=false";

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
    public function history($reference = null, $archiver = null, $originator = null, $depositor = null, $archivalAgreement = null, $fromDate = null, $toDate = null, $status = null)
    {
        return $this->search("ArchiveTransfer", $reference, $archiver, $originator, $depositor, $archivalAgreement, $fromDate, $toDate, $status, false);
    }

    /**
     * Flag for transfer
     * @param array  $archiveIds                Array of archive identifier
     * @param string $archiverOrgRegNumber      The archival agency
     * @param string $comment                   The message comment
     * @param string $identifier                The medona message reference
     * @param string $format                    The message format
     *
     * @return array The result of the operation
     */
    public function setForTransfer($archiveIds, $archiverOrgRegNumber, $comment, $identifier = null, $format = null)
    {
        $senderOrg = \laabs::getToken('ORGANIZATION');
        if (!$senderOrg) {
            throw \laabs::newException('medona/invalidMessageException', "No current organization choosen");
        }

        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $senderOrgRegNumber = $senderOrg->registrationNumber;
        $user = \laabs::getToken('AUTH');
        $userName = false;
        if($user) {
            $userAccountController = \laabs::newController('auth/userAccount');
            $user = $userAccountController->edit($user->accountId);
            $userName = $user->accountName;
        }

        $archives = [];
        $result = array('success' => array(), 'error' => array());
        $counter = 0;

        foreach ($archiveIds as $archiveId) {
            $archive = $this->archiveController->retrieve($archiveId, true);

            try {

                $transferable = $this->isTransferable($archive);
            
            } catch (\bundle\medona\notTransferableArchiveException $notTransferableArchiveException) {
                $result["error"][] = $archive->archiveId;

                continue;
            }
            
            $result["success"][] = $archive->archiveId;
            $archives[] = $archive;
        }

        if (!empty($archives)) {
            if (!$identifier) {
                $identifier = "archiveTransfer_".date("Y-m-d_H-i-s");
            }

            $reference = $identifier;
            $i = 1;
            
            $unique = array(
                'type' => 'ArchiveTransfer',
                'senderOrgRegNumber' => $senderOrgRegNumber,
                'reference' => $reference,
            );

            while ($this->sdoFactory->exists("medona/message", $unique)) {
                $i++;
                $unique['reference'] = $reference = $identifier.'_'.$i;
            }
            
            $recipientOrgReqNumber = $archiverOrgRegNumber;

            $this->send($reference, $archives, $senderOrgRegNumber, $recipientOrgReqNumber, $comment, $format);
        }
        return $result;
    }


    public function send($reference, $archives, $senderOrg, $recipientOrg, $comment = null, $format = null)
    {
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

        $message->type = "ArchiveTransfer";
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

                $archive->lifeCycleEvent = $this->lifeCycleJournalController->getObjectEvents($archive->archiveId, 'recordsManagement/archive');

                $this->archiveController->setStatus($archive->archiveId, 'transferable');
            }

            $message->archive = $archives;
            $message->dataObjectCount = count($archives);

            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $archiveTransferController = \laabs::newController("$namespace/ArchiveTransfer");
                $archiveTransferController->send($message);
            } else {
                $archiveTransfer = $this->sendMessage($message);
                $message->object = $archiveTransfer;

                $archiveTransfer->transferringAgency = $this->sendOrganization($message->senderOrg);
                $archiveTransfer->archivalAgency = $this->sendOrganization($message->recipientOrg);

                $this->generate($message);
                $this->save($message);
            }

            $this->create($message);
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

        return $message;
    }

    /**
     * Export outgoing archive
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

        $this->changeStatus($messageId, "downloaded");

        return $exportResult;
    }

    /**
     * Validate archive transfer message
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
     * Validate archive transfer message
     * @param string $messageId The message identifier
     */
    public function acknowledge($messageId)
    {
        $this->changeStatus($messageId, "acknowledge");

        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));
        
        $this->lifeCycleJournalController->logEvent(
            'medona/acknowledgement',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }

    /**
     * Reject the archive transfer
     * @param string $messageId The message identifier
     * @param string $comment   The comment
     *
     */
    public function reject($messageId, $comment)
    {
        $this->changeStatus($messageId, "rejected");

        $message = $this->sdoFactory->read('medona/message', $messageId);
        $messageObject = json_decode($message->data);
        $messageObject->comment[] = $comment;
        $message->data = json_encode($messageObject);
        $this->update($message, "medona/message");

        $message->unitIdentifier = $this->sdoFactory->readChildren('medona/unitIdentifier', $message);

        foreach ($message->unitIdentifier as $unitIdentifier) {
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
     * Validate archive transfer message
     * @param string $messageId The message identifier
     */
    public function process($messageId)
    {
        $this->changeStatus($messageId, "validating");
        $message = $this->sdoFactory->read('medona/message', array("messageId" => $messageId));
        $message->active = false;

        $this->update($message, "medona/message");

        $message->unitIdentifier = $this->sdoFactory->readChildren('medona/unitIdentifier', $message);

        foreach ($message->unitIdentifier as $unitIdentifier) {
            $this->archiveController->outgoingTransfer($unitIdentifier->objectId);
        }

        $this->changeStatus($messageId, "validated");

        $uri = $this->messageDirectory . DIRECTORY_SEPARATOR . (string)$message->messageId;
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

    protected function isTransferable($archive)
    {
        $currentDate = \laabs::newTimestamp();
        $this->archiveController->checkRights($archive);

        if ($archive->status === 'frozen') {
            throw \laabs::Bundle('medona')->newException('notTransferableArchiveException', 'A frozen archive can\'t be modified.');
        }

        if ($archive->status != 'preserved') {
            throw \laabs::Bundle('medona')->newException('notTransferableArchiveException', 'An action is already in progress on this archive.');
        }

        if (isset($archive->finalDisposition) && $archive->finalDisposition != 'preservation') {
            throw \laabs::Bundle('medona')->newException('notTransferableArchiveException', 'Archive not set for preservation.');
        }

        if (isset($archive->disposalDate) && $archive->disposalDate > $currentDate) {
            throw \laabs::Bundle('medona')->newException('notTransferableArchiveException', 'Disposal date not reached.');
        }

        if (empty($archive->disposalDate) && (isset($archive->retentionRuleCode) || isset($archive->retentionDuration))) {
            throw \laabs::Bundle('medona')->newException('notTransferableArchiveException', 'Disposal date not reached.');
        }

        //if finaldisposition is not null or empty
        if (empty($archive->finalDisposition)) {
            throw \laabs::Bundle('medona')->newException('notTransferableArchiveException', "Final disposition must be advised for this action");
        }


        return $archive;
    }
}