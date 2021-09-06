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

namespace presentation\maarchRM\UserStory\restitution;

/**
 * User story - restitution request validation interface
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface restitutionRequestValidationInterface
{
    /**
     * Get restitution requests to validate
     *
     * @uses medona/archiveRestitution/readRequestValidationList
     * @return medona/message/restitutionRequestIncomingList
     */
    public function readRestitutionRequestvalidation();

    /**
     * Accept archive restitution request
     *
     * @uses medona/archiveRestitution/updateRequestacceptance_messageId_
     * @return medona/message/acceptArchiveRestitutionRequest
     */
    public function updateRestitutionrequest_messageId_accept();

    /**
     * Reject archive restitution request
     * @param string $messageId The message identifier
     * @param string $comment   The comment
     *
     * @uses medona/archiveRestitution/updateRequestrejection
     * @return medona/message/rejectArchiveRestitutionRequest
     */
    public function updateRestitutionrequest_messageId_reject($messageId, $comment = null);

    /**
     * Process archive restitution
     *
     * @uses medona/archiveRestitution/updateProcess_message_
     * @return medona/message/processArchiveRestitutionRequest
     */
    public function updateRestitutionrequest_message_process();
}
