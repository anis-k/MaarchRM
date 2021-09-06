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

namespace bundle\medona\Controller;

/**
 * Archives modification
 */
class ArchiveConversion
{
    protected $sdoFactory;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Flag for converison
     * @param array $documentIds Array of document identifier
     *
     * @return bool
     */
    public function conversion($documentIds)
    {
        $archiveConversionRequestController = \laabs::newController("medona/ArchiveConversionRequest");
        $organizationController = \laabs::newController('organization/organization');

        $archiveIds = array();

        $documentsByOriginator = array();
        foreach ($documentIds as $documentId) {
            $archiveDocumentDigitalResource = $this->sdoFactory->find('recordsManagement/archiveDocumentDigitalResource', "docId='".(string) $documentId."'")[0];
            $archiveIds[] = $archiveDocumentDigitalResource->archiveId;

            if (!isset($documentsByOriginator[$archiveDocumentDigitalResource->originatorOrgRegNumber])) {
                $documentsByOriginator[$archiveDocumentDigitalResource->originatorOrgRegNumber] = array();
            }

            $documentsByOriginator[$archiveDocumentDigitalResource->originatorOrgRegNumber][] = $archiveDocumentDigitalResource;
        }

        $senderOrg = \laabs::getToken('ORGANIZATION');
        if (!$senderOrg) {
            throw \laabs::newException('medona/invalidMessageException', "No current organization choosen");
        }

        foreach ($documentsByOriginator as $originatorOrgRegNumber => $documents) {
            $recipientOrg = $organizationController->getOrgByRegNumber($originatorOrgRegNumber);

            $archiveConversionRequestController->send((string) \laabs::newId(), $senderOrg, $recipientOrg, $documents);
        }

        return $documentIds;
    }
}
