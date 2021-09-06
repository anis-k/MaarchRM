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
 * Class for ArchiveModificationNotification
 *
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class ArchiveModificationNotification extends ArchiveNotification
{

    /**
     * Send a new transfer reply
     * @param string $reference    The notification message identifier
     * @param array  $archives     The archives to send
     * @param string $senderOrg    The identifier of sender
     * @param string $recipientOrg The identifier of recipient
     * @param string $comment      The comment of modification
     * @param string $format       The message format
     *
     * @return The message generated
     */
    public function send($reference, $archives = array(), $senderOrg, $recipientOrg, $comment = false, $format = null)
    {
        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->type = "ArchiveModificationNotification";

        $schema = "mades";
        if ($format) {
            $schema = $format;
        } elseif ($archives[0]->descriptionClass === 'seda2') {
            $schema = 'seda2';
        } elseif (\laabs::hasBundle('seda')) {
            $schema = "seda";
        }

        $message->schema = $schema;

        $message->status = "sent";
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;

        $message->comment[] = $comment;

        $message->reference = $reference;

        $message->senderOrgRegNumber = $senderOrg;
        $message->recipientOrgRegNumber = $recipientOrg;

        $this->readOrgs($message); // read org names, addresses, communications, contacts

        $message->archive = $archives;

        foreach ($archives as $archive) {
            $unitIdentifier = \laabs::newInstance("medona/unitIdentifier");
            $unitIdentifier->messageId = $message->messageId;
            $unitIdentifier->objectClass = "recordsManagement/archive";
            $unitIdentifier->objectId = (string) $archive->archiveId;

            $message->unitIdentifier[] = $unitIdentifier;
        }

        $message->dataObjectCount = count($message->archive);

        try {
            if ($message->schema) {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $archiveModificationNotificationController = \laabs::newController("$namespace/ArchiveModificationNotification");
                $archiveModificationNotificationController->send($message);
            }
            $operationResult = true;
        } catch (\Exception $e) {
            $message->status = "error";
            $operationResult = false;

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
