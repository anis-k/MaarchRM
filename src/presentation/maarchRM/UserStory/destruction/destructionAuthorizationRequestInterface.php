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
namespace presentation\maarchRM\UserStory\destruction;

/**
 * User story for destruction authorization request
 *
 * @package Medona
 * @author Alexandre MORIN <alexandre.morin@maarch.org>
 */
interface destructionAuthorizationRequestInterface
{
    /******************     Get  incoming destruction authorization request      ************************/

    /**
     * Get received archive destruction authorization request messages
     *
     * @uses medona/archiveAuthorization/readDestructionincominglist
     * @return medona/message/destructionAuthorizationIncomingList
     */
    public function readDestructionAuthorizationrequests();


    /******************     Accept/reject auhorization originating agency request      ************************/

    /**
     * Accept authorization originating agency request
     * @param string $messageId The message identifier
     *
     * @uses medona/archiveAuthorization/updateOriginatingagencyrequestacceptance_messageId_
     * @return medona/message/acceptAuthorizationOriginatingAgencyRequest
     */
    public function updateAuthorizationoriginatingagencyrequest_messageId_Accept($messageId);

    /**
     * Reject authorization originating agencyy request
     * @param string $messageId The message identifier
     * @param string $comment   The message comment
     *
     * @uses medona/archiveAuthorization/updateOriginatingagencyrequestrejection
     * @return medona/message/rejectAuthorizationOriginatingAgencyRequest
     */
    public function updateAuthorizationoriginatingagencyrequest_messageId_Reject($messageId, $comment =null);


    /******************     Accept/reject control authority request      ************************/

    /**
     * Accept authorization control authority request
     * @param string $messageId The message identifier
     *
     * @uses medona/archiveAuthorization/updateControlauthorityrequestacceptance_messageId_
     * @return medona/message/acceptAuthorizationControlAuthorityRequest
     */
    public function updateAuthorizationcontrolauthorityrequest_messageId_Accept($messageId);

    /**
     * Reject authorization control authority request
     * @param string $messageId The message identifier
     * @param string $comment   The message comment
     *
     * @uses medona/archiveAuthorization/updateControlauthorityrequestrejection
     * @return medona/message/rejectAuthorizationControlAuthorityRequest
     */
    public function updateAuthorizationcontrolauthorityrequest_messageId_Reject($messageId, $comment = null);
}