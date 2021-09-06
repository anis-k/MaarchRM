<?php
/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of bundle digitalSafe.
 *
 * Bundle digitalSafe is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle digitalSafe is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalSafe.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\digitalSafe\Controller;

class digitalSafe
{
    protected $sdoFactory;
    protected $archiveController;
    protected $lifeCycleJournalController;
    protected $organizationController;
    protected $userPositionController;
    protected $servicePositionController;
    protected $accountController;
    protected $account;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;

        $this->archiveController = \laabs::newController("recordsManagement/archive");
        $this->lifeCycleJournalController = \laabs::newController("lifeCycle/journal");
        $this->organizationController = \laabs::newController('organization/organization');
        $this->userPositionController = \laabs::newController('organization/userPosition');
        $this->servicePositionController = \laabs::newController('organization/servicePosition');
        $this->accountController = \laabs::newController('auth/userAccount');
        $this->digitalResourceController = \laabs::newController('digitalResource/digitalResource');

        $accountToken = \laabs::getToken('AUTH');
        if (!$accountToken) {
            throw $this->getThrowable("Missing authentication credential", 401);
        }

        $this->account = $this->accountController->get($accountToken->accountId);
    }

    /**
     * Depose numerical object
     *
     * @param  string $originatorOwnerOrgRegNumber ID_CCFN
     * @param  string $originatorOrgRegNumber      ID_CONT
     * @param  array  $digitalResources            ON
     * @param  array  $descriptionObject           Metadata
     * @param  string $originatorArchiveId         ID_ON_UTI
     *
     * @return object $replyMessage                Numerical object metadata
     */
    public function receive(
        $originatorOwnerOrgRegNumber,
        $originatorOrgRegNumber,
        $digitalResources,
        $descriptionObject = null,
        $originatorArchiveId = null
    ) {
        $archive = \laabs::newInstance('recordsManagement/archive');
        $archive->digitalResources = $digitalResources;
        $archive->originatorOrgRegNumber = $originatorOrgRegNumber;
        $archive->originatorOwnerOrgRegNumber = $originatorOwnerOrgRegNumber;
        $archive->descriptionObject = $descriptionObject;
        $archive->originatorArchiveId = $originatorArchiveId;

        $replyMessage = new \stdClass();
        $replyMessage->timestamp = \laabs::newTimestamp();

        $accountToken = \laabs::getToken('AUTH');
        $account = $this->accountController->get($accountToken->accountId);
        $replyMessage->accountName = $account->accountName;

        foreach ($archive->digitalResources as $resource) {
            if ((isset($resource->hash) && !is_null($resource->hash))
                && (isset($resource->hashAlgorithm)
                    && !is_null($resource->hashAlgorithm))
            ) {
                try {
                    $this->checkHash($resource->handler, $resource->hash, $resource->hashAlgorithm);
                } catch (\Exception $e) {
                    throw $this->getThrowable($e->getMessage(), 400, $replyMessage);
                }
            } elseif (!isset($resource->hash) && !isset($resource->hashAlgorithm)) {
                continue;
            } else {
                throw $this->getThrowable("Hash or hash algorithm missing", 401, $replyMessage);
            }
        }

        try {
            $archiveId = $this->archiveController->receive($archive, false);
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
        } catch (\Exception $e) {
            throw $this->getThrowable($e->getMessage(), 400, $replyMessage);
        }

        $replyMessage->archiveId = $archiveId;
        if (!is_null($archive->originatorArchiveId)) {
            $replyMessage->originatorArchiveId = $archive->originatorArchiveId;
        }
        $replyMessage->originatorOwnerOrgRegNumber = $archive->originatorOwnerOrgRegNumber;
        $replyMessage->originatorOrgRegNumber = $archive->originatorOrgRegNumber;

        $resources = $this->digitalResourceController->getResourcesByArchiveId($archiveId);

        $replyMessage->digitalResources = [];
        foreach ($resources as $resource) {
            $replyMessage->digitalResources[] = $resource;
        }

        $replyMessage->operationResult = true;

        return $replyMessage;
    }

    /**
     * Destruct a numerical object
     *
     * @param  string $originatorOwnerOrgRegNumber ID_CCFN
     * @param  string $originatorOrgRegNumber      ID_CONT
     * @param  string $archiveId                   IDU
     *
     * @return object $replyMesage                 Numerical object metadata
     */
    public function destruct(
        $originatorOwnerOrgRegNumber,
        $originatorOrgRegNumber,
        $archiveId
    ) {
        $replyMessage = new \stdClass();
        $replyMessage->timestamp = \laabs::newTimestamp();
        $replyMessage->accountName = $this->account->accountName;

        if (!$this->checkRight($originatorOwnerOrgRegNumber, $originatorOrgRegNumber, $archiveId)) {
            throw $this->getThrowable("Permission denied", 401, $replyMessage);
        }

        try {
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
            $this->archiveController->dispose([$archiveId]);
            $res = $this->archiveController->eliminate($archiveId);
            if (count($res['error']) == 0) {
                $res = $this->archiveController->destruct($archiveId);
            }
        } catch (\Exception $e) {
            throw $this->getThrowable($e->getMessage(), 400, $replyMessage);
        }

        if (count($res['error']) == 1) {
            throw $this->getThrowable("The request could not be processed", 409, $replyMessage);
        }

        $replyMessage->originatorOwnerOrgRegNumber = $archive->originatorOwnerOrgRegNumber;
        $replyMessage->originatorOrgRegNumber = $archive->originatorOrgRegNumber;
        $replyMessage->archiveId = $archive->archiveId;
        $replyMessage->originatorArchiveId = $archive->originatorArchiveId;
        $replyMessage->operationResult = true;

        return $replyMessage;
    }

    /**
     * Read a numerical object resource by its id
     *
     * @param  string $originatorOwnerOrgRegNumber ID_CCFN
     * @param  string $originatorOrgRegNumber      ID_CONT
     * @param  string $archiveId                   IDU
     *
     * @return $replyMesage                        Numerical object metadata
     */
    public function consultation(
        $originatorOwnerOrgRegNumber,
        $originatorOrgRegNumber,
        $archiveId
    ) {
        $replyMessage = new \stdClass();
        $replyMessage->timestamp = \laabs::newTimestamp();
        $replyMessage->accountName = $this->account->accountName;

        if (!$this->checkRight($originatorOwnerOrgRegNumber, $originatorOrgRegNumber, $archiveId)) {
            throw $this->getThrowable("Permission denied", 401, $replyMessage);
        }

        try {
            $resIds = $this->archiveController->getDigitalResources($archiveId);
            if ($resIds) {
                $replyMessage->digitalResources = [];
                foreach ($resIds as $resId) {
                    $resource = $this->archiveController->consultation($archiveId, $resId);
                    $replyMessage->digitalResources[] = $resource;
                }
            }
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
        } catch (\Exception $e) {
            throw $this->getThrowable($e->getMessage(), 400, $replyMessage);
        }

        $replyMessage->originatorOwnerOrgRegNumber = $archive->originatorOwnerOrgRegNumber;
        $replyMessage->originatorOrgRegNumber = $archive->originatorOrgRegNumber;
        $replyMessage->archiveId = $archive->archiveId;
        $replyMessage->originatorArchiveId = $archive->originatorArchiveId;

        $replyMessage->operationResult = true;

        return $replyMessage;
    }

    /**
     * Read numerical object technical metadata
     *
     * @param  string $originatorOwnerOrgRegNumber ID_CCFN
     * @param  string $originatorOrgRegNumber      ID_CONT
     * @param  string $archiveId                   IDU
     *
     * @return $replyMesage                        Numerical object metadata
     */
    public function retrieve(
        $originatorOwnerOrgRegNumber,
        $originatorOrgRegNumber,
        $archiveId
    ) {
        $replyMessage = new \stdClass();
        $replyMessage->timestamp = \laabs::newTimestamp();
        $replyMessage->accountName = $this->account->accountName;

        if (!$this->checkRight($originatorOwnerOrgRegNumber, $originatorOrgRegNumber, $archiveId)) {
            throw $this->getThrowable("Permission denied", 401, $replyMessage);
        }

        try {
            $archive = $this->archiveController->retrieve($archiveId);
        } catch (\Exception $e) {
            throw $this->getThrowable($e->getMessage(), 404, $replyMessage);
        }

        $replyMessage->originatorOwnerOrgRegNumber = $archive->originatorOwnerOrgRegNumber;
        $replyMessage->originatorOrgRegNumber = $archive->originatorOrgRegNumber;
        $replyMessage->archiveId = $archive->archiveId;
        $replyMessage->originatorArchiveId = $archive->originatorArchiveId;
        $replyMessage->depositDate = $archive->depositDate;
        $replyMessage->descriptionObject = $archive->descriptionObject;

        foreach ($archive->digitalResources as $digitalResource) {
            unset($digitalResource->address);
        }

        $replyMessage->digitalResources = $archive->digitalResources;

        $replyMessage->operationResult = true;

        return $replyMessage;
    }

    /**
     * Verify numerical object integrity
     *
     * @param  string $originatorOwnerOrgRegNumber ID_CCFN
     * @param  string $originatorOrgRegNumber      ID_CONT
     * @param  string $archiveId                   IDU
     * @return $replyMesage                        Numerical object metadata
     */
    public function verifyIntegrity(
        $originatorOwnerOrgRegNumber,
        $originatorOrgRegNumber,
        $archiveId
    ) {
        $replyMessage = new \stdClass();
        $replyMessage->timestamp = \laabs::newTimestamp();
        $replyMessage->accountName = $this->account->accountName;

        if (!$this->checkRight($originatorOwnerOrgRegNumber, $originatorOrgRegNumber, $archiveId)) {
            throw $this->getThrowable("Permission denied", 401, $replyMessage);
        }

        try {
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
            $res = $this->archiveController->verifyIntegrity($archiveId);
        } catch (\Exception $e) {
            throw $this->getThrowable($e->getMessage(), 400, $replyMessage);
        }

        $replyMessage->archiveId = $archive->archiveId;
        $replyMessage->originatorOwnerOrgRegNumber = $archive->originatorOwnerOrgRegNumber;
        $replyMessage->originatorOrgRegNumber = $archive->originatorOrgRegNumber;
        $replyMessage->depositDate = $archive->depositDate;
        $replyMessage->originatorArchiveId = $archive->originatorArchiveId;

        if (count($res['error']) > 1) {
            $replyMessage->operationResult = false;
            $replyMessage->operationMessage = "No integrity";
            return $replyMessage;
        }
        $replyMessage->operationResult = true;

        return $replyMessage;
    }

    /**
     * Read all events on numerical objects according to filters
     *
     * @param  string $originatorOwnerOrgRegNumber ID_CCFN
     * @param  string $originatorOrgRegNumber      ID_CONT
     * @param  string $fromDate                    starting date
     * @param  string $toDate                      ending date
     * @param  string $originatorArchiveId         orgRegNumber
     * @param  string $archiveId                   IDU
     *
     * @return object $replyMesage                 Numerical object metadata
     */
    public function journal(
        $originatorOwnerOrgRegNumber,
        $originatorOrgRegNumber = null,
        $fromDate = null,
        $toDate = null,
        $originatorArchiveId = null,
        $archiveId = null
    ) {
        $replyMessage = new \stdClass();
        $replyMessage->originatorOwnerOrgRegNumber = $originatorOwnerOrgRegNumber;
        $replyMessage->timestamp = \laabs::newTimestamp();
        $replyMessage->accountName = $this->account->accountName;

        try {
            $query = array();
            $queryParams = array();

            $queryParams['objectClass'] = 'recordsManagement/archive';
            $query['objectClass'] = "objectClass = :objectClass";

            $queryParams['originatorOwnerOrgRegNumber'] = $originatorOwnerOrgRegNumber;
            $query['originatorOwnerOrgRegNumber'] = "orgRegNumber = :originatorOwnerOrgRegNumber";

            if ($archiveId) {
                $queryParams['objectId'] = $archiveId;
                $query['objectId'] = "objectId = :objectId";
            }

            $queryParams['accountId'] = $this->account->accountId;
            $query['accountId'] = "accountId = :accountId";

            if ($originatorOrgRegNumber) {
                $queryParams['originatorOrgRegNumber'] = $originatorOrgRegNumber;
                $query['originatorOrgRegNumber'] = "orgUnitRegNumber = :originatorOrgRegNumber";
            }
            if ($originatorArchiveId) {
                $queryParams['originatorArchiveId'] = $originatorArchiveId;
                $query['originatorArchiveId'] = "originatorArchiveId = :originatorArchiveId";
            }

            if ($fromDate) {
                $queryParams['minDate'] = $fromDate;
                $query['minDate'] = "timestamp >= :minDate";
            }

            if ($toDate) {
                $queryParams['maxDate'] = $toDate->add(new \DateInterval('PT23H59M59S'));
                $query['maxDate'] = "timestamp <= :maxDate";
            }

            $queryString = implode(' AND ', $query);

            $events = $this->sdoFactory->find(
                'lifeCycle/event',
                $queryString,
                $queryParams
            );

            $replyMessage->lifeCycleEvents = [];
            foreach ($events as $i => $event) {
                $event->accountName = $this->account->accountName;
                $event->eventInfo = $this->lifeCycleJournalController->getObjectEvents($event->objectId, $event->objectClass);
                $replyMessage->lifeCycleEvents[] = $event;
            }
        } catch (\Exception $e) {
            throw $this->getThrowable($e->getMessage(), 400, $replyMessage);
        }

        if ($originatorOrgRegNumber) {
            $this->logEvent($originatorOrgRegNumber, 'organization/journal');
        } else {
            $this->logEvent($originatorOwnerOrgRegNumber, 'organization/journal');
        }

        $replyMessage->operationResult = true;

        return $replyMessage;
    }

    /**
     * List numerical objects
     *
     * @param  string  $originatorOwnerOrgRegNumber ID_CCFN
     * @param  string  $originatorOrgRegNumber      ID_CONT
     * @param  string  $fromDate                    starting date
     * @param  string  $toDate                      ending date
     * @param  string  $originatorArchiveId
     * @param  string  $archiveId                   IDU
     * @param  boolean $hasLog                      log events in lifecycle (Y/N)
     *
     * @return object $replyMesage                 Numerical object metadata
     */
    public function listing(
        $originatorOwnerOrgRegNumber,
        $originatorOrgRegNumber,
        $fromDate = null,
        $toDate = null,
        $originatorArchiveId = null,
        $archiveId = null,
        $hasLog = true
    ) {
        $queryParts = array();
        $queryParams = array();

        $replyMessage = new \stdClass();
        $replyMessage->originatorOwnerOrgRegNumber = $originatorOwnerOrgRegNumber;
        $replyMessage->originatorOrgRegNumber = $originatorOrgRegNumber;
        $replyMessage->timestamp = \laabs::newTimestamp();
        $replyMessage->accountName = $this->account->accountName;

        try {
            $organization = $this->organizationController->getOrgByRegNumber($originatorOwnerOrgRegNumber);
        } catch (\Exception $exception) {
            throw $this->getThrowable("Organization " . $originatorOwnerOrgRegNumber . " doesn't exist", 404, $replyMessage);
        }

        if ($organization->isOrgUnit) {
            throw $this->getThrowable("The organization must not be a organization unit", 403, $replyMessage);
        }

        $userPositions = $this->userPositionController->listPositions($this->account->accountId);
        $userPositions[] = $this->servicePositionController->getPosition($this->account->accountId);

        $organizations = $this->organizationController->readDescendantServices($organization->orgId);

        $userOrganisations = [];
        foreach ($organizations as $organization) {
            if (is_null($originatorOrgRegNumber)
                || $originatorOrgRegNumber == $organization->registrationNumber) {
                foreach ($userPositions as $userPosition) {
                    if ($userPosition->orgId == $organization->orgId) {
                        $userOrganisations[] = $organization;
                    }
                }
            }
        }

        if (empty($userOrganisations)) {
            throw $this->getThrowable("The user is not positioned on the organization.", 403, $replyMessage);
        }

        $queryParts['organization']  = "(";
        foreach ($userOrganisations as $userOrganisation) {
            if ($userOrganisation === reset($userOrganisations)) {
                $queryParts['organization'] .= "originatorOrgRegNumber = '". $userOrganisation->registrationNumber ."'";
            }

            $queryParts['organization'] .= " OR originatorOrgRegNumber = '". $userOrganisation->registrationNumber ."'";
        }
        $queryParts['organization']  .= ")";

        if ($fromDate || $toDate) {
            try {
                $fromDate = $fromDate ? \laabs::newDatetime($fromDate, "UTC") : false;
                $toDate = $toDate ? \laabs::newDatetime($toDate, "UTC") : false;
            } catch (\Exception $exception) {
                throw $this->getThrowable("Invalid format date", 400, $replyMessage);
            }

            if ($fromDate && $toDate) {
                $queryParams['fromDate'] = $fromDate->format('Y-m-d').'T00:00:00';
                $queryParams['toDate'] = $toDate->format('Y-m-d').'T23:59:59';
                $queryParts['depositDate'] = "depositDate >= :fromDate AND depositDate <= :toDate";
            } elseif ($fromDate) {
                $queryParams['fromDate'] = $fromDate->format('Y-m-d').'T00:00:00';
                $queryParts['depositDate'] = "depositDate >= :fromDate";
            } elseif ($toDate) {
                $queryParams['toDate'] = $toDate->format('Y-m-d').'T23:59:59';
                $queryParts['depositDate'] = "depositDate <= :toDate";
            }
        }

        if ($archiveId) {
            $queryParams['archiveId'] = $archiveId;
            $queryParts['archiveId'] = "archiveId = :archiveId";
        }

        if ($originatorArchiveId) {
            $queryParams['originatorArchiveId'] = $originatorArchiveId;
            $queryParts['originatorArchiveId'] = "originatorArchiveId = :originatorArchiveId";
        }
        $queryParts['archive'] = "status != 'disposed'";

        $queryString = \laabs\implode(' AND ', $queryParts);

        $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        $archives = $this->sdoFactory->find(
            'recordsManagement/archive',
            $queryString,
            $queryParams,
            false,
            false,
            $maxResults
        );

        $replyMessage->archiveIds = [];
        foreach ($archives as $archive) {
            $replyMessage->archiveIds[] = $archive->archiveId;
        }

        if ($hasLog) {
            $this->logEvent($originatorOrgRegNumber, 'organization/listing');
        }

        $replyMessage->operationResult = true;

        return $replyMessage;
    }

    /**
     * Counting numerical objects according to filters
     *
     * @param  string $originatorOwnerOrgRegNumber ID_CCFN
     * @param  string $originatorOrgRegNumber      ID_CONT
     * @param  string $fromDate                    starting date
     * @param  string $toDate                      ending date
     * @param  string $originatorArchiveId
     *
     * @return object $replyMesage                 Numerical object metadata
     */
    public function counting(
        $originatorOwnerOrgRegNumber,
        $originatorOrgRegNumber,
        $fromDate = null,
        $toDate = null,
        $originatorArchiveId = null
    ) {
        $replyMessage = $this->listing(
            $originatorOwnerOrgRegNumber,
            $originatorOrgRegNumber,
            $fromDate,
            $toDate,
            $originatorArchiveId,
            null,
            false
        );

        if ($replyMessage->operationResult == true) {
            $replyMessage->count = count($replyMessage->archiveIds);

            $this->logEvent($originatorOrgRegNumber, 'organization/counting');
        }

        return $replyMessage;
    }

    /**
     * Check whether if user is allowed to access functionnality
     *
     * @param  string  $originatorOwnerOrgRegNumber ID_CCFN
     * @param  string  $originatorOrgRegNumber      ID_CONT
     * @param  string  $archiveId                   IDU
     *
     * @return boolean
     */
    protected function checkRight(
        $originatorOwnerOrgRegNumber,
        $originatorOrgRegNumber,
        $archiveId
    ) {
        try {
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
        } catch (\Exception $e) {
            $replyMessage = new \stdClass();
            throw $this->getThrowable("archive " . $archiveId . " doesn't exist", 404, $replyMessage);
        }

        if ($archive->originatorOwnerOrgRegNumber == $originatorOwnerOrgRegNumber
            && $archive->originatorOrgRegNumber == $originatorOrgRegNumber
        ) {
            return true;
        }

        return false;
    }

    /**
     * Calculate hash and verify if its correct
     *
     * @param  string $contents      base64 encoded file
     * @param  string $hash          hash file
     * @param  string $hashAlgorithm hash algortithm used to crypt file
     */
    protected function checkHash($contents, $hash, $hashAlgorithm)
    {
        $hash_calculated = strtolower(hash($hashAlgorithm, base64_decode($contents)));

        if ($hash_calculated !== strtolower($hash)) {
            throw \laabs::newException("digitalResource/invalidHashException", "Invalid hash.");
        }
    }

    /**
     * log event in lifecycle journal
     *
     * @param  string $regNumber organization registration number
     * @param  string $eventType type of event
     */
    protected function logEvent($regNumber, $eventType)
    {
        $organization = $this->organizationController->getOrgByRegNumber($regNumber);

        $this->lifeCycleJournalController->logEvent(
            $eventType,
            'organization/organization',
            $organization->orgId,
            $organization,
            true
        );
    }

    /**
     * Prepare a throwable
     * @param string The message
     * @param int    The code 
     * @param mixed  The contextual data
     * 
     * @return \Exception
     */
    protected function getThrowable($message, $code, $context = [])
    {
        $exception = \laabs::newException('digitalSafe/Exception', $message, $code);

        foreach ($context as $name => $value) {
            $exception->{$name} = $value;
        }
        $exception->operationResult = false;
        
        return $exception;
    }
}
