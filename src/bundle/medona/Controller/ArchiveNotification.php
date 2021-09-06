<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle medona
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
 * Class for ArchiveNotification
 *
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class ArchiveNotification extends abstractMessage
{
     /**
     * Get received notification messages
     * @param string $reference
     * @param string $archiver
     * @param string $originator
     * @param string $depositor
     * @param string $archivalAgreement
     * @param date   $fromDate
     * @param date   $toDate
     *
     * @return array Array of medona/message object
     */
    public function listReception(
        $reference = null,
        $archiver = null,
        $originator = null,
        $depositor = null,
        $archivalAgreement = null,
        $fromDate = null,
        $toDate = null
    ) {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts = [];
        $queryParts[] = "recipientOrgRegNumber=$registrationNumber OR senderOrgRegNumber=$registrationNumber";
        $queryParts[] = "type='ArchiveModificationNotification' 
        OR type='ArchiveDestructionNotification' 
        OR type='ArchivalProfileModificationNotification'";
        $queryParts[] = "active=true";

        $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        return $this->sdoFactory->find(
            'medona/message',
            '('.implode(') and (', $queryParts).')',
            null,
            false,
            false,
            $maxResults
        );
    }

    /**
     * Get transfer history
     *
     * @param string $reference         Reference
     * @param string $archiver          Archiver
     * @param string $originator        Originator
     * @param string $depositor         Depositor
     * @param string $archivalAgreement Archival agreement
     * @param date   $fromDate          From date
     * @param date   $toDate            To date
     * @param string $status            Status
     *
     * @return array Array of medona/message object
     */
    public function history(
        $reference = null,
        $archiver = null,
        $originator = null,
        $depositor = null,
        $archivalAgreement = null,
        $fromDate = null,
        $toDate = null,
        $status = null
    ) {
        return $this->search(
            "ArchiveNotification",
            $reference,
            $archiver,
            $originator,
            $depositor,
            $archivalAgreement,
            $fromDate,
            $toDate,
            $status,
            false
        );
    }

    /**
     * Count notification message
     *
     * @return array Number of notification messages
     */
    public function count()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $res = array();
        $queryParts = array();
        $queryParts[] = "(type='ArchiveModificationNotification' 
        OR type='ArchiveDestructionNotification' 
        OR type='ArchivalProfileModificationNotification')";
        $queryParts[] = "recipientOrgRegNumber=$registrationNumber OR senderOrgRegNumber=$registrationNumber";
        $queryParts[] = "active=true";
        $res['received'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        return $res;
    }
}
