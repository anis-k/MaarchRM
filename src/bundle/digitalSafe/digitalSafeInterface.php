<?php
/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of bundle digitalSafe.
 *
 * Bundle digitalSafe is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle digitalSafe is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalSafe.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\digitalSafe;

use bundle\digitalResource\Model\digitalResource;

/**
 * API digital safe
 *
 * @package digitalSafe
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface digitalSafeInterface
{
    /**
     * Depose numerical object
     *
     * @param array                             of digitalResource/digitalResource
     * @param object                            The descriptive metadata object
     * @param string                            Originator organisation Archive identifier
     *
     * @action digitalSafe/digitalSafe/receive
     */
    public function create_originatorOwnerOrgRegNumber__originatorOrgRegNumber_(
        $digitalResources,
        $descriptionObject = null,
        $originatorArchiveId = null
    );

    /**
     * Counting numerical objects according to filters
     *
     * @param string $fromDate
     * @param string $toDate
     * @param string $originatorArchiveId
     *
     * @action digitalSafe/digitalSafe/counting
     */
    public function read_originatorOwnerOrgRegNumber__originatorOrgRegNumber_Count(
        $fromDate = null,
        $toDate = null,
        $originatorArchiveId = null
    );

    /**
     * Read all events on numerical objects according to filters
     *
     * @param string $originatorOrgRegNumber
     * @param string $fromDate
     * @param string $toDate
     * @param string $originatorArchiveId
     * @param string $archiveId
     *
     * @action digitalSafe/digitalSafe/journal
     *
     */
    public function read_originatorOwnerOrgRegNumber_events(
        $originatorOrgRegNumber = null,
        $fromDate = null,
        $toDate = null,
        $originatorArchiveId = null,
        $archiveId = null
    );

    /**
     * List numerical objects
     *
     * @param string $fromDate
     * @param string $toDate
     * @param string $originatorArchiveId
     * @param string $archiveId
     *
     * @action digitalSafe/digitalSafe/listing
     */
    public function read_originatorOwnerOrgRegNumber__originatorOrgRegNumber_(
        $fromDate = null,
        $toDate = null,
        $originatorArchiveId = null,
        $archiveId = null
    );

    /**
     * Destruct a numeric object
     *
     * @param string $archiveId Archive identifier
     *
     * @action digitalSafe/digitalSafe/destruct
     *
     */
    public function delete_originatorOwnerOrgRegNumber__originatorOrgRegNumber__archiveId_();

    /**
     * Read a numerical object resource by its id
     *
     * @action digitalSafe/digitalSafe/consultation
     */
    public function read_originatorOwnerOrgRegNumber__originatorOrgRegNumber__archiveId_();

    /**
     * Verify numerical object integrity
     * @param string $archiveId Archive identifier
     *
     * @action digitalSafe/digitalSafe/verifyIntegrity
     *
     */
    public function read_originatorOwnerOrgRegNumber__originatorOrgRegNumber__archiveId_Integritycheck();

    /**
     * Read numerical object technical metadata
     *
     * @action digitalSafe/digitalSafe/retrieve
     */
    public function read_originatorOwnerOrgRegNumber__originatorOrgRegNumber__archiveId_Metadata();
}
