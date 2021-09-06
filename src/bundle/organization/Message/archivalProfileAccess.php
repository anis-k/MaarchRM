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
 * Class model that represents an acces to profile for a given org unit
 *
 * @package organization
 * @author  Proser DE LAURE (Maarch) <prosper.delaure@maarch.org>
 *
 */
class archivalProfileAccess
{
    /**
     * The organization identifier
     *
     * @var id
     * @notempty
     */
    public $orgId;

    /**
     * The archival profile reference
     *
     * @var string
     * @notempty
     */
    public $archivalProfileReference;

    /**
     * The access is read only
     *
     * @var boolean
     */
    public $originatorAccess;

    /**
     * The service level reference
     *
     * @var string
     */
    public $serviceLevelReference;

    /**
     * The user access
     *
     * @var json
     */
    public $userAccess;
}
