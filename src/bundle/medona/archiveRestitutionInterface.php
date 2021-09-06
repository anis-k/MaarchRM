<?php
/*
 * Copyright (C) 2016 Maarch
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
 * Archive restitution interface
 *
 * @package Medona
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 */
interface archiveRestitutionInterface extends messageInterface
{
    /**
     * Get request validation restitution messages
     *
     * @action medona/ArchiveRestitutionRequest/requestValidationList
     */
    public function readRequestValidationList();

    /**
     * Get request process restitution messages
     *
     * @action medona/ArchiveRestitutionRequest/requestProcessList
     */
    public function readRequestProcessList();

    /**
     * Get validation restitution messages
     *
     * @action medona/ArchiveRestitution/validationList
     */
    public function readValidationList();

    /**
     * Get validation restitution messages
     *
     * @action medona/ArchiveRestitution/processList
     */
    public function readProcessList();

    /**
     * Export archive delivery request reply
     *
     * @action medona/ArchiveRestitution/export
     */
    public function read_messageId_exportArchive();

    /**
     * Get outgoing restitution messages
     *
     * @action medona/ArchiveRestitution/outgoinglist
     */
    public function readOutgoinglist();

    /**
     * Count restitution messages
     *
     * @action medona/ArchiveRestitutionRequest/count
     */
    public function readCount();

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
     * @action medona/ArchiveRestitution/history
     */
    public function readHistory($reference = null, $archiver = null, $originator = null, $depositor = null, $archivalAgreement = null, $fromDate = null, $toDate = null , $status = null);

    /**
     * Accept archive restitution request
     *
     * @action medona/ArchiveRestitutionRequest/accept
     */
    public function updateRequestacceptance_messageId_();

    /**
     * Reject archive restitution request
     *
     * @param string $messageId Identifier of message
     * @param string $comment   The comment
     *
     * @action medona/ArchiveRestitutionRequest/reject
     */
    public function updateRequestrejection($messageId, $comment);

     /**
     * Validate archive restitution
     *
     * @action medona/ArchiveRestitution/validate
     */
    public function update_messageId_Accept();

    /**
     * Reject archive restitution
     * @param string $messageId The message identifier
     * @param string $comment   A comment
     *
     * @action medona/ArchiveRestitution/reject
     */
    public function update_messageId_reject($messageId, $comment = null);

    /**
     * Acquite archive restitution request
     *
     * @action medona/ArchiveRestitution/acknowledge
     */
    public function update_messageId_acknowledge();

    /**
     * Process archive restitution
     *
     * @action medona/ArchiveRestitution/process
     */
    public function update_messageId_Process();

    /**
     * Destruct all restitued archives of restitution message
     *
     * @action medona/ArchiveRestitutionRequest/destructAll
     */
    public function updateDestructall();

    /**
     * Process archive restitution
     *
     * @action medona/ArchiveRestitutionRequest/process
     */
    public function updateProcess_message_();


    /**
     * Processes messages
     *
     * @action medona/ArchiveRestitutionRequest/processBatch
     */
    public function updateProcessbatch();

    /**
     * Flag archives for restitution
     * @param array  $archiveIds Array of archive identifier
     * @param string $identifier The reference for message
     * @param string $comment    A comment
     * @param string $format     Message format
     *
     * @action medona/ArchiveRestitution/setForRestitution
     *
     */
    public function updateSetforrestitution($archiveIds, $identifier = null, $comment = null, $format = null);
}
