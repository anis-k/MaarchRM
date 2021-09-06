<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle auth.
 *
 * Bundle auth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle auth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle auth.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\auth\Message;

/**
 * auth role definition
 *
 * @package auth
 *
 */
class roleImportExport
{
    /**
     * The role identifier
     *
     * @var string
     */
    public $roleId;

    /**
     * The role name
     *
     * @var string
     */
    public $roleName;

    /**
     * The role description
     *
     * @var string
     */
    public $description;

    /**
     * Status of role
     *
     * @var string
     */
    public $enabled;

    /**
     * Array of user story names
     *
     * @var string
     */
    public $privileges;

    /**
     *  The security level of role
     *
     * @var string
     */
    public $securityLevel;

    public function __toString()
    {
        return json_encode(get_object_vars($this));
    }
}
