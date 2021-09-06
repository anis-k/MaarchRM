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
namespace bundle\recordsManagement\Model;
/**
 * Class model that represents retention rule
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 *
 * @pkey [archiveId]
 * 
 * @substitution recordsManagement/archive
 * @xmlns rm maarch.org:laabs:recordsManagement
 */
class archiveRetentionRule
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
     * The retention rule code
     *
     * @var string
     */
    public $retentionRuleCode;

    /**
     * The starting date of the retention rule calculation
     *
     * @var date
     * @xpath rm:retentionStartDate
     */
    public $retentionStartDate;

    /**
     * The duration of retention
     *
     * @var duration
     * @xpath rm:retentionDuration
     */
    public $retentionDuration;

    /**
     * The action to execute when the retention rule is over
     *
     * @var string
     * @xpath rm:finalDisposition
     */
    public $finalDisposition;

    /**
     * The disposal date of the archive
     *
     * @var date
     */
    public $disposalDate;

    /**
     * The status of retention rule
     *
     * @var string
     * @xpath rm:retentionRuleStatus
     */
    public $retentionRuleStatus;

}