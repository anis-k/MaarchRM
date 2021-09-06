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
namespace bundle\medona;

/**
 * Archive authorization interface
 *
 * @package Medona
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface archiveAuthorizationInterface extends messageInterface
{
    /**
     * Get outgoing archive communication authorization request messages
     *
     * @action medona/AuthorizationRequest/listCommunicationSending
     */
    public function readCommunicationoutgoinglist();

    /**
     * Get ingoing archive communication authorization request messages
     *
     * @action medona/AuthorizationRequest/listCommunicationReception
     */
    public function readCommunicationincominglist();

    /**
     * Count archive communication authorization request messages
     *
     * @action medona/AuthorizationRequest/countCommunication
     */
    public function readCommunicationcount();

    /**
     * Get outgoing archive communication authorization request messages
     *
     * @action medona/AuthorizationRequest/listDestructionSending
     */
    public function readDestructionoutgoinglist();

    /**
     * Get ingoing archive communication authorization request messages
     *
     * @action medona/AuthorizationRequest/listDestructionReception
     */
    public function readDestructionincominglist();

    /**
     * Count archive communication authorization request messages
     *
     * @action medona/AuthorizationRequest/countDestruction
     */
    public function readDestructioncount();

    /**
     * Accept authorization originating agency request
     *
     * @action medona/AuthorizationOriginatingAgencyRequest/accept
     */
    public function updateOriginatingagencyrequestacceptance_messageId_();

    /**
     * Reject authorization control authority request
     *
     * @action medona/AuthorizationOriginatingAgencyRequest/reject
     */
    public function updateOriginatingagencyrequestrejection($messageId, $comment = null);

    /**
     * Get ingoing delivery messages
     *
     * @action medona/AuthorizationControlAuthorityRequest/listReception
     */
    public function readIncominglist();

    /**
     * Accept authorization originating agency request
     *
     * @action medona/AuthorizationControlAuthorityRequest/accept
     */
    public function updateControlauthorityrequestacceptance_messageId_();

    /**
     * Reject authorization originating agency request
     *
     * @action medona/AuthorizationControlAuthorityRequest/reject
     */
    public function updateControlauthorityrequestrejection($messageId, $comment = null);
}
