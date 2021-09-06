<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\recordsManagement\Controller;

/**
 * Trait for archives restitution
 */
trait archiveOutgoingTransferTrait
{
    /**
     * Transfer an archive
     * @param string $archiveId The identifier of the archive
     *
     * @return recordsManagement/archive The archive transferred
     */
    public function outgoingTransfer($archiveId)
    {
        $this->verifyIntegrity($archiveId);

        $archive = $this->retrieve((string)$archiveId, true);

        $statusChanged = $this->setStatus((string) $archive->archiveId, "transfered");

        $valid = count($statusChanged["success"]) ? true : false;

        // Life cycle journal
        $this->logOutgoingTransfer($archive, $valid);

        return $valid ? $archive : null;
    }
}
