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
namespace presentation\maarchRM\UserStory\archiveDeposit;

/**
 * User story of deposit processing
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface processTransferInterface
{
    /**
     * Search form
     *
     * @uses medona/archiveTransfer/readIncominglist
     * @return medona/message/transferIncomingList
     */
    public function readTransferReceived();

    /**
     * Validate archive transfer
     *
     * @uses medona/archiveTransfer/updateRequestvalidate_messageId_
     * @return medona/message/validateArchiveTransfer
     */
    public function updateTransfervalidate_messageId_();

    /**
     * Accept archive transfer
     *
     * @uses medona/archiveTransfer/updateRequestacceptance_messageId_
     * @return medona/message/acceptArchiveTransfer
     */
    public function updateTransferacceptance_messageId_();

    /**
     * Reject archive transfer
     * @param string $messageId
     * @param string $comment
     *
     * @uses medona/archiveTransfer/updateRequestrejection
     * @return medona/message/rejectArchiveTransfer
     */
    public function updateTransferrejection_messageId_($messageId, $comment = null);

    /**
     * Modify archive transfer
     *
     * @return seda/messageComposer/editMessage
     * @uses  seda/ArchiveTransferComposition/read_messageId_
     */
    public function readTransfermodify_messageId_();

    /**
     * Update a draft seda message
     * @param object $messageObject The message
     *
     * @return seda/messageComposer/update
     * @uses seda/ArchiveTransferComposition/update_messageId_
     */
    public function updateSedaArchivetransfer_messageId_($messageObject);

    /**
     * Process archive transfer
     *
     * @uses medona/archiveTransfer/updateProcess_messageId_
     * @return medona/message/processArchiveTransfer
     */
    public function updateTransferprocess_messageId_();
}
