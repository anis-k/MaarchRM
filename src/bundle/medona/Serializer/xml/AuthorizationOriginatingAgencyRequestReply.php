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
namespace bundle\medona\Serializer\xml;

/**
 * Class for AuthorizationOriginatingAgencyRequestReply message handling
 */
class AuthorizationOriginatingAgencyRequestReply
    extends abstractBusinessReplyMessage
{

    /**
     * Generate a new authorization originating agency request reply
     * @param medona/message $message
     */
    public function generate($message)
    {
        parent::generate($message);   
        
        $this->setReplyCode($message->replyCode);
        
        $this->setMessageRequestIdentifier($message->requestReference);
        
        $this->setAuthorizationRequestReplyIdentifier($message->authorizationReference);
        
        $this->addUnitIdentifiers();
        
        $this->setOrganization($message->senderOrgRegNumber, "Requester");
        
        $this->setOrganization($message->recipientOrgRegNumber, "ArchivalAgency");
        
    }

}