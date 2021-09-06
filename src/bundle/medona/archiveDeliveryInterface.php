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
 * Archive delivery interface
 *
 * @package Medona
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
interface archiveDeliveryInterface extends messageInterface
{
    /**
     * Get ingoing delivery messages
     *
     * @action medona/ArchiveDeliveryRequestReply/listReception
     */
    public function readRequestReplyList();

    /**
     * Get ingoing delivery messages
     *
     * @action medona/ArchiveDeliveryRequest/listReception
     */
    public function readRequestList();

    /**
     * Count delivery messages
     *
     * @action medona/ArchiveDeliveryRequest/count
     */
    public function readCount();

    /**
     * Export archive delivery request reply
     *
     * @action medona/ArchiveDeliveryRequest/export
     */
    public function read_messageId_exportArchive();

    /**
     * Send an authorization request
     *
     * @action medona/ArchiveDeliveryRequest/sendAuthorizationRequest
     */
    public function updateSendauthorizationrequest_messageId_();

    /**
     * Accept archive delivery request
     *
     * @action medona/ArchiveDeliveryRequest/derogation
     */
    public function updateRequestderogation_messageId_();

    /**
     * Reject archive delivery request
     * @param string $messageId The message identifier
     * @param string $comment   A comment
     * @action medona/ArchiveDeliveryRequest/reject
     */
    public function updateRequestrejection($messageId, $comment = null);

    /**
     * Valdiate archive delivery request
     *
     * @action medona/ArchiveDeliveryRequest/validate
     */
    public function updateRequestvalisation_messageId_();

    /**
     * Get destruction message history
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
     * @action medona/ArchiveDeliveryRequest/history
     */
    public function readHistory($reference = null, $archiver = null, $originator = null, $depositor = null, $archivalAgreement = null, $fromDate = null, $toDate = null, $status = null);

    
    /**
     * Search message by sender / recipient / reference / date
     * @param string $sender
     * @param string $recipient
     * @param date   $fromDate
     * @param date   $toDate
     * @param string $reference
     *
     * @action medona/ArchiveDeliveryRequest/listSending
     */
    public function readSearchoutgoing($sender = null, $recipient = null, $fromDate = null, $toDate = null, $reference = null);

    /**
     * Process archive delivry
     *
     * @action medona/ArchiveDeliveryRequest/processBatch
     */
    public function updateProcessBatch();

    /**
     * Process archive delivery message
     *
     * @action medona/ArchiveDeliveryRequest/process
     */
    public function updateDelivery_message_process();

    /**
     * Deliver an archive
     * @param mixed  $archiveIds    The identifier of archive or a list of identifiers
     * @param string $identifier    The medona message reference
     * @param boolean $derogation   Ask for an authorization
     * @param string $comment       The message comment
     * @param string $format        The message format
     *
     * @action medona/ArchiveDeliveryRequest/requestDelivery
     */
    public function createDelivery($archiveIds, $identifier = null, $derogation = false, $comment = null, $format = null);

    /**
     * Get process delivery messages
     *
     * @action medona/ArchiveDeliveryRequest/processList
     */
    public function readDeliveryProcessList();
}
