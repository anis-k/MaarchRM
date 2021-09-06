<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle seda.
 *
 * Bundle seda is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle seda is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle seda.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\mades\Controller;

/**
 * Class for AuthorizationOriginatingAgencyRequest message handling
 */
class AuthorizationOriginatingAgencyRequest extends abstractMessage
{
    /**
     * Generate a new archive delivery request reply
     * @param medona/message $message
     */
    public function send($message)
    {
        $authorizationOriginatingAgencyRequest = abstractMessage::send($message);

        $authorizationOriginatingAgencyRequest->authorizationOriginatingAgencyRequestIdentifier =
            $message->reference;

        $authorizationOriginatingAgencyRequest->request = $this->sendRequest($message->authorizationRequestContent);

        $authorizationOriginatingAgencyRequest->archivalAgency = $this->sendOrganization($message->senderOrg);

        $authorizationOriginatingAgencyRequest->originatingAgency = $this->sendOrganization($message->recipientOrg);
    }

}
