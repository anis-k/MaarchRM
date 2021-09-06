<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle medona.
 *
 * Bundle medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\medona\Controller;

/**
 * Class of controlAuthority
 *
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
class controlAuthority
{
    protected $sdoFactory;

    /**
     * Constructor of controlAuthority class
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Get all relations between control authorities and originators
     *
     * @return array The list of control authority by originator
     */
    public function index()
    {
        return $this->sdoFactory->find('medona/controlAuthority');
    }

    /**
     * Add a relation between a control authority and an originator
     * @param medona/controlAuthority $controlAuthority Control authority of originator $
     *
     * @return bool
     */
    public function create($controlAuthority)
    {
        if ($this->sdoFactory->exists('medona/controlAuthority', $controlAuthority)) {
            throw \laabs::Bundle('medona')->newException('controlAuthorityException', 'The relation control authority and orignator already exists.');
        }

        return $this->sdoFactory->create($controlAuthority, 'medona/controlAuthority');
    }

    /**
     * Edit a relation between a control authority and an originator
     * @param string $originatorOrgUnitId Originator identifier
     *
     * @return object The relation object
     */
    public function read($originatorOrgUnitId)
    {
        return $this->sdoFactory->read('medona/controlAuthority', $originatorOrgUnitId);
    }

    /**
     * Update a relation between a control authority and an originator
     * @param medona/controlAuthority $controlAuthority    The control authority object to update
     * @param string                  $originatorOrgUnitId The last originatorOrgUnitId
     *
     * @return bool The resut of the operation
     */
    public function update($controlAuthority, $originatorOrgUnitId)
    {
        if(($controlAuthority->originatorOrgUnitId=='*')||empty($this->sdoFactory->find('medona/controlAuthority',"originatorOrgUnitId='$controlAuthority->originatorOrgUnitId'" ))) {
            $this->delete($originatorOrgUnitId);
            $this->create($controlAuthority);
            
        } else {
            throw \laabs::Bundle('medona')->newException('controlAuthorityException', 'The relation control authority and orignator already exists.');
        }

        return true;
    }

    /**
     * Delete a relation between a control authority and an originator
     * @param id $originatorOrgUnitId Originator identifier
     *
     * @return bool The resut of the operation
     */
    public function delete($originatorOrgUnitId)
    {
        $controlAuthority = $this->sdoFactory->read('medona/controlAuthority', $originatorOrgUnitId);

        if (!$controlAuthority) {
            return false;
        }
        try {
            $this->sdoFactory->delete($controlAuthority);
        } catch (\Exception $e) {
            throw new \bundle\recordsManagement\Exception\retentionRuleException("Relation not deleted.");
        }

        return true;
    }
}
