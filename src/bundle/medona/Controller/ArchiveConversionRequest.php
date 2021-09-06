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
 * Conversion request message
 *
 * @author Alexis RAGOT <alexis.ragot@maarch.org>
 */
class ArchiveConversionRequest extends abstractMessage
{
    /**
     * Send a new a new delivery request
     * @param string $reference    The message identifier
     * @param object $senderOrg    The requesting org
     * @param object $recipientOrg The originating org
     * @param array  $documents    An array of document objects
     *
     * @return The request message generated
     */
    public function send($reference, $senderOrg, $recipientOrg, $documents)
    {
        if (!is_array($documents)) {
            $documents = array($documents);
        }

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();

        $message->schema = "medona";
        $message->type = "ArchiveConversionRequest";
        $message->status = 'processWait';
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;
        $message->reference = $reference;

        $message->senderOrgRegNumber = $senderOrg->registrationNumber;
        $message->recipientOrgRegNumber = $recipientOrg->registrationNumber;

        // read org names, addresses, communications, contacts
        $this->readOrgs($message);

        foreach ($documents as $document) {
            $unitIdentifier = \laabs::newInstance("medona/unitIdentifier");
            $unitIdentifier->messageId = $message->messageId;
            $unitIdentifier->objectClass = "documentManagement/document";
            $unitIdentifier->objectId = (string) $document->docId;

            $message->unitIdentifier[] = $unitIdentifier;
        }

        $archiveConversionRequest = $this->sendMessage($message);
        $message->object = $archiveConversionRequest;

        $archiveConversionRequest->requester = $this->sendOrganization($message->senderOrg);
        $archiveConversionRequest->archivalAgency = $this->sendOrganization($message->recipientOrg);

        $message->object->unitIdentifier = $message->unitIdentifier;

        $this->create($message);

        return $message;
    }

    /**
     * Process all archive destructions
     *
     * @return the result of process
     */
    public function processAll()
    {
        $index = $this->sdoFactory->index('medona/message', array('messageId'), "type = 'ArchiveConversionRequest' AND status = 'processWait'");
        $result = array();
        foreach ($index as $messageId) {
            try {
                $results[(string) $message->messageId] = $this->process($messageId);
            } catch (\Exception $e) {
                $results[(string) $message->messageId] = $e;
            }
        }

        return $result;
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

        $convertedDocumentIds = array();

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        foreach ($message->unitIdentifier as $unitIdentifier) {
            $docId = (string) $unitIdentifier->objectId;

            try {
                $convertedDocument = $this->archiveController->convert($docId, $messageId);

                $convertedDocumentIds[$docId] = (string) $convertedDocument->docId;
            } catch (\Exception $e) {
                throw new \bundle\medona\Exception\invalidConversionException("Error of conversion process");
            }
        }

        $message->status = "processed";
        $message->operationDate = \laabs::newDatetime(null, "UTC");

        $this->update($message);

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $convertedDocumentIds;
    }

    /**
     * Get conversion request message
     *
     * @return array Array of medona/message object
     */
    public function listConversionRequest()
    {
        $queryParts = array();

        $queryParts[] = "type='ArchiveConversionRequest'";

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
