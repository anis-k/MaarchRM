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
namespace bundle\medona;
/**
 *  @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface controlAuthorityInterface 
{
    /**
     * Get the relation control authority / originator
     *
     * @action medona/ControlAuthority/index
     */
    public function readList();

    /**
     * Add a relation control authority / originator
     * @param medona/controlAuthority $controlAuthority the relation control authority / originator
     *
     * @action medona/ControlAuthority/create
     */
    public function create($controlAuthority);

    /**
     * Update a relation control authority / originator
     * @param string                  $originatorOrgUnitId Originator identifier
     * @param medona/controlAuthority $controlAuthority   the relation control authority / originator
     *
     * @action medona/ControlAuthority/update
     */
    public function update($originatorOrgUnitId,$controlAuthority);
    
    /**
     * Delete a relation control authority / originator
     * @param string $originatorOrgUnitId Originator identifier
     *
     * @action medona/ControlAuthority/delete
     */
    public function delete($originatorOrgUnitId);
}
