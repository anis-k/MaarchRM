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
namespace bundle\recordsManagement\Controller;

/**
 * Class of adminArchivalProfile
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class archivalProfile
{
    protected $sdoFactory;

    protected $lifeCycleJournalController;

    protected $descriptionFields;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->lifeCycleJournalController = \laabs::newController('lifeCycle/journal');

        foreach (\Laabs::newController('recordsManagement/descriptionField')->index() as $descriptionField) {
            if (!empty($descriptionField->enumeration)) {
                $descriptionField->enumeration = json_decode($descriptionField->enumeration);
            }
            $this->descriptionFields[$descriptionField->name] = $descriptionField;
        }
    }

    /**
     * List archival profiles
     *
     * @return recordsManagement/archivalProfile[] The list of archival profiles
     */
    public function index()
    {
        return $this->sdoFactory->find('recordsManagement/archivalProfile');
    }

    /**
     * New empty archival profile with default values
     *
     * @return recordsManagement/archivalProfile The archival profile object
     */
    public function newProfile()
    {
        $archivalProfile = \laabs::newInstance("recordsManagement/archivalProfile");

        return $archivalProfile;
    }

    /**
     * Edit an archival profile
     * @param string $archivalProfileId   The archival profile's identifier
     * @param bool   $withRelatedProfiles Bring back the contents profiles
     *
     * @return recordsManagement/archivalProfile The profile object
     */
    public function read($archivalProfileId, $withRelatedProfiles=false)
    {
        $archivalProfile = $this->sdoFactory->read('recordsManagement/archivalProfile', $archivalProfileId);

        $this->readDetail($archivalProfile);

        if ($withRelatedProfiles) {
            $archivalProfile->containedProfiles = $this->getContentsProfiles($archivalProfileId);
        }

        return $archivalProfile;
    }

    /**
     * get an archival profile by reference
     * @param string $archivalProfileReference The archival profile reference
     *
     * @return recordsManagement/archivalProfile The profile object
     */
    public function getByReference($archivalProfileReference)
    {
        $archivalProfile = $this->sdoFactory->read('recordsManagement/archivalProfile', array('reference' => $archivalProfileReference));

        $this->readDetail($archivalProfile);

        return $archivalProfile;
    }

    /**
     * get array of archival profile by description class
     * @param string $archivalProfileDescriptionClass The archival profile reference
     *
     * @return Array $archivalProfiles Array of recordsManagement/archivalProfile object
     */
    public function getByDescriptionClass($archivalProfileDescriptionClass)
    {
        $archivalProfiles = $this->sdoFactory->find('recordsManagement/archivalProfile', "descriptionClass='$archivalProfileDescriptionClass'");

        foreach ($archivalProfiles as $archivalProfile) {
            $this->readDetail($archivalProfile);
        }

        return $archivalProfiles;
    }

    /**
     * Read the agragates
     * @param recordsManagement/archivalProfile $archivalProfile
     */
    public function readDetail($archivalProfile)
    {
        // Read retention rule
        if ($archivalProfile->retentionRuleCode) {
            $archivalProfile->retentionRule = $this->sdoFactory->read('recordsManagement/retentionRule', $archivalProfile->retentionRuleCode);
        }

        // Read access rule
        if (!empty($archivalProfile->accessRuleCode)) {
            $archivalProfile->accessRule = $this->sdoFactory->read('recordsManagement/accessRule', $archivalProfile->accessRuleCode);
        }

        // Read profile description
        $archivalProfile->archiveDescription = $this->sdoFactory->readChildren('recordsManagement/archiveDescription', $archivalProfile, null, 'position');
        if ($archivalProfile->descriptionClass == '') {
            foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                $archiveDescription->descriptionField = $this->descriptionFields[$archiveDescription->fieldName];
            }
        } else {
            $reflectionClass = \laabs::getClass($archivalProfile->descriptionClass);

            foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                if ($reflectionClass->hasProperty($archiveDescription->fieldName)) {
                    $reflectionProperty = $reflectionClass->getProperty($archiveDescription->fieldName);

                    $descriptionField = \laabs::newInstance('recordsManagement/descriptionField');
                    $descriptionField->name = $reflectionProperty->name;
                    if (isset($reflectionProperty->tags['label'])) {
                        $descriptionField->label = $reflectionProperty->tags['label'][0];
                    } else {
                        $descriptionField->label = $reflectionProperty->name;
                    }

                    $descriptionField->type = $reflectionProperty->getType();

                    $descriptionField->enumeration = $reflectionProperty->getEnumeration();
                    if ($descriptionField->enumeration) {
                        $descriptionField->type = 'name';
                    }

                    $archiveDescription->descriptionField = $descriptionField;
                }
            }
        }
    }

    /**
     * Get the contents profiles list
     * string $archivalProfileId The parent profile identifier
     *
     * @return array The list of contents archival profile
     */
    public function getContentsProfiles($archivalProfileId)
    {
        $contentsProfiles = [];
        $relationships = $this->sdoFactory->find('recordsManagement/archivalProfileRelationship', "parentProfileId ='$archivalProfileId'");

        if (count($relationships)) {
            foreach ($relationships as $relationship) {
                $contentsProfiles[] = $this->sdoFactory->read('recordsManagement/archivalProfile', $relationship->containedProfileId);
            }
        }

        return $contentsProfiles;
    }

    /**
     * Get the standard archive field
     * @return array
     */
    public function getArchiveDescriptionFields()
    {
        $descriptionFields = [];
        $nameTypes = [
                'archiveId' => 'name',
                'originatorArchiveId' => 'name',
                'originatorOrgRegNumber' => 'name',
                
                'archiveName' => 'text',

                'depositDate' => 'date',
            ];
        // Read document profiles
        foreach ($nameTypes as $name => $type) {
            $descriptionField = \Laabs::newInstance('recordsManagement/descriptionField');
            $descriptionField->name = $descriptionField->label = $name;
            $descriptionField->type = $type;

            $descriptionFields[$name] = $descriptionField;
        }

        return $descriptionFields;
    }

    /**
     * Get the standard document fields
     * @return array
     */
    public function getDocumentDescriptionFields()
    {
        $descriptionFields = [];
        $nameTypes = [
                'description' => 'text',
                'language' => 'text',
                'purpose' => 'text',
                'title' => 'text',
                'creator' => 'text',
                'publisher' => 'text',
                'contributor' => 'text',
                'spatialCoverage' => 'text',
                'temporalCoverage' => 'text',

                'docId' => 'name',
                'originatorDocId' => 'name',
                'category' => 'name',

                'creation' => 'date',
                'issue' => 'date',
                'receipt' => 'date',
                'response' => 'date',
                'submission' => 'date',
                'available' => 'date',
                'valid' => 'date',
            ];
        // Read document profiles
        foreach ($nameTypes as $name => $type) {
            $descriptionField = \Laabs::newInstance('recordsManagement/descriptionField');
            $descriptionField->name = $descriptionField->label = $name;
            $descriptionField->type = $type;

            $descriptionFields[$name] = $descriptionField;
        }

        return $descriptionFields;
    }

    /**
     * create an archival profile
     * @param recordsManagement/archivalProfile $archivalProfile The archival profile object
     *
     * @return boolean The result of the request
     */
    public function create($archivalProfile)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $archivalProfile->archivalProfileId = \laabs::newId();

            $this->sdoFactory->create($archivalProfile, 'recordsManagement/archivalProfile');

            $this->createDetail($archivalProfile);
            // Contents profiles
            $this->updateContainedProfiles($archivalProfile->archivalProfileId, $archivalProfile->containedProfiles);

            // Life cycle journal
            $eventItems = array('archivalProfileReference' => $archivalProfile->reference);
            $this->lifeCycleJournalController->logEvent('recordsManagement/profileCreation', 'recordsManagement/archivalProfile', $archivalProfile->archivalProfileId, $eventItems);
        
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return  $archivalProfile->archivalProfileId;
    }

    /**
     * update an archival profile
     * @param recordsManagement/archivalProfile $archivalProfile The archival profile object
     *
     * @return boolean The request of the request
     */
    public function update($archivalProfile)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $archivalProfile = \laabs::cast($archivalProfile, 'recordsManagement/archivalProfile');

            $this->deleteDetail($archivalProfile);
            $this->createDetail($archivalProfile);

            // archival profile
            $this->sdoFactory->update($archivalProfile, "recordsManagement/archivalProfile");
            // Contents profiles
            $this->updateContainedProfiles($archivalProfile->archivalProfileId, $archivalProfile->containedProfiles);

            // Life cycle journal
            $eventItems = array('archivalProfileReference' => $archivalProfile->reference);
            $this->lifeCycleJournalController->logEvent('recordsManagement/ArchivalProfileModification', 'recordsManagement/archivalProfile', $archivalProfile->archivalProfileId, $eventItems);
        
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return true;
    }

    /**
     * Cpdate an archival content profile
     * @param string $archivalProfileId The parent profile identifier
     * @param array  $containedProfiles  The content profiles identifiers
     *
     * @return boolean The request of the request
     */
    protected function updateContainedProfiles($parentProfileId, $containedProfiles)
    {
        if (!count($containedProfiles)) {
            return;
        }

        // Validation
        foreach ($containedProfiles as $profileId) {
            try {
                $profile = $this->sdoFactory->read("recordsManagement/archivalProfile", $profileId);

            } catch (\Exception $e) {
                throw new \core\Exception\NotFoundException("$profileId can't be found.");
            }
            
            if (!$this->validateContainedProfile($parentProfileId, $profileId)) {
                throw new \core\Exception\ForbiddenException("$profile->reference can't be content in this archival profile."); 
            }
        }


        $oldcontainedProfiles = $this->sdoFactory->find("recordsManagement/archivalProfileRelationship", "parentProfileId = '$parentProfileId'");
        if (count($oldcontainedProfiles)) {
            $this->sdoFactory->deleteCollection($oldcontainedProfiles, "recordsManagement/archivalProfileRelationship");
        }

        $archivalProfileRelationships = [];
        foreach ($containedProfiles as $containedProfileId) {
            $archivalProfileRelationship = \laabs::newInstance('recordsManagement/archivalProfileRelationship');
            $archivalProfileRelationship->parentProfileId = $parentProfileId; 
            $archivalProfileRelationship->containedProfileId = $containedProfileId;

            $archivalProfileRelationships[] = $archivalProfileRelationship;
        }

        $this->sdoFactory->createCollection($archivalProfileRelationships, "recordsManagement/archivalProfileRelationship");

        return true;
    }

    /**
     * Validate an archival profile contents profiles
     * @param string $archivalProfileId The parent profile identifier
     * @param string $containedProfileId    The contents profiles identifiers to validate
     *
     * @return boolean The request of the request
     */
    protected function validateContainedProfile($parentProfileId, $containedProfileId)
    {
        if ($parentProfileId == $containedProfileId) {
            return false;
        }

        $relationships = $this->sdoFactory->find("recordsManagement/archivalProfileRelationship", "parentProfileId = '$containedProfileId'");

        if (count($relationships)) {
            foreach ($relationships as $relationship) {
                if (!$this->validateContainedProfile($parentProfileId, $relationship->containedProfileId)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * delete an archival profile
     * @param string $archivalProfileId The identifier of the archival profile
     *
     * @return boolean The request of the request
     */
    public function delete($archivalProfileId)
    {
        $archivalProfile = $this->sdoFactory->read('recordsManagement/archivalProfile', $archivalProfileId);


        // containedProfiles
        $archivalProfileRelationships = $this->sdoFactory->find("recordsManagement/archivalProfileRelationship", "parentProfileId='$archivalProfileId' or containedProfileId='$archivalProfileId'");
        if (count($archivalProfileRelationships)) {
            $this->sdoFactory->deleteCollection($archivalProfileRelationships, "recordsManagement/archivalProfileRelationship");
        }
        //$this->sdoFactory->deleteChildren("recordsManagement/archivalProfileRelationship", $archivalProfile);
        
        $this->deleteDetail($archivalProfile);

        $this->sdoFactory->delete($archivalProfile);

        // Life cycle journal
        $eventItems = array('archivalProfileReference' => $archivalProfile->reference);
        $this->lifeCycleJournalController->logEvent('recordsManagement/profileDestruction', 'recordsManagement/archivalProfile', $archivalProfile->archivalProfileId, $eventItems);

        return true;
    }

    /**
     * Get form of teh description class
     * @param type $archivalProfileReference The reference of the archival profile
     *
     * @return object The description class object parsed with the profile descriptions
     */
    public function descriptionForm($archivalProfileReference)
    {
        $archivalProfile = $this->getByReference($archivalProfileReference);
        $descriptionObject = \laabs::newController($archivalProfile->descriptionClass)->form($archivalProfile->archiveDescription);
        $descriptionObject->descriptionClass =  $archivalProfile->descriptionClass;

        return $descriptionObject;
    }

    protected function createDetail($archivalProfile)
    {
        if (!empty($archivalProfile->archiveDescription)) {
            $position = 0;
            foreach ($archivalProfile->archiveDescription as $description) {
                $description->archivalProfileId = $archivalProfile->archivalProfileId;
                $description->position = $position;
                $position++;

                $this->sdoFactory->create($description, 'recordsManagement/archiveDescription');
            }
        }
    }

    protected function deleteDetail($archivalProfile)
    {
        $this->sdoFactory->deleteChildren('recordsManagement/archiveDescription', $archivalProfile);

        /*$documentProfiles = $this->sdoFactory->readChildren('recordsManagement/documentProfile', $archivalProfile);
        foreach ($documentProfiles as $documentProfile) {
            $this->sdoFactory->deleteChildren('recordsManagement/documentDescription', $documentProfile);
        }

        $this->sdoFactory->deleteChildren('recordsManagement/documentProfile', $archivalProfile);*/
    }

    /**
     * Get the archival profile descriptions for the given org unit
     * @param string $orgRegNumber
     * @param string $originatorAccess
     * 
     * @return array
     */
    public function getOrgUnitArchivalProfiles($orgRegNumber, $originatorAccess=false)
    {
        $archivalProfileAccesses = [];
        $orgUnitArchivalProfiles = [];

        $organization = \laabs::callService('organization/organization/readByregnumber_registrationNumber_', $orgRegNumber);
        
        $archivalProfileAccesses = $this->sdoFactory->find('organization/archivalProfileAccess', "orgId='".$organization->orgId."'");
        
        foreach ($archivalProfileAccesses as $archivalProfileAccess) {
            $orgUnitArchivalProfiles[] = $this->getByReference($archivalProfileAccess->archivalProfileReference);
        }

        return $orgUnitArchivalProfiles;
    }

    /**
     * Get descendant archival profiles
     * 
     * @return array
     */
    public function getdescendantArchivalProfiles()
    {
        $descendantArchivalProfiles = [];
        $descendantServicesOrgId = [];

        $descendantServices = \laabs::callService('organization/userPosition/readDescendantservices');

        foreach ($descendantServices as $orgRegNumber) {
            $organization = \laabs::callService('organization/organization/readByregnumber_registrationNumber_', $orgRegNumber);
            if (!empty($organization)) {
                $descendantServicesOrgId[] = $organization->orgId;
            }
        }

        $archivalProfileAccesses = $this->sdoFactory->find('organization/archivalProfileAccess', "orgId=['". \laabs\implode("','" , $descendantServicesOrgId)."']");
        
        foreach ($archivalProfileAccesses as $archivalProfileAccess) {
            if (!empty($descendantArchivalProfiles[$archivalProfileAccess->archivalProfileReference])){
                continue;
            }
            $descendantArchivalProfiles[$archivalProfileAccess->archivalProfileReference] = $this->getByReference($archivalProfileAccess->archivalProfileReference);
        }

        return $descendantArchivalProfiles;
    }
}
