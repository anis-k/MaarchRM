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
 * Class for originating agency request auhorisation message
 * 
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class AuthorizationOriginatingAgencyRequest extends AuthorizationRequest
{
    /**
     * Send authorization originating agency request
     * @param string $requestMessage         The request message identifier
     * @param string $originatorOrgRegNumber The originating agency reg number
     *
     * @return medona/message
     */
    public function send($requestMessage, $originatorOrgRegNumber)
    {
        if (is_scalar($requestMessage)) {
            $requestMessage = $this->read($requestMessage);
        }

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->schema = $requestMessage->schema;
        $message->type = "AuthorizationOriginatingAgencyRequest";
        $message->status = 'sent';
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;
        $message->reference = $requestMessage->reference."_AuthorizationOriginatingAgency";

        $message->authorizationReference = $requestMessage->reference;
        $message->authorizationRequesterOrgRegNumber = $requestMessage->senderOrgRegNumber;
        $message->authorizationReason = $requestMessage->type;

        $message->senderOrgRegNumber = $requestMessage->recipientOrgRegNumber;
        $message->recipientOrgRegNumber = $originatorOrgRegNumber;
        $message->comment = $requestMessage->object->comment;

        $this->readOrgs($message);

        $message->authorizationRequestContent = \laabs::newInstance('medona/AuthorizationRequestContent');
        $message->authorizationRequestContent->authorizationReason = $requestMessage->type;
        $message->authorizationRequestContent->requestDate = $requestMessage->date;
        $message->authorizationRequestContent->unitIdentifier = $requestMessage->unitIdentifier;
        $message->authorizationRequestContent->requester = $requestMessage->senderOrg;

        try {
            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $authorizationOriginatingAgencyRequestController = \laabs::newController("$namespace/AuthorizationOriginatingAgencyRequest");
                $authorizationOriginatingAgencyRequestController->send($message);
            } else {
                $authorizationOriginatingAgencyRequest = $this->sendMessage($message);
                $message->object = $authorizationOriginatingAgencyRequest;

                $authorizationOriginatingAgencyRequest->archivalAgency = $this->sendOrganization($message->senderOrg);

                $authorizationOriginatingAgencyRequest->originatingAgency = $this->sendOrganization($message->recipientOrg);

                $message->object->unitIdentifier = $requestMessage->unitIdentifier;

                $this->generate($message);
                $this->save($message);
            }
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
            true
        );

        $this->create($message);

        return $message;
    }

    /**
     * Accepte the authorization originating agency request
     *
     * @param string $messageId
     */
    public function accept($messageId)
    {
        $message = $this->sdoFactory->read('medona/message', $messageId);

        $requestMessage = $this->sdoFactory->read('medona/message', array('reference' => $message->authorizationReference, 'type' => $message->authorizationReason, 'senderOrgRegNumber' => $message->authorizationRequesterOrgRegNumber));
        $requestMessage = $this->read($requestMessage->messageId);

        $this->changeStatus($messageId, "accepted");

        // Send reply
        \laabs::newController('medona/AuthorizationOriginatingAgencyRequestReply')->send($message, '000');

        $controlAutorityControler = \laabs::newController("medona/ControlAuthority");
        // Check if control authority is set on system
        if (count($controlAutorityControler->index()) > 0) {
            $message->status == "control_authorization_wait";
            $authorizationControlAuthorityRequestController = \laabs::newController('medona/AuthorizationControlAuthorityRequest');
            $authorizationControlAuthorityRequestController->send($requestMessage, $message->recipientOrgRegNumber);
        } else {
            $requestMessageController = \laabs::newController('medona/'.$requestMessage->type);
            $requestMessageController->accept($requestMessage);
        }

        $this->lifeCycleJournalController->logEvent(
            'medona/acceptance',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }

    /**
     * Reject the authorization originating agency request
     *
     * @param string $messageId
     * @param string $comment
     */
    public function reject($messageId, $comment = null)
    {
        $message = $this->sdoFactory->read('medona/message', $messageId);

        $requestMessage = $this->sdoFactory->read('medona/message', array('reference' => $message->authorizationReference, 'type' => $message->authorizationReason, 'senderOrgRegNumber' => $message->authorizationRequesterOrgRegNumber));
        $this->changeStatus($requestMessage->messageId, "rejected");

        $this->changeStatus($messageId, "rejected");

        $requestMessage->unitIdentifier = $this->sdoFactory->readChildren('medona/unitIdentifier', $requestMessage);

        foreach ($requestMessage->unitIdentifier as $unitIdentifier) {
            $this->archiveController->setStatus((string) $unitIdentifier->objectId, "preserved");
        }

        \laabs::newController('medona/AuthorizationOriginatingAgencyRequestReply')->send($messageId, 'rejected', $comment);

        $this->lifeCycleJournalController->logEvent(
            'medona/rejection',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }
}
