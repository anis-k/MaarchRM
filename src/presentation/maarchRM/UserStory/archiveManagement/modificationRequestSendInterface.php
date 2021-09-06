<?php

/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of Maarch RM.
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
 * along with Maarch RM.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\archiveManagement;
/**
 * Interface for archive modification
 */
interface modificationRequestSendInterface
{
    /**
     * Get modification requests
     * 
     * @uses medona/archiveModificationRequest/read
     * @return medona/message/modificationRequestList
     */
    public function readModificationrequest();

    /**
     * Get the deliveries messages
     *
     * @uses medona/archiveModificationRequest/readHistory
     * @return medona/message/modificationRequestHistory
     */
    public function readModificationrequestHistory();

    /**
     * Create request
     * @param array  $archiveIds            List of archives
     * @param string $comment               A comment
     * @param string $identifier            An identifier
     * @param string $format                The message format
     *
     * @uses medona/archiveModificationRequest/create
     * @return medona/archiveModification/modificationRequestSent
     */
    public function createModificationrequest($archiveIds, $comment, $identifier = null, $format = null);
}
