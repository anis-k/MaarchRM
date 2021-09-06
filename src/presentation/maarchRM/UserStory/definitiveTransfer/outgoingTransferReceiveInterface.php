<?php
/*
 * Copyright (C) 2015 Maarch
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
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\definitiveTransfer;

/**
 * User story for transfer sending
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface outgoingTransferReceiveInterface
{
    /**
     * Search form
     *
     * @uses medona/archiveTransfer/readOutgoingtransferReception
     * @return medona/message/outgoingTransferList
     */
    public function readOutgoingtransferReceived();

    /**
     * Download archive
     *
     * @uses medona/ArchiveTransfer/readOutgoingtransfer_messageId_exportArchive
     * @return medona/message/messageExport
     */
    public function readOutgoingtransfer_messageId_export();

    /**
     * Process restitution
     *
     * @uses medona/ArchiveTransfer/updateOutgoingtransfer_messageId_acknowledge
     * @return medona/message/processArchiveRestitution
     */
    public function updateOutgoingtransfer_messageId_Acknowledge();

    /**
     * Reject archive restitution
     * @param string $messageId The message identifier
     * @param string $comment   The comment
     *
     * @uses medona/ArchiveTransfer/updateOutgoingtransfer_messageId_reject
     * @return medona/message/rejectArchiveRestitution
     */
    public function updateOutgoingtransfer_messageId_reject($messageId, $comment = null);
}