<?php
/* 
 * Copyright (C) 2019 Maarch
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
 * Conversion request message
 *
 * @author Cyril VAZQUEZ <cyril.vazquez@maarch.org>
 */
class ArchiveModificationRequest extends abstractMessage
{
    /**
     * Send a new a new modification request
     * @param array  $archiveIds   An array of unit identifier
     * @param string $identifier    The message identifier
     * @param string $comment      A comment
     *
     * @return The request message generated
     */
    public function send($archiveIds, $identifier = null, $comment = null)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $senderOrg = \laabs::getToken('ORGANIZATION');

        foreach ($archiveIds as $archiveId) {
            $archive = $this->archiveController->retrieve($archiveId);
            $archives[] = $archive;
        }

        $archivesByArchiver = array();
        foreach ($archives as $archive) {
            if (!isset($archivesByArchiver[$archive->archiverOrgRegNumber])) {
                $archivesByArchiver[$archive->archiverOrgRegNumber] = array();
            }

            $archivesByArchiver[$archive->archiverOrgRegNumber][] = $archive;
        }

        if (!$identifier) {
            $identifier = "archiveModificationRequest_".date("Y-m-d_H-i-s");
        }

        $messages = [];
        $i = 0;
        foreach ($archivesByArchiver as $archiverOrgRegNumber => $archives) {
            $i++;
            
            $message = \laabs::newInstance('medona/message');
            $message->messageId = \laabs::newId();

            $message->schema = false;
            /*if (\laabs::hasBundle('seda')) {
               $message->schema = "seda";
            }*/
            $message->type = "ArchiveModificationRequest";
            $message->status = 'received';
            $message->date = \laabs::newDatetime(null, "UTC");
            $message->receptionDate = $message->date;
            $message->reference = $identifier.'_'.str_pad($i, 2, '0', STR_PAD_LEFT);
            
            $message->senderOrgRegNumber = $senderOrg->registrationNumber;
            $message->recipientOrgRegNumber = $archiverOrgRegNumber;

            // read org names, addresses, communications, contacts
            $this->readOrgs($message);

            if ($comment) {
                $message->comment[] = $comment;
            }

            foreach ($archiveIds as $archiveId) {
                $unitIdentifier = \laabs::newInstance("medona/unitIdentifier");
                $unitIdentifier->messageId = $message->messageId;
                $unitIdentifier->objectClass = "recordsManagement/archive";
                $unitIdentifier->objectId = (string) $archiveId;

                $message->unitIdentifier[] = $unitIdentifier;
            }

            try {
                if ($message->schema) {
                    $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                    $archiveModificationRequestController = \laabs::newController("$namespace/ArchiveModificationRequest");
                    $archiveModificationRequestController->send($message);
                }
                $operationResult = true;
            } catch (\Exception $e) {
                $message->status = "error";
                $operationResult = false;

                throw $e;
            }

            $this->create($message);

            $this->lifeCycleJournalController->logEvent(
                'medona/sending',
                'medona/message',
                $message->messageId,
                $message,
                $operationResult
            );

            $messages[] = $message;
        }

        return $messages;
    }

    /**
     * Retrieve the list of requests
     *
     * @return array Array of medona/message object
     */
    public function listReception()
    {
        $queryParts = array();

        $queryParts[] = "type='ArchiveModificationRequest' and status='received'";

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
     * @param string $requester         Requester
     * @param date   $fromDate          From date
     * @param date   $toDate            To date
     * @param string $status            Status
     *
     * @return array Array of medona/message object
     */
    public function history(
        $reference = null,
        $archiver = null,
        $requester = null,
        $fromDate = null,
        $toDate = null,
        $status = null
    ) {
        return $this->search(
            "ArchiveModificationRequest",
            $reference,
            $archiver,
            $requester,
            null,
            null,
            $fromDate,
            $toDate,
            $status
        );
    }


    /**
     * Accept archive restitution request message
     * @param string $messageId The message identifier
     * @param string $comment   A comment from archiver
     *
     * @return medona/message The medon reply message
     */
    public function accept($messageId, $comment = null)
    {
        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));
        $message->status = 'accepted';

        if (!empty($comment)) {
            if (!empty($message->comment)) {
                $message->comment = json_decode($message->comment);
            } else {
                $message->comment = [];
            }

            $message->comment[] = $comment;
        }

        $this->update($message);

        $this->lifeCycleJournalController->logEvent(
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
     * @param string $comment   The comment
     *
     * @return object The reply message
     */
    public function reject($messageId, $comment = null)
    {
        
        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));
        $message->status = 'rejected';

        if (!empty($comment)) {
            if (!empty($message->comment)) {
                $message->comment = json_decode($message->comment);
            } else {
                $message->comment = [];
            }

            $message->comment[] = $comment;
        }

        $this->update($message);
        
        $this->lifeCycleJournalController->logEvent(
            'medona/rejection',
            'medona/message',
            $message->messageId,
            $message,
            true
        );

        
        return $message;
    }
}
