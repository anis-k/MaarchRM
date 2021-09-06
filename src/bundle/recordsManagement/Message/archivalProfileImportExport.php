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
namespace bundle\recordsManagement\Message;

/**
 * Class model that represents an archival profile
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 */
class archivalProfileImportExport
{
    /**
     * The reference
     *
     * @var string
     * @pattern #^[A-Za-z][A-Za-z0-9_-]*$#
     * @notempty
     */
    public $reference;

    /**
     * The name
     *
     * @var string
     */
    public $name;

    /**
     * The description of the rule
     *
     * @var string
     */
    public $description;

    /**
     * The archive description schema
     *
     * @var string
     */
    public $descriptionSchema;

    /**
     * The archive description class
     *
     * @var string
     */
    public $descriptionClass;

    /**
     * The starting date of the retention rule calculation
     *
     * @var string
     */
    public $retentionStartDate;

    /**
     * The retention rule code
     *
     * @var string
     */
    public $retentionRuleCode;

    /**
     *  The access code
     *
     * @var string
     */
    public $accessRuleCode;


    /**
     * The document accepts user custom indexes
     *
     * @var boolean
     */
    public $acceptUserIndex;

    /**
     * The archive accepts sub archive without profile
     *
     * @var boolean
     */
    public $acceptArchiveWithoutProfile;

    /**
     * The archive description level
     *
     * @var string
     */
    public $fileplanLevel;

    /**
     *  The list of profile description
     *
     * @var string
     */
    public $archiveDescriptions;

    /**
     *  The list of child archival profiles
     *
     * @var string
     */
    public $childrenProfiles;
}
