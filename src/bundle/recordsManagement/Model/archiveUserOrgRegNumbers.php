<?php
/*
 * Copyright (C) 2019 Maarch
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
namespace bundle\recordsManagement\Model;
/**
 * View to get/set archive user org access
 *
 * @package RecordsManagement
 * @author  Cyril Vazquez (Maarch) <cyril.vazquez@maarch.org>
 *
 * @pkey [archiveId]
 *
 * @substitution recordsManagement/archive
 */
class archiveUserOrgRegNumbers
{
    /**
     * The archive identifier
     *
     * @var id
     * @xvalue generate-id
     * @notempty
     */
    public $archiveId;

    /**
     * The user org registration numbers
     *
     * @var tokenList
     */
    public $userOrgRegNumbers;

    /**
     * @var timestamp
     */
    public $lastModificationDate;
}