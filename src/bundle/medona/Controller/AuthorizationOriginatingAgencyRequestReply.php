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
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class AuthorizationOriginatingAgencyRequestReply extends abstractMessage
{
    /**
     * Send a new authorization originating agency request reply
     * @param string $requestIdentifier The request message identifier
     * @param string $replyCode         The reply code
     * @param string $comment           A comment
     *
     * @return The reply message generated
     */
    public function send($requestIdentifier, $replyCode = "000", $comment = null)
    {
        $requestMessage = $this->read($requestIdentifier);

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->type = "AuthorizationOriginatingAgencyRequestReply";
        $message->schema = $requestMessage->schema;
        $message->status = "sent";
        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;
        $message->replyCode = $replyCode;

        if ($comment) {
            $message->comment[] = $comment;
        }

        $message->reference = $requestMessage->reference.'_Reply_'.date("Y-m-d_H-i-s");
        $message->requestReference = $requestMessage->reference;
        $message->authorizationReason = $requestMessage->authorizationReason;
        $requestMessage->replyReference = $message->reference;

        $message->senderOrgRegNumber = $requestMessage->recipientOrgRegNumber;
        $senderOrg = $this->orgController->getOrgByRegNumber($message->senderOrgRegNumber);
        $message->senderOrgName = $senderOrg->orgName;

        $message->recipientOrgRegNumber = $requestMessage->senderOrgRegNumber;
        $recipientOrg = $this->orgController->getOrgByRegNumber($message->recipientOrgRegNumber);
        $message->recipientOrgName = $recipientOrg->orgName;

        try {
            // read org names, addresses, communications, contacts
            $this->readOrgs($message);
        } catch (\Exception $e) {
            $recipientOrg = \laabs::getToken("ORGANIZATION");

            $message->recipientOrgRegNumber = $recipientOrg->registrationNumber;
            $message->recipientOrg = $recipientOrg;

            $message->senderOrgRegNumber = $recipientOrg->registrationNumber;
            $message->senderOrg = $recipientOrg;
        }

        try {
            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $authorizationOriginatingAgencyRequestReplyController = \laabs::newController("$namespace/AuthorizationOriginatingAgencyRequestReply");
                $authorizationOriginatingAgencyRequestReplyController->send($message);
            } else {
                $authorizationOriginatingAgencyRequestReply = $this->sendMessage($message);
                $message->object = $authorizationOriginatingAgencyRequestReply;

                $authorizationOriginatingAgencyRequestReply->archivalAgency = $this->sendOrganization($message->senderOrg);

                $authorizationOriginatingAgencyRequestReply->originatingAgency = $this->sendOrganization($message->recipientOrg);

                $this->generate($message);
                $this->save($message);
            }

            $this->update($requestMessage);
        } catch (\Exception $e) {
            $message->status = "invalid";
            $this->create($message);

            $this->logValidationErrors($message, $e);

            throw $e;
        }

        $this->create($message);

        return $message;
    }
}
