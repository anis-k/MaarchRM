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

namespace bundle\recordsManagement;

/**
 * Interface for management of storage rules
 *
 * @package Recordsmanagement
 */
interface storageRuleInterface
{
    /**
     * List the storage rules
     *
     * @action recordsManagement/storageRule/index
     */
    public function readIndex();

    /**
     * Create a storage rule
     * @param recordsManagement/storageRule $storageRule The storage rule
     *
     * @action recordsManagement/storageRule/create
     */
    public function create($storageRule);

    /**
     * Read a storage rule
     *
     * @action recordsManagement/storageRule/read
     */
    public function read_code_();

    /**
     * Update a storage rule
     * @param recordsManagement/storageRule $storageRule The storage rule
     *
     * @action recordsManagement/storageRule/update
     */
    public function update($storageRule);

    /**
     * Delete a storage rule
     *
     * @action recordsManagement/storageRule/delete
     */
    public function delete_code_();
}