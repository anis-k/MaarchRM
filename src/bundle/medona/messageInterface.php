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
 * Standard interface for log archive description class
 *
 * @package Medona
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 */
interface messageInterface
{
    /**
     * Search message by sender / recipient / reference / date
     *
     * @param string $type              Type
     * @param string $reference         Reference
     * @param string $archiver          Archiver
     * @param string $originator        Originator
     * @param string $depositor         Depositor
     * @param string $archivalAgreement Archival agreement
     * @param date   $fromDate          From date
     * @param date   $toDate            To date
     * @param string $status            Status
     *
     * @action medona/message/search
     */
    public function readSearch($type, $reference = null, $archiver = null, $originator = null, $depositor = null, $archivalAgreement = null, $fromDate = null, $toDate = null, $status = null);
    
    /**
     * Get outgoing transfer messages
     *
     * @action medona/message/getByReference
     */
    public function readReference($reference);

    /**
     * Read a message
     *
     * @action medona/message/read
     */
    public function read_messageId_();

    /**
     * Read a message
     *
     * @action medona/message/export
     */
    public function read_messageId_Export();

    /**
     * Retry a message
     *
     * @action medona/message/retry
     */
    public function read_messageId_Retry();

    /**
     * Read a message attachment
     *
     * @action medona/message/getDataObjectAttachment
     */
    public function read_messageId_Attachment_attachmentId_();

    /**
     * End a transaction
     *
     * @action medona/message/endTransaction
     */
    public function updateEndtransaction_messageId_();

    /**
     * Receive message with all contents embedded
     * @param string $messageFile The message binary contents OR a filename
     * @param array  $attachments An array of filenames for attachments
     *
     * @action medona/ArchiveTransfer/receive
     */
    /* public function createArchivetransfer($messageFile, $attachments = array());*/

    /**
     * Validate message against schema and rules
     * @param string $message
     *
     * @action medona/ArchiveTransfer/validate
     */
    //public function updateValidateArchivetransfer($message);

    /**
     * Count active messages for each type
     *
     * @action medona/message/countActiveMessages
     */
    public function readCount();

    /**
     * Archive messages
     *
     * @action medona/message/archiveMessages
     */
    public function updateArchive();

    /**
     * Archive a message
     *
     * @action medona/message/archiveMessage
     */
    public function update_messageId_Archive();

    /**
     * Service to remove message directory
     *
     * @action medona/message/MessageDirectoryPurge
     */
    public function deleteMessagedirectorypurge();
}
