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
namespace bundle\mades\Controller;
/**
 * Class for AuthorizationControlAuthorityRequestReply message handling
 */
class AuthorizationControlAuthorityRequestReply extends abstractMessage
{
    /**
     * Send message with all contents embedded
     * @param string $message The message
     */
    public function send($message)
    {
        $authorizationControlAuthorityRequestReply = abstractMessage::send($message);

        $authorizationControlAuthorityRequestReply->replyCode = $this->sendReplyCode($message->replyCode);

        $authorizationControlAuthorityRequestReply->authorizationControlAuthorityRequestIdentifier =
            $message->requestReference;
        $authorizationControlAuthorityRequestReply->authorizationControlAuthorityRequestReplyIdentifier =
            $message->reference;
        
        $authorizationControlAuthorityRequestReply->archivalAgency = $this->sendOrganization($message->recipientOrg);

        $authorizationControlAuthorityRequestReply->controlAuthority = $this->sendOrganization($message->senderOrg);
    }
}
