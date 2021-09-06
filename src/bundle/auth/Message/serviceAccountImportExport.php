<?php

/*
 * Copyright (C) 2020 Maarch
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
 * userAccount message
 *
 * @package ImportExport
 * @author  Jerome Boucher <jerome.boucher@maarch.org>
 */
class serviceAccountImportExport
{
    /**
     * @var string
     * @pattern #^[A-Za-z][A-Za-z0-9_.@]*[A-Za-z]$#
     * @notempty
     */
    public $accountName;

    /**
     * The displayed name
     *
     * @var string
     * @notempty
     */
    public $displayName;

    /**
     * @var string
     * @notempty
     */
    public $emailAddress;

    /**
     * The user password (base64 encoded and salted)
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $organizations;

    /**
     * @var string
     */
    public $privileges;

    /**
     * @var bool
     */
    public $locked = false;

    /**
     * @var bool
     */
    public $enabled = true;

    /**
     * @var string
     */
    public $ownerOrgRegNumber;

    /**
     * @var bool
     */
    public $isAdmin;
}
