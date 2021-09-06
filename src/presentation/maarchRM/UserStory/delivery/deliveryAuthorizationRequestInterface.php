<?php
/*
 * Copyright (C) 2016 Maarch
 *
 * This file is part of medona.
 *
 * medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona. If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\UserStory\delivery;

/**
 * User story - delivery authorization request
 *  @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
interface deliveryAuthorizationRequestInterface
{
    /**
     * Get sending archive communication authorization request messages
     *
     * @uses medona/archiveAuthorization/readCommunicationincominglist
     * @return medona/message/deliveryAuthorizationList
     */
    public function readDeliveryAuthorizationrequest();

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
