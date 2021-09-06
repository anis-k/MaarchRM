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

namespace bundle\recordsManagement\Controller;

/**
 * Management of the storage rule
 *
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class storageRule
{

    protected $sdoFactory;
    protected $lifeCycleJournalController;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->lifeCycleJournalController = \laabs::newController("lifeCycle/journal");
    }

    /**
     * List the storage rules
     *
     * @return recordsManagement/storageRule[] The list of storage rules
     */
    public function index()
    {
        $retentionRules = $this->sdoFactory->find('recordsManagement/storageRule');

        return $retentionRules;
    }

    /**
     * Create a storage rule
     * @param recordsManagement/storageRule $storageRule The storage rule
     *
     * @return boolean The request result
     */
    public function create($storageRule)
    {
        try {
            return $this->sdoFactory->create($storageRule, 'recordsManagement/storageRule');
        } catch (\Exception $e) {
            throw new \bundle\recordsManagement\Exception\retentionRuleException("Storage rule not created.");
        }
    }

    /**
     * Read a storage rule
     * @param string $code The storage rule code
     *
     * @return recordsManagement/storageRule The storage rule
     */
    public function read($code)
    {
        return $this->sdoFactory->read('recordsManagement/storageRule', $code);
    }

    /**
     * Update a storage rule
     * @param recordsManagement/storageRule $retentionRule The storage rule
     *
     * @return boolean The request result
     */
    public function update($storageRule)
    {
        try {
            $res = $this->sdoFactory->update($storageRule, 'recordsManagement/storageRule');

        } catch (\core\Exception $e) {
            throw new \bundle\recordsManagement\Exception\retentionRuleException("Storage rule not updated.");
        }

        return $res;
    }

    /**
     * Delete a storage rule
     * @param string $code The storage rule code
     *
     * @return boolean The request result
     */
    public function delete($code)
    {
        $storageRule = $this->sdoFactory->read('recordsManagement/storageRule', $code);

        if (!$storageRule) {
            return false;
        }
        try {
            $this->sdoFactory->delete($storageRule);
        } catch (\Exception $e) {
            throw new \bundle\recordsManagement\Exception\retentionRuleException("Storage rule not deleted.");
        }

        return true;
    }

    /**
     * Get storageRule by code
     * @param string $code The storage rule code
     *
     * @return recordsManagement/storageRule
     */
    public function getRetentionRule($code)
    {
        $retentionRule = $this->sdoFactory->find('recordsManagement/storageRule', "code = '$code'");

        return $retentionRule;
    }
}
