<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle organization.
 *
 * Bundle organization is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle organization is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle organization.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\organization\Message;

/**
 * Organization
 *
 * @package Organization
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class organizationImportExport
{
    /**
     * The organization name (legal)
     *
     * @var string
     */
    public $orgName;

    /**
     * Another organization legal or known form of name
     *
     * @var string
     */
    public $otherOrgName;

    /**
     * The displayed name of the object
     *
     * @var string
     * @notempty
     */
    public $displayName;

    /**
     * The organization legal classification
     *
     * @var string
     */
    public $legalClassification;

    /**
     * The organization business type
     *
     * @var string
     */
    public $businessType;

    /**
     * The organization description
     *
     * @var string
     */
    public $description;

    /**
     * The organization type code
     *
     * @var string
     */
    public $orgTypeCode;

    /**
     * The organization role codes list
     *
     * @var tokenlist
     */
    public $orgRoleCodes;

    /**
     * The registration number
     *
     * @var string
     * @notempty
     */
    public $registrationNumber;

    /**
     * The organization tax identifier
     *
     * @var string
     */
    public $taxIdentifier;

    /**
     * The organization begin date
     *
     * @var date
     */
    public $beginDate;

    /**
     * The organization end date
     *
     * @var date
     */
    public $endDate;

    /**
     * The organization parent orgId
     *
     * @var string
     */
    public $parentOrgRegNumber;

    /**
     * The organization owner orgId
     *
     * @var string
     */
    public $ownerOrgRegNumber;

    /**
     * The organization is an orgUnit
     *
     * @var bool
     */
    public $isOrgUnit;

    /**
     * Status of organization
     *
     * @var bool
     */
    public $enabled = true;
}
