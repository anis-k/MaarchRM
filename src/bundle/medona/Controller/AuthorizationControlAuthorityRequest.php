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
 * Class for control authority authorization request message
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class AuthorizationControlAuthorityRequest extends AuthorizationRequest
{
    /**
     * Send a new authorization control authority request
     * @param mixed $requestMessage         The message identifier
     * @param mixed $originatorOrgRegNumber The originator
     *
     * @return medona/message The message generated
     */
    public function send($requestMessage, $originatorOrgRegNumber=null)
    {
        if (is_scalar($requestMessage)) {
            $requestMessage = $this->read($requestMessage);
        }

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->schema = $requestMessage->schema;
        $message->type = "AuthorizationControlAuthorityRequest";
        $message->status = 'sent';
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;
        $message->reference = $requestMessage->reference."_AuthorizationControlAuthority";

        $message->authorizationReference = $requestMessage->reference;
        $message->authorizationRequesterOrgRegNumber = $requestMessage->senderOrgRegNumber;
        $message->authorizationReason = $requestMessage->type;

        $message->senderOrgRegNumber = $requestMessage->recipientOrgRegNumber;
        $message->comment = $requestMessage->object->comment;

        if ($requestMessage->type == "ArchiveDeliveryRequest") {
            $controlAuthority = $this->getControlAuthority($requestMessage->senderOrg->orgId);
        } elseif ($requestMessage->type == "ArchiveDestructionRequest") {
            $orgController = \laabs::newController("organization/organization");
            $originator = $orgController->getOrgByRegNumber($originatorOrgRegNumber);

            $controlAuthority = $this->getControlAuthority($originator->orgId);

        }

        $message->recipientOrgRegNumber = $controlAuthority->registrationNumber;

        $this->readOrgs($message);

        $message->authorizationRequestContent = \laabs::newInstance('medona/AuthorizationRequestContent');
        $message->authorizationRequestContent->authorizationReason = $requestMessage->type;
        $message->authorizationRequestContent->requestDate = $requestMessage->date;
        $message->authorizationRequestContent->unitIdentifier = $requestMessage->unitIdentifier;
        $message->authorizationRequestContent->requester = $requestMessage->senderOrg;

        try {
            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $authorizationControlAuthorityRequestController = \laabs::newController("$namespace/AuthorizationControlAuthorityRequest");
                $authorizationControlAuthorityRequestController->send($message);
            } else {
                $authorizationControlAuthorityRequest = $this->sendMessage($message);
                $message->object = $authorizationControlAuthorityRequest;

                $authorizationControlAuthorityRequest->archivalAgency = $this->sendOrganization($message->senderOrg);

                $authorizationControlAuthorityRequest->controlAuthority = $this->sendOrganization($message->recipientOrg);

                $message->object->unitIdentifier = $requestMessage->unitIdentifier;

                $this->generate($message);
                $this->save($message);
            }
            $operationResult = true;
        } catch (\Exception $e) {
            $message->status = "invalid";
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

        return $message;
    }

    /**
     * Accepte the authorization control authority request
     *
     * @param string $messageId The message identifier
     */
    public function accept($messageId)
    {
        if (is_scalar($messageId)) {
            $message = $this->read($messageId);
        } else {
            $message = $messageId;
        }

        $this->changeStatus($messageId, "accepted");

        $requestMessage = $this->sdoFactory->read('medona/message', array('reference' => $message->authorizationReference, 'type' => $message->authorizationReason, 'senderOrgRegNumber' => $message->authorizationRequesterOrgRegNumber));

        $requestMessageController = \laabs::newController('medona/'.$requestMessage->type);
        $requestMessageController->accept((string) $requestMessage->messageId);

        \laabs::newController('medona/AuthorizationControlAuthorityRequestReply')->send($message);

        $this->lifeCycleJournalController->logEvent(
            'medona/acceptance',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }

    /**
     * Reject the authorization control authority request
     *
     * @param string $messageId The message identifier
     * @param string $comment   A comment
     */
    public function reject($messageId, $comment = null)
    {
        if (is_scalar($messageId)) {
            $message = $this->read($messageId);
        } else {
            $message = $messageId;
        }

        $this->changeStatus($messageId, "rejected");

        $requestMessage = $this->sdoFactory->read('medona/message', array('reference' => $message->authorizationReference, 'type' => $message->authorizationReason, 'senderOrgRegNumber' => $message->authorizationRequesterOrgRegNumber));
        $this->changeStatus($requestMessage->messageId, "rejected");

        $requestMessage->unitIdentifier = $this->sdoFactory->readChildren('medona/unitIdentifier', $requestMessage);

        foreach ($requestMessage->unitIdentifier as $unitIdentifier) {
            $this->archiveController->setStatus((string) $unitIdentifier->objectId, "preserved");
        }

        \laabs::newController('medona/AuthorizationControlAuthorityRequestReply')->send($message, 'rejected', $comment);

        $this->lifeCycleJournalController->logEvent(
            'medona/rejection',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }

    /**
     * Get control authority
     * @param string $orgId Organization identifier/regNumber
     *
     * @return id Control authority identifier
     */
    public function getControlAuthority($orgId)
    {
        $controlAuthorityController = \laabs::newController("medona/ControlAuthority");
        $orgController = \laabs::newController("organization/organization");

        if ($this->sdoFactory->exists('medona/controlAuthority', $orgId)) {
            $controlAuthority = $controlAuthorityController->read($orgId);

            return $orgController->read((string) $controlAuthority->controlAuthorityOrgUnitId);
        }

        $ancestorOrgs = $orgController->readParentOrg($orgId);

        foreach ($ancestorOrgs as $ancestorOrg) {
            /*if (!in_array('originator', (array) $ancestorOrg->orgRoleCodes)) {
                continue;
            }*/

            if ($this->sdoFactory->exists('medona/controlAuthority', $ancestorOrg->orgId)) {
                $controlAuthority = $controlAuthorityController->read($ancestorOrg->orgId);
                return $orgController->read((string) $controlAuthority->controlAuthorityOrgUnitId);
            }
        }

        if ($this->sdoFactory->exists('medona/controlAuthority', '*')) {
            $controlAuthority = $controlAuthorityController->read('*');

            return $orgController->read((string) $controlAuthority->controlAuthorityOrgUnitId);
        }

        throw \laabs::Bundle('medona')->newException('controlAuthorityException', 'None control authority parameters.');
    }
}
