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
 * User story - Process restitution validation
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface restitutionValidationInterface
{
    /**
     * Get restitution to validation
     *
     * @uses medona/ArchiveRestitution/readValidationList
     * @return medona/message/restitutionValidationIncomingList
     */
    public function readRestitutionValidation();

    /**
     * Download archive
     *
     * @uses medona/ArchiveRestitution/read_messageId_exportArchive
     * @return medona/message/messageExport
     */
    public function readRestitution_messageId_Export();

    /**
     * Process restitution
     *
     * @uses medona/ArchiveRestitution/update_messageId_acknowledge
     * @return medona/message/processArchiveRestitution
     */
    public function updateRestitution_messageId_Acknowledge();

    /**
     * Reject archive restitution
     * @param string $messageId The message identifier
     * @param string $comment   The comment
     *
     * @uses medona/archiveRestitution/update_messageId_reject
     * @return medona/message/rejectArchiveRestitution
     */
    public function updateRestitution_messageId_reject($messageId, $comment = null);
}
