<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\destruction;

/**
 * User story destruction process interface
 *
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface destructionProcessInterface
{
    /**
     * Get incoming list destruction messages
     *
     * @uses medona/archiveDestruction/readIncominglist
     * @return medona/message/destructionIncomingList
     */
    public function readDestructionProcesslist();

    /**
     * Accept destruction request
     *
     * @uses medona/archiveDestruction/updateRequestvalidation_messageId_
     * @return medona/message/validateArchiveDestructionRequest
     */
    public function updateDestructionrequest_messageId_Accept();

    /**
     * Reject destruction request
     * @param string $messageId The message identifier
     * @param string $comment   The comment
     *
     * @uses medona/archiveDestruction/updateRequestrejection
     * @return medona/message/rejectArchiveDestructionRequest
     */
    public function updateDestructionrequest_messageId_Reject($messageId, $comment = null);
}
