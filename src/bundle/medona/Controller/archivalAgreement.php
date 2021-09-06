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
 * Class of adminArchivalProfile
 *
 * @author Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class archivalAgreement
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
     * List archival agreements
     *
     * @return medona/archivalAgreement[] The list of archival agreements
     */
    public function index()
    {
        return $this->sdoFactory->find('medona/archivalAgreement');
    }

    /**
     * New empty archival agreement with default values
     *
     * @return medona/archivalAgreement The archival profile object
     */
    public function newAgreement()
    {
        return \laabs::newInstance("medona/archivalAgreement");
    }

    /**
     * Edit an archival agreement
     * @param string $archivalAgreementId The archival agreement's identifier
     *
     * @return medona/archivalAgreement The profile object
     */
    public function edit($archivalAgreementId)
    {
        if ($archivalAgreementId) {
            return $this->sdoFactory->read('medona/archivalAgreement', $archivalAgreementId);
        }

        return $this->newAgreement();
    }

    /**
     * create an archival profile
     * @param medona/archivalAgreement $archivalAgreement The archival agreement object
     *
     * @return boolean THe result of the request
     */
    public function create($archivalAgreement)
    {
        $archivalAgreement = \laabs::cast($archivalAgreement, "medona/archivalAgreement");
        $archivalAgreement->archivalAgreementId = \laabs::newId();

        $this->sdoFactory->create($archivalAgreement);

        return $archivalAgreement->archivalAgreementId;
    }

    /**
     * update an archival agreement
     * @param medona/archivalAgreement $archivalAgreement The archival agreement object
     *
     * @return boolean The request of the request
     */
    public function update($archivalAgreement)
    {
        $archivalAgreement = \laabs::cast($archivalAgreement, "medona/archivalAgreement");

        return $this->sdoFactory->update($archivalAgreement, "medona/archivalAgreement");
    }

    /**
     * delete an archival agreement
     * @param string $archivalAgreementId The identifier of the archival agreement
     *
     * @return boolean The request of the request
     */
    public function delete($archivalAgreementId)
    {
        $archivalAgreement = $this->sdoFactory->read('medona/archivalAgreement', $archivalAgreementId);

        return $this->sdoFactory->delete($archivalAgreement, 'medona/archivalAgreement');
    }

    /**
     * Retrieve the agreement object by its reference
     * @param string $archivalAgreementRef The agreement name
     *
     * @return medona/archivalAgreement The agreement
     */
    public function getByReference($archivalAgreementRef)
    {
        $archivalAgreement = $this->sdoFactory->read("medona/archivalAgreement", array("reference" => $archivalAgreementRef));
        if (!$archivalAgreement) {
            throw \laabs::newException('medona/unknownArchivalAgreementException', "Archival agreement '$archivalAgreementRef' not found");
        }

        $orgController =  \laabs::newController("organization/organization");
        $archivalAgreement->archiverOrg = $orgController->getOrgByRegNumber($archivalAgreement->archiverOrgRegNumber);
        $archivalAgreement->depositorOrg = $orgController->getOrgByRegNumber($archivalAgreement->depositorOrgRegNumber);

        return $archivalAgreement;
    }

    /**
     * Retrieve the agreement object by archival profile reference
     * @param string $archivalProfileReference The profile reference
     *
     * @return medona/archivalAgreement The agreement
     */
    public function getByProfileReference($archivalProfileReference)
    {
        $archivalAgreements = $this->sdoFactory->find("medona/archivalAgreement", "archivalProfileReference = '$archivalProfileReference'");
        if (!$archivalAgreements) {
            return null;
        }

        $orgController =  \laabs::newController("organization/organization");

        foreach ($archivalAgreements as $archivalAgreement) {
            $archivalAgreement->archiverOrg = $orgController->getOrgByRegNumber($archivalAgreement->archiverOrgRegNumber);
            $archivalAgreement->depositorOrg = $orgController->getOrgByRegNumber($archivalAgreement->depositorOrgRegNumber);
        }

        return $archivalAgreements;
    }

    /**
     * Retrieve the agreement object by serviceL level
     * @param string $serviceLevelReference The service level reference
     *
     * @return medona/archivalAgreement The agreement
     */
    public function getByServiceLevelReference($serviceLevelReference)
    {
        $archivalAgreements = $this->sdoFactory->find("medona/archivalAgreement", "serviceLevelReference = '$serviceLevelReference'");
        if (!$archivalAgreements) {
            return null;
        }

        $orgController =  \laabs::newController("organization/organization");

        foreach ($archivalAgreements as $archivalAgreement) {
            $archivalAgreement->archiverOrg = $orgController->getOrgByRegNumber($archivalAgreement->archiverOrgRegNumber);
            $archivalAgreement->depositorOrg = $orgController->getOrgByRegNumber($archivalAgreement->depositorOrgRegNumber);
        }

        return $archivalAgreements;
    }

    /**
     * Count the archival aggreements for an organization
     * @param string $orgRegNumber The organization registration number
     *
     * @return int The number of archival aggreements with this organization
     */
    public function countByOrg($orgRegNumber) {
        $queryString = [];
        $queryString[] = "archiverOrgRegNumber='$orgRegNumber'";
        $queryString[] = "depositorOrgRegNumber='$orgRegNumber'";

        $count = $this->sdoFactory->count("medona/archivalAgreement", \laabs\implode(" OR ", $queryString));

        return $count;
    }
}
