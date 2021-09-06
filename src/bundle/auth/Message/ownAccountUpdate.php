<?php

/*
 * Copyright (C) 2019 Maarch
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
 * ownAccountUpdate message
 *
 * @package Auth
 * @author  Jerome Boucher <jerome.boucher@maarch.og>
 */
class ownAccountUpdate
{
    /**
     * @var string
     * @pattern #^[A-Za-z][A-Za-z0-9_.@-]*$#
     * @notempty
     */
    public $accountName;

    /**
     * @var string
     */
    public $emailAddress;

    /**
     * The user fisrt name (given name)
     *
     * @var string
     */
    public $firstName;

    /**
     * The user last name (family name, surname)
     *
     * @var string
     */
    public $lastName;

    /**
     * The displayed name
     *
     * @var string
     * @notempty
     */
    public $displayName;

    /**
     * The person title (civility)
     *
     * @var string
     */
    public $title;
}
