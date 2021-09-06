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
 * Archive transfer interface
 *
 * @package Medona
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
interface archiveTransferInterface
    extends messageInterface
{
    /**
     * Get ingoing transfer messages
     *
     * @action medona/ArchiveTransfer/listReception
     */
    public function readIncominglist();

    /**
     * Get outgoing transfer messages
     *
     * @action medona/ArchiveTransfer/listSending
     */
    public function readOutgoinglist();

    /**
     * Get outgoing transfer messages
     *
     * @param string $reference         Reference
     * @param string $archiver          Archiver
     * @param string $originator        Originator
     * @param string $depositor         Depositor
     * @param string $archivalAgreement Archival agreement
     * @param date   $fromDate          From date
     * @param date   $toDate            To date
     * @param string $status            Status
     *
     * @action medona/ArchiveTransfer/history
     */
    public function readHistory($reference = null, $archiver = null, $originator = null, $depositor = null, $archivalAgreement = null, $fromDate = null, $toDate = null, $status = null);

    /**
     * Count transfer messages
     *
     * @action medona/ArchiveTransfer/count
     */
    public function readCount();

    /**
     * Receive message with all contents embedded
     * @param mixed  $messageFile The message binary contents or a filename
     * @param array  $attachments An array of filenames for attachments
     * @param string $schema      The schema of the message file
     * @param string $filename    The message file name
     *
     * @action medona/ArchiveTransfer/receive
     */
    public function create($messageFile, $attachments = array(), $schema = null, $filename = null);

    /**
     * Receive message with all contents embedded
     *
     * @param mixed  $package  Message binary contents or a filename
     * @param string $connector    Connector to use
     * @param array  $params       Parameters to adapt message
     *
     * @action medona/ArchiveTransfer/receiveSource
     */
    public function createSource($package, $connector, $params = []);

    /**
     * Validate messages against schema and rules
     *
     * @action medona/ArchiveTransfer/validateBatch
     */
    public function updateValidateBatch();

    /**
     * Validate message
     *
     * @action medona/ArchiveTransfer/validate
     */
    public function updateValidate_messageId_();

    /**
     * Validate draft message
     *
     * @action medona/ArchiveTransfer/validateDraft
     */
    public function updateValidatedraft_messageId_();

    /**
     * Validate messages against schema and rules
     *
     * @action medona/ArchiveTransfer/processBatch
     */
    public function updateProcessBatch();

    /**
     * Validate archive transfer
     *
     * @action medona/ArchiveTransfer/validate
     */
    public function updateRequestvalidate_messageId_();

    /**
     * Accept archive transfer
     *
     * @action medona/ArchiveTransfer/accept
     */
    public function updateRequestacceptance_messageId_();

    /**
     * Reject archive transfer
     * @param string $messageId The message identifier
     * @param string $comment   A comment
     *
     * @action medona/ArchiveTransfer/reject
     */
    public function updateRequestrejection($messageId, $comment = null);

    /**
     * Process archive transfer
     *
     * @action medona/ArchiveTransfer/process
     */
    public function updateProcess_messageId_();

    /*********** OUTGOING TRANSFER ******************/

    /**
     * Create outgoing transfer
     * @param array $archiveIds             List of archives
     * @param string $archiverOrgRegNumber  An Archiver
     * @param string $comment               A comment
     * @param string $identifier            An identifier
     * @param string $format                The message format
     *
     * @action medona/ArchiveTransferSending/setForTransfer
     */
    public function updateOutgoingtransferSending($archiveIds, $archiverOrgRegNumber, $comment, $identifier = null, $format = null);

    /**
     * Get ingoing transfer messages
     *
     * @action medona/ArchiveTransferSending/listReception
     */
    public function readOutgoingtransferReception();

    /**
     * Get process transfer messages
     *
     * @action medona/ArchiveTransferSending/processList
     */
    public function readOutgoingtransferProcessList();

    /**
     * Get outgoing transfer messages
     * @param string    $reference
     * @param string    $archiver
     * @param string    $originator
     * @param string    $depositor
     * @param string    $archivalAgreement
     * @param date      $fromDate
     * @param date      $toDate
     *
     * @action medona/ArchiveTransferSending/history
     */
    public function readOutgoingtransferHistory($reference = null, $archiver = null, $originator = null, $depositor = null, $archivalAgreement = null, $fromDate = null, $toDate = null, $status = null);

    /**
     * Export outgoing transfer
     *
     * @action medona/ArchiveTransferSending/export
     */
    public function readOutgoingtransfer_messageId_exportArchive();


    /**
     * Reject outgoing transfer
     * @param string $messageId The message identifier
     * @param string $comment   A comment
     *
     * @action medona/ArchiveTransferSending/reject
     */
    public function updateOutgoingtransfer_messageId_reject($messageId, $comment = null);

    /**
     * Acquite outgoing transfer
     *
     * @action medona/ArchiveTransferSending/acknowledge
     */
    public function updateOutgoingtransfer_messageId_acknowledge();

    /**
     * Process outgoing transfer
     *
     * @action medona/ArchiveTransferSending/process
     */
    public function updateOutgoingtransfer_messageId_Process();
}
