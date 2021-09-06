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
class AuthorizationControlAuthorityRequestReply extends abstractMessage
{
     /**
     * Send a new authorization authority request reply
     * @param string $requestMessage The request message identifier
     * @param string $replyCode      The reply code
     * @param string $comment        A comment
     *
     * @return The reply message generated
     */
    public function send($requestMessage, $replyCode = "000", $comment = null)
    {
        if (is_scalar($requestMessage)) {
            $requestMessage = $this->read($requestMessage);
        }

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->type = "AuthorizationControlAuthorityRequestReply";
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
        $requestMessage->replyReference = $requestMessage->reference;

        $message->senderOrgRegNumber = $requestMessage->recipientOrgRegNumber;
        $message->recipientOrgRegNumber = $requestMessage->senderOrgRegNumber;
        $this->readOrgs($message);

        try {
            if ($message->schema != 'medona') {
                $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
                $authorizationControlAuthorityRequestReplyController = \laabs::newController("$namespace/AuthorizationControlAuthorityRequestReply");
                $authorizationControlAuthorityRequestReplyController->send($message);
            } else {
                $authorizationControlAuthorityRequestReply = $this->sendMessage($message);
                $message->object = $authorizationControlAuthorityRequestReply;

                $authorizationControlAuthorityRequestReply->archivalAgency = $this->sendOrganization($message->senderOrg);

                $authorizationControlAuthorityRequestReply->controlAuthority = $this->sendOrganization($message->recipientOrg);

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
}
