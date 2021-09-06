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
 * Archival profile modification notification
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class ArchivalProfileModificationNotification extends ArchiveNotification
{

    /**
     * Send a new transfer reply
     * @param string $reference The notification message identifier
     * @param array  $profile   The profile
     *
     * @return The message generated
     */
    public function send($reference, $profile)
    {
        $archivalAgreements = $this->archivalAgreementController->getByProfileReference((string) $profile->archivalProfileId);
        $recipients = array();

        foreach ($archivalAgreements as $archivalAgreement) {
            foreach ($archivalAgreement->originatorOrgIds as $originatorOrgId) {
                $recipient = $this->orgController->read($originatorOrgId);
                $recipients[] = $recipient;
            }
        }

        $messages = array();

        if (!\laabs::hasBundle("seda")) {
            $this->retentionRuleController = \laabs::newController('recordsManagement/retentionRule');
            $retentionRule = $this->retentionRuleController->read($profile->retentionRuleCode);
            $profile->retentionRuleDuration = $retentionRule->duration;
            $profile->retentionRuleFinalDisposition = $retentionRule->finalDisposition;

            $this->accessRuleController = \laabs::newController('recordsManagement/accessRule');
            $accessRule = $this->accessRuleController->edit($profile->accessRuleCode);
            $profile->accessRuleDuration = $accessRule->duration;
        }

        foreach ($recipients as $recipient) {
            $message = \laabs::newInstance('medona/message');
            $message->messageId = \laabs::newId();
            $message->type = "ArchivalProfileModificationNotification";
            $message->schema = "medona";
            $message->status = "sent";
            $message->date = \laabs::newDatetime(null, "UTC");
            $message->receptionDate = $message->date;

            $message->reference = $reference."_".$recipient->registrationNumber;

            $senderOrg = $this->orgController->getOrgsByRole('archiver')[0];
            $message->senderOrgRegNumber = $senderOrg->registrationNumber;
            $message->senderOrgName = $senderOrg->orgName;

            $message->recipientOrgRegNumber = $recipient->registrationNumber;
            $message->recipientOrgName = $recipient->orgName;

            $message->profile = $profile;

            try {
                $this->generate($message);
                $this->save($message);
                $operationResult = true;

            } catch (\Exception $e) {
                $message->status = "error";
                $operationResult = false;
            }

            $this->lifeCycleJournalController->logEvent(
                'medona/sending',
                'medona/message',
                $message->messageId,
                $message,
                $operationResult
            );

            $this->create($message);

            $messages[] = $message;
        }

        return $messages;
    }
}
