<?php
/*
 * Copyright (C) 2018 Maarch
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
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\archiveManagement;
/**
 * Interface for management of archival profile
 * 
 * @package RecordsMangement
 * @author  Maarch Cyril Vazquez <cyril.vazquez@maarch.org>
 */ 
interface addResourceInterface
{
    /**
     * Add a resource to the archive
     * @param string $contents
     * @param string $filename
     *
     * @uses recordsManagement/archive/create_archiveId_Digitalresource
     */
    public function create_archiveId_Digitalresource($contents, $filename = null);

    /**
     * Resource destruction
     * @param array $resIds Id List of resource
     *
     * @uses recordsManagement/archive/delete_archiveId_Digitalresource
     * @return recordsManagement/archive/deleteResource
     */
    public function delete_archiveId_Digitalresource($resIds);
}
