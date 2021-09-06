<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\adminArchive;

/**
 * User story admin retention rule
 * @author Dylan CORREIA <dylan.correia@maarch.org>
 */
interface adminStorageRuleInterface
{
    /**
     *  Storage rules index
     * 
     * @return recordsManagement/storageRule/index
     *
     * @uses recordsManagement/storageRule/readIndex
     * 
     */
    public function readStoragerules();

    /**
     *  List the storage rules
     * 
     * @return recordsManagement/storageRule/listRules
     *
     * @uses recordsManagement/storageRule/readIndex
     * 
     */
    public function readStoragerulestable();

    /**
     *  Create an storage rule
     * @param recordsManagement/storageRule $storageRule The storage rule
     * 
     * @return recordsManagement/storageRule/create
     *
     * @uses recordsManagement/storageRule/create
     *
     */
    public function createStoragerule($storageRule);

    /**
     *  Read an storage rule
     * 
     * @return recordsManagement/storageRule/edit The storage rule
     *
     * @uses recordsManagement/storageRule/read_code_
     *
     */
    public function readStoragerule_code_($code);

    /**
     *  Update a storage rule
     * @param recordsManagement/retentionRule $storageRule The retention rule
     * 
     * @return recordsManagement/storageRule/update
     *
     * @uses recordsManagement/storageRule/update
     *
     */
    public function updateStoragerule_code_($storageRule);

    /**
     *  Delete an access rule
     * @param string $code The access rule code
     * 
     * @return recordsManagement/storageRule/delete
     *
     * @uses recordsManagement/storageRule/delete_code_
     *
     */
    public function deleteStoragerule_code_($code);
}