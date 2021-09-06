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
 * Class for archiveAuthorizationTrait
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class AuthorizationRequest extends abstractMessage
{

    /**
     * Get received communication authorization request message
     *
     * @return array Array of medona/message object
     */
    public function listCommunicationReception()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();
        $queryParts = array();

        $queryParts[] = "type='AuthorizationControlAuthorityRequest'";
        $queryParts[] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts[] = "authorizationReason = 'ArchiveDeliveryRequest'";
        $queryParts[] = "status='sent'";
        $queryParts[] = "active=true";

        return $this->sdoFactory->find("medona/message", implode(' and ', $queryParts));
    }

    /**
     * Get received communication authorization request message
     *
     * @return array Array of medona/message object
     */
    public function listCommunicationSending()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();
        $queryParts = array();

        $queryParts[] = "type=['AuthorizationOriginatingAgencyRequest', 'AuthorizationControlAuthorityRequest']";
        $queryParts[] = "senderOrgRegNumber=$registrationNumber";
        $queryParts[] = "authorizationReason = 'ArchiveDeliveryRequest'";
        $queryParts[] = "active=true";

        return $this->sdoFactory->find("medona/message", implode(' and ', $queryParts));
    }

    /**
     * Count communication authorization message
     *
     * @return array Number of received and sent messages
     */
    public function countCommunication()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();
        $res = array();
        $queryParts = array();

        $queryParts["type"] = "type=['AuthorizationOriginatingAgencyRequest', 'AuthorizationControlAuthorityRequest']";
        $queryParts["registrationNumber"] = "senderOrgRegNumber=$registrationNumber";
        $queryParts["authorizationReason"] = "authorizationReason = 'ArchiveDeliveryRequest'";
        $queryParts["active"] = "active=true";
        $res['sent'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        $queryParts["registrationNumber"] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts["status"] = "status='sent'";
        $res['received'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        return $res;
    }

    /**
     * Get received destruction authorization request message
     *
     * @return array Array of medona/message object
     */
    public function listDestructionReception()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();
        $queryParts = array();

        $queryParts["type"] = "type=['AuthorizationOriginatingAgencyRequest', 'AuthorizationControlAuthorityRequest']";
        $queryParts["registrationNumber"] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts["authorizationReason"] = "authorizationReason = 'ArchiveDestructionRequest'";
        $queryParts["status"] = "status='sent'";
        $queryParts["active"] = "active=true";

        return $this->sdoFactory->find("medona/message", implode(' and ', $queryParts));
    }

    /**
     * Get received destruction authorization request message
     *
     * @return array Array of medona/message object
     */
    public function listDestructionSending()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();
        $queryParts = array();

        $queryParts[] = "type=['AuthorizationOriginatingAgencyRequest', 'AuthorizationControlAuthorityRequest']";
        $queryParts[] = "senderOrgRegNumber=$registrationNumber";
        $queryParts[] = "authorizationReason = 'ArchiveDestructionRequest'";
        $queryParts[] = "active=true";

        return $this->sdoFactory->find("medona/message", implode(' and ', $queryParts));
    }

    /**
     * Count communication authorization message
     *
     * @return array Number of received and sent messages
     */
    public function countDestruction()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();
        $res = array();
        $queryParts = array();

        $queryParts["type"] = "type=['AuthorizationOriginatingAgencyRequest', 'AuthorizationControlAuthorityRequest']";
        $queryParts["registrationNumber"] = "senderOrgRegNumber=$registrationNumber";
        $queryParts["authorizationReason"] = "authorizationReason = 'ArchiveDestructionRequest'";
        $queryParts["active"] = "active=true";
        $res['sent'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        $queryParts["registrationNumber"] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts["status"] = "status='sent'";
        $res['received'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        return $res;
    }
}
