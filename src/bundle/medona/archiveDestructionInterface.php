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
 * Archive destruction interface
 *
 * @package Medona
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface archiveDestructionInterface extends messageInterface
{

    /**
     * Get ingoing transfer messages
     *
     * @action medona/ArchiveDestructionRequest/listReception
     */
    public function readIncominglist();

    /**
     * Count transfer messages
     *
     * @action medona/ArchiveDestructionRequest/count
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
     * @action medona/ArchiveDestructionRequest/history
     */
    public function readHistory($reference = null, $archiver = null, $originator = null, $depositor = null, $archivalAgreement = null, $fromDate = null, $toDate = null, $status = null);

    /**
     * Validate archive delivery request
     *
     * @action medona/ArchiveDestructionRequest/validate
     */
    public function updateRequestvalidation_messageId_();

    /**
     * Reject archive delivery request
     * @param string $messageId The message identifier
     * @param string $comment   A comment
     *
     * @action medona/ArchiveDestructionRequest/reject
     */
    public function updateRequestrejection($messageId, $comment = null);

    /**
     * Process all archive destructions
     *
     * @action medona/ArchiveDestructionRequest/processAll
     */
    public function updateProcessall();

    /**
     * Flag archives for disposal
     * @param array  $archiveIds The archives ids
     * @param string $comment    The comment of modification
     * @param string $identifier Message identifier
     * @param string $format     Message format
     * @return boolean
     *
     * @request UPDATE medona/dispose
     * @action medona/ArchiveDestruction/dispose
     *
     */
    public function updateDisposearchives($archiveIds, $comment = null, $identifier = null, $format = null);
}
