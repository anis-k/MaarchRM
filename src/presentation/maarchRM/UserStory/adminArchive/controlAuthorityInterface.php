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
 * User story admin relation control authority / originator
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface controlAuthorityInterface
{
    /**
     * Get the relation control authority / originator
     *
     * @return medona/controlAuthority/index
     * @uses medona/controlAuthority/readList
     */
    public function readControlauthority();

    /**
     * Add a relation control authority / originator
     * @param medona/controlAuthority $controlAuthority The relation control authority / originator to create
     *
     * @return medona/controlAuthority/create
     * @uses medona/controlAuthority/create
     */
    public function createControlauthority($controlAuthority);

    /**
     * Update a relation control authority / originator
     * @param string                  $originatorOrgUnitId Originator identifier
     * @param medona/controlAuthority $controlAuthority   The relation control authority / originator to update
     *
     * @return medona/controlAuthority/update
     * @uses medona/controlAuthority/update
     */
    public function updateControlauthority($originatorOrgUnitId,$controlAuthority);
    
    /**
     * Delete a relation control authority / originator
     * @param string                  $originatorOrgUnitId Originator identifier
     * 
     * @return medona/controlAuthority/delete
     * @uses medona/controlAuthority/delete
     */
    public function deleteControlauthority($originatorOrgUnitId);
}
