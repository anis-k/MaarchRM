<?php
/*
 * Copyright (C) 2015 Maarch
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
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\definitiveTransfer;

/**
 * User story for transfer sending
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface outgoingTransferSendInterface
{
    /**
     * Create transfer sending
     * @param array $archiveIds             List of archives
     * @param string $archiverOrgRegNumber  An Archiver
     * @param string $comment               A comment
     * @param string $identifier            An identifier
     * @param string $format                The message format
     *
     * @uses medona/archiveTransfer/updateOutgoingtransferSending
     * @return medona/archiveModification/transferSending
     */
    public function updateOutgoingtransferSending($archiveIds, $archiverOrgRegNumber, $comment, $identifier = null, $format = null);
}