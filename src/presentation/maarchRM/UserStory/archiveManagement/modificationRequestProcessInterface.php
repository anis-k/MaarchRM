<?php

/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of Maarch RM.
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
 * along with Maarch RM.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\archiveManagement;
/**
 * Interface for archive modification
 */
interface modificationRequestProcessInterface
{
    /**
     * Get modification requests
     * @uses medona/archiveModificationRequest/read
     * 
     * @return medona/message/modificationRequestList
     */
    public function readModificationrequestList();

    /**
     * Get the deliveries messages
     *
     * @uses medona/archiveModificationRequest/readHistory
     * @return medona/message/ModificationRequestHistory
     */
    public function readModificationrequestHistory();

    /**
     * Accept modification request
     * @param string $comment A comment
     *
     * @uses medona/archiveModificationRequest/update_messageId_Accept
     * @return medona/archiveModification/modificationRequestAccepted
     */
    public function updateModificationrequest_messageId_Accept();

    /**
     * Reject modification request
     * @param string $comment The message comment
     *
     * @uses medona/archiveModificationRequest/update_messageId_Reject
     * 
     * @return medona/archiveModification/modificationRequestRejected
     */
    public function updateModificationrequest_messageId_Reject($comment = null);
}
