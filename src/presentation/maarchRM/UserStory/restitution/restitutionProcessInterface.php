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
 * User story - Restitutions
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface restitutionProcessInterface
{
    /**
     * Get restitution requests to process
     *
     * @uses medona/archiveRestitution/readProcessList
     * @return medona/message/restitutionIncomingList
     */
    public function readRestitutionProcess();

    /**
     * Process restitution
     *
     * @uses medona/ArchiveRestitution/update_messageId_process
     * @return medona/message/processArchiveRestitution
     */
    public function updateRestitution_messageId_Process();

    /**
     * Reject archive restitution request
     * @param string $messageId The message identifier
     * @param string $comment   The comment
     *
     * @uses medona/archiveRestitution/updateRequestrejection
     * @return medona/message/rejectArchiveRestitutionRequest
     */
    public function updateRestitutionrequest_messageId_reject($messageId, $comment = null);
}
