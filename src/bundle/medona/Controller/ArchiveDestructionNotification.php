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
 * Archive destruction notification
 *
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class ArchiveDestructionNotification extends ArchiveNotification
{
    /**
     * Send a new transfer reply
     * @param medona/message $destructionRequest The destruction request message
     * @param array          $archives           The destroyed archives
     *
     * @return The message generated
     */
    public function send($destructionRequest, $archives, $format = null)
    {
        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->type = "ArchiveDestructionNotification";

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

        $message->reference = $destructionRequest->reference."_DestructionNotification";

        $message->senderOrgRegNumber = $archives[0]->archiverOrgRegNumber;
        $message->recipientOrgRegNumber = $archives[0]->originatorOrgRegNumber;
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
            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $archiveModificationNotificationController = \laabs::newController("$namespace/ArchiveDestructionNotification");
                $archiveModificationNotificationController->send($message);
            } else {
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
