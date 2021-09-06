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
 * Organization list
 *
 * @package Organization
 * @author  Alexandre Morin <alexandre.morin@maarch.org>
 */
class organizationList
{
    /**
     * The organization's identifier
     *
     * @var id
     */
    public $orgId;

    /**
     * The displayed name of the object
     *
     * @var string
     * @notempty
     */
    public $displayName;
    
    /**
     * The registration number
     *
     * @var string
     * @notempty
     */
    public $registrationNumber;

    /**
     * The organization's parent orgId
     *
     * @var string
     */
    public $parentOrgId;

    /**
     * The organization's parent orgName
     *
     * @var string
     */
    public $parentOrgName;

    /**
     * The organization owner orgId
     *
     * @var string
     */
    public $ownerOrgId;

    /**
     * The organization is a service
     *
     * @var bool
     */
    public $isOrgUnit;

    /**
     * The children organizations
     *
     * @var organization/organization[]
     */
    public $organization;

    /**
     * Status of organization
     *
     * @var bool
     */
    public $enabled = true;
}