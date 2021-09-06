<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle medona.
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
 * Trait for all types of messages
 *
 * @author Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 */
abstract class abstractMessage extends message
{

    protected $message;

    /**
     * Load a message
     * @param medona/message $message
     */
    public function loadMessage($message)
    {
        $this->message = $message;

        if (!isset($message->xPath)) {
            $message->xPath = \laabs::newService('dependency/xml/XPath', $message->xml);
        }

        $message->xPath->registerNamespace('medona', 'org:afnor:medona:1.0');
        $message->xPath->registerNamespace('recordsManagement', 'maarch.org:laabs:recordsManagement');
        $message->xPath->registerNamespace('digitalResource', 'maarch.org:laabs:digitalResource');
    }

    protected function sendUnitIdentifier($mUnitIdentifier)
    {
        $unitIdentifier = \laabs::newInstance('medona/Identifier', (string) $mUnitIdentifier->objectId);

        switch ($mUnitIdentifier->objectClass) {
            case 'recordsManagement/archive':
                $unitIdentifier->schemeName = 'ArchivalAgencyArchiveIdentifier';
                break;
        }

        return $unitIdentifier;
    }

    protected function sendOrganization($orgOrganization)
    {
        $organization = \laabs::newInstance('medona/Organization');

        $organization->identifier = \laabs::newInstance('medona/Identifier', $orgOrganization->registrationNumber);

        $organization->organizationDescriptiveMetadataClass = 'organization/organization';

        $organization->organizationDescriptiveMetadata = $orgOrganization;

        return $organization;
    }

    protected function useArchivalAgreement($archivalAgreementReference)
    {
        $archivalAgreementController = \laabs::newController('medona/archivalAgreement');

        if (!isset($this->archivalAgreements[$archivalAgreementReference])) {
            $this->currentArchivalAgreement = $archivalAgreementController->getByReference($archivalAgreementReference);
            $this->archivalAgreements[$archivalAgreementReference] = $this->currentArchivalAgreement;
        } else {
            $this->currentArchivalAgreement = $this->archivalAgreements[$archivalAgreementReference];
        }

        return $this->currentArchivalAgreement;
    }

    protected function getMessageTypeController($schema, $messageType)
    {
        if (!isset($this->packageSchemas[$schema])) {
            $schemaConf = $this->packageSchemas[$schema];
            if (isset($schemaConf['controllers'][$messageType])) {
                return \laabs::newController($schemaConf['controllers'][$messageType]);
            }
        }

        if (\laabs::hasBundle($schema)) {
            $bundle = \laabs::bundle($schema);

            if ($bundle->hasController($messageType)) {
                return \laabs::newController($schema.'/'.$messageType);
            }
        }
    }

    protected function logValidationErrors($message, $exception)
    {
        $eventInfo = get_object_vars($message);
        $eventInfo['code'] = $exception->getCode();
        $eventInfo['info'] = $exception->getMessage();
        if (isset($exception->errors)) {
            foreach ((array) $exception->errors as $error) {
                if (is_string($error) || (is_object($error) && method_exists($error, '__toString'))) {
                    $eventInfo['info'] .= PHP_EOL. (string) $error;
                }
            }
        }

        $event = $this->lifeCycleJournalController->logEvent(
            'medona/sending',
            'medona/message',
            $message->messageId,
            $eventInfo,
            false
        );
    }
}
