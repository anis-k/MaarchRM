<?php
/*
 * Copyright (C) 2016 Maarch
 *
 * This file is part of medona.
 *
 * medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona. If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\UserStory\restitution;

/**
 * User story - restitution request
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface restitutionRequestInterface
{
    /**
     * Get restitution request form
     *
     * @return medona/message/restitutionRequest
     */
    public function readRestitutionRequest();

    /**
     * Request a restitution
     * @param array  $archiveIds Array of archive idenfiers
     * @param string $comment    The request comment
     * @param string $identifier The request identifier
     * @param string $format     The request format
     *
     * @uses medona/archiveRestitution/updateSetforrestitution
     * @return medona/archiveModification/setForRestitution
     */
    public function updateRestitutionRequest($archiveIds, $comment = null, $identifier = null, $format = null);
}
