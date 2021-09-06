<?php
/*
 * Copyright (C) 2019 Maarch
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
 * Archive modification request interface
 *
 * @package Medona
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */
interface archiveModificationRequestInterface extends messageInterface
{
    /**
     * Get ingoing modification request messages
     *
     * @action medona/ArchiveModificationRequest/listReception
     */
    public function read();

    /**
     * Create request on one or several archive units
     * @param mixed  $archiveIds The identifier of archive or a list of identifiers
     * @param string $identifier The medona message reference
     * @param string $comment    The message comment
     * @param string $format     Message format
     *
     * @action medona/ArchiveModificationRequest/send
     */
    public function create($archiveIds, $identifier = null, $comment = null, $format = null);

    /**
     * Reject archive modification request
     * @param string $comment   A comment
     * 
     * @action medona/ArchiveModificationRequest/reject
     */
    public function update_messageId_Reject($comment = null);

    /**
     * Accept archive modification request
     * 
     * @action medona/ArchiveModificationRequest/accept
     */
    public function update_messageId_Accept($comment = null);

    /**
     * Get destruction message history
     *
     * @param string $reference         Reference
     * @param string $archiver          Archiver
     * @param string $requester         Requester
     * @param date   $fromDate          From date
     * @param date   $toDate            To date
     * @param string $status            Status
     *
     * @action medona/ArchiveModificationRequest/history
     */
    public function readHistory($reference = null, $archiver = null, $requester = null, $fromDate = null, $toDate = null, $status = null);


}
