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
 * Class for AuthorizationControlAuthorityRequest message handling
 */
class AuthorizationControlAuthorityRequest
    extends abstractBusinessRequestMessage
{

    /**
     * Generate a new authorization request
     * @param medona/message $message
     */
    public function generate($message)
    {
        parent::generate($message);
        
        $this->setAuthorizationRequestContent();

        $this->setOrganization($message->senderOrgRegNumber, "ArchivalAgency");

        $this->setOrganization($message->recipientOrgRegNumber, "ControlAuthority");
    }

    protected function setAuthorizationRequestContent()
    {
        $authorizationRequestContentElement = $this->message->xml->createElement('AuthorizationRequestContent');
        $this->message->xml->documentElement->appendChild($authorizationRequestContentElement);

        $authorizationReasonElement = $this->message->xml->createElement('AuthorizationReason', $this->message->authorizationRequestContent->authorizationReason);
        $authorizationRequestContentElement->appendChild($authorizationReasonElement);

        $requestDateElement = $this->message->xml->createElement('RequestDate', (string) $this->message->authorizationRequestContent->requestDate);
        $authorizationRequestContentElement->appendChild($requestDateElement);

        foreach ($this->message->authorizationRequestContent->unitIdentifier as $unitIdentifier) {
            $unitIdentifierElement = $this->message->xml->createElement('UnitIdentifier', $unitIdentifier->objectId);
            $authorizationRequestContentElement->appendChild($unitIdentifierElement);
        }

        $requesterElement = $this->message->xml->createElement('Requester');
        $authorizationRequestContentElement->appendChild($requesterElement);
        $requesterIdentifierElement = $this->message->xml->createElement('Identifier', (string) $this->message->authorizationRequestContent->requester->registrationNumber);
        $requesterElement->appendChild($requesterIdentifierElement);
    }



}
