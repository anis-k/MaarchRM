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
 * Trait for archives modification
 */
trait archiveModificationTrait
{

    /**
     * Read the retention rule of an archive
     * @param string $archiveId The archive identifier
     *
     * @return recordsManagement/archiveRetentionRule The retention rule object
     */
    public function editArchiveRetentionRule($archiveId)
    {
        $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);

        if ($archive->status === 'frozen') {
            throw new \bundle\recordsManagement\Exception\retentionRuleException('A frozen archive can\'t be modified.');
        }

        return \laabs::castMessage($archive, 'recordsManagement/archiveRetentionRule');

    }

    /**
     * Read the access rule of an archive
     * @param string $archiveId The archive identifier
     *
     * @return recordsManagement/archiveAccessRule The access rule updated
     */
    public function editArchiveAccessRule($archiveId)
    {
        $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);

        if ($archive->status === 'frozen') {
            throw new \bundle\recordsManagement\Exception\retentionRuleException('A frozen archive can\'t be modified.');
        }

        $this->getAccessRule($archive);

        return \laabs::castMessage($archive, 'recordsManagement/archiveAccessRule');
    }

    /**
     * Modify the archive retention rule
     * @param recordsManagement/archiveRetentionRule $retentionRule The retention rule object
     * @param mixed                                  $archiveIds    The archives ids
     *
     * @return bool The result of the operation
     */
    public function modifyRetentionRule($retentionRule, $archiveIds)
    {

        $retentionRuleReceived = $retentionRule;

        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $res = array('success' => array(), 'error' => array());

        $archives = array();

        if (!$currentOrg = \laabs::getToken("ORGANIZATION")) {
            throw \laabs::newException('recordsManagement/noOrgUnitException', "Permission denied: You have to choose a working organization unit to proceed this action.");
        }

        foreach ($archiveIds as $archiveId) {
            $archive = $this->retrieve($archiveId);
            $this->checkRights($archive);

            if (!in_array($archive->status, array("preserved"))) {
                array_push($res['error'], $archiveId);

                $operationResult = false;
            } else {
                $retentionRule = clone($retentionRuleReceived);

                $retentionRule->archiveId = $archiveId;

                // Update current object for caller
                if ($retentionRule->changeStartDate === false) {
                    $retentionRule->retentionStartDate = $archive->retentionStartDate;
                }

                if (empty($retentionRule->retentionRuleCode)) {
                    $retentionRule->retentionRuleCode = $archive->retentionRuleCode;
                    $retentionRule->retentionDuration = $archive->retentionDuration;
                }

                if ($retentionRule->finalDisposition === null) {
                    $retentionRule->finalDisposition = $archive->finalDisposition;
                } elseif ($retentionRule->finalDisposition === '') {
                    $retentionRule->finalDisposition = null;
                }

                if ($retentionRule->retentionDuration === '') {
                    $retentionRule->retentionDuration = null;
                } elseif (!empty($retentionRule->retentionDuration) && $retentionRule->retentionDuration->y >= 9999) {
                    $retentionRule->disposalDate = null;
                } else {
                    if (!empty($retentionRule->retentionDuration) && !empty($retentionRule->retentionStartDate)) {
                        $retentionRule->disposalDate = $this->calculateDate($retentionRule->retentionStartDate, $retentionRule->retentionDuration);
                    }
                }

                $retentionRule->retentionRuleStatus = "current";

                $this->sdoFactory->update($retentionRule, 'recordsManagement/archive');

                $retentionRule->previousStartDate = $archive->retentionStartDate;
                $retentionRule->previousDuration = $archive->retentionDuration;
                $retentionRule->previousFinalDisposition = $archive->finalDisposition;

                array_push($res['success'], $archiveId);

                $operationResult = true;

                $archives[] = $archive;

                // Life cycle journal
                $this->logRetentionRuleModification($archive, $retentionRule, $operationResult);
            }
        }

        return $res;
    }

    /**
     * Modify the archive access
     * @param recordsManagement/archiveAccessCode $accessRule The access rule object
     * @param array                               $archiveIds The archives ids
     *
     * @return bool The result of the operation
     */
    public function modifyAccessRule($accessRule, $archiveIds)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $res = array('success' => array(), 'error' => array());

        $archives = array();
        $operationResult = null;

        $accessRuleReceived = $accessRule;

        foreach ($archiveIds as $archiveId) {
            $archive = $this->retrieve($archiveId);
            $this->checkRights($archive);

            if (!in_array($archive->status, array("preserved"))) {
                array_push($res['error'], $archiveId);

                $operationResult = false;
            } else {
                $accessRule = clone($accessRuleReceived);

                $accessRule->archiveId = $archiveId;

                if (!$accessRule->changeStartDate) {
                    $accessRule->accessRuleStartDate = $archive->accessRuleStartDate;
                }

                if (empty($accessRule->accessRuleCode)) {
                    $accessRule->accessRuleCode = $archive->accessRuleCode;
                    $accessRule->accessRuleDuration = $archive->accessRuleDuration;
                }

                if ($accessRule->accessRuleDuration === '') {
                    $accessRule->accessRuleDuration = null;
                } elseif (!empty($accessRule->accessRuleDuration) && $accessRule->accessRuleDuration->y >= 9999) {
                    $accessRule->accessRuleComDate = null;
                } elseif (!empty($accessRule->accessRuleDuration) && !empty($accessRule->accessRuleStartDate)) {
                    $accessRule->accessRuleComDate = $this->calculateDate($accessRule->accessRuleStartDate, $accessRule->accessRuleDuration);
                }

                $this->sdoFactory->update($accessRule, 'recordsManagement/archive');

                $accessRule->previousAccessRuleStartDate = $archive->accessRuleStartDate;
                $accessRule->previousAccessRuleDuration = $archive->accessRuleDuration;

                $archive->accessRuleStartDate = $accessRule->accessRuleStartDate;
                $archive->accessRuleDuration = $accessRule->accessRuleDuration;
                $archive->accessRuleComDate = $accessRule->accessRuleComDate;

                array_push($res['success'], $archiveId);

                $operationResult = true;

                $archives[] = $archive;

                // Life cycle journal
                $this->logAccessRuleModification($archive, $accessRule, $operationResult);
            }
        }

        return $res;
    }

    /**
     * Suspend archives
     * @param mixed $archiveIds Array of archive identifier
     *
     * @return array
     */
    public function freeze($archiveIds)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $archives = array();

        foreach ($archiveIds as $archiveId) {
            $archive = $this->retrieve($archiveId);
            $this->checkRights($archive);

            $archives[$archiveId] = $archive;
        }

        $res = $this->setStatus($archiveIds, 'frozen');


        for ($i = 0, $count = count($res['success']); $i < $count; $i++) {
            $archive = $archives[$res['success'][$i]];
            $this->logFreeze($archive, true);
        }

        for ($i = 0, $count = count($res['error']); $i < $count; $i++) {
            $archive = $archives[$res['error'][$i]];
            $this->logFreeze($archive, false);
        }

        return $res;
    }

    /**
     * Liberate archives
     * @param mixed $archiveIds Array of archive identifier
     *
     * @return array
     */
    public function unfreeze($archiveIds)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $archives = array();

        foreach ($archiveIds as $archiveId) {
            $archive = $this->retrieve($archiveId);
            $this->checkRights($archive);

            $archives[$archiveId] = $archive;
        }

        $res = $this->setStatus($archiveIds, 'preserved');


        for ($i = 0, $count = count($res['success']); $i < $count; $i++) {
            $archive = $archives[$res['success'][$i]];
            $this->logUnfreeze($archive, true);
        }

        for ($i = 0, $count = count($res['error']); $i < $count; $i++) {
            $archive = $archives[$res['error'][$i]];
            $this->logUnfreeze($archive, false);
        }

        return $res;
    }
    
    /**
     * Update metadata of archive
     * @param string $archiveId
     * @param string $originatorArchiveId
     * @param string $archiverArchiveId
     * @param string $archiveName
     * @param string $description
     * @param date   $originatingDate
     *
     * @return boolean The result of the operation
     */
    public function modifyMetadata(
        $archiveId,
        $originatorArchiveId = null,
        $archiverArchiveId = null,
        $archiveName = null,
        $originatingDate = null,
        $description = null,
        $checkAccess = true
    ) {
        $archive = $this->retrieve($archiveId, $withData = false, $checkAccess);

        if ($checkAccess) {
            $this->checkRights($archive);
        }

        if (!empty($archive->archivalProfileReference)) {
            $archivalProfileDescription = \laabs::callService('recordsManagement/archivalProfile/readByreference_reference_', $archive->archivalProfileReference)->archiveDescription;
        }

        if ($archiveName) {
            $archive->archiveName = $archiveName;
        }
        
        if ($originatorArchiveId) {
            $archive->originatorArchiveId = $originatorArchiveId;
        }

        if ($archiverArchiveId) {
            $archive->archiverArchiveId = $archiverArchiveId;
        }

        if ($originatingDate) {
            $archive->originatingDate = $originatingDate;
        }

        $publicArchives = \laabs::configuration('presentation.maarchRM')['publicArchives'];

        if (!empty($description)) {
            $descriptionObject = $description;

            if (!empty($archivalProfileDescription)) {
                foreach ($archivalProfileDescription as $descriptionImmutable) {
                    if ($descriptionImmutable->isImmutable) {
                        $fieldName = (string)$descriptionImmutable->fieldName;
                        if ($descriptionObject->$fieldName != $archive->descriptionObject->$fieldName) {
                            throw new \bundle\recordsManagement\Exception\invalidArchiveException('Invalid object');
                        }
                    }

                }
            }
            
            if (!empty($archive->archivalProfileReference) && !$publicArchives) {
                $this->useArchivalProfile($archive->archivalProfileReference);
                
                if (!empty($this->currentArchivalProfile->descriptionClass)) {
                    $this->validateDescriptionClass($descriptionObject, $this->currentArchivalProfile);
                } else {
                    $this->validateDescriptionModel($descriptionObject, $this->currentArchivalProfile);
                }
            }

            if (!empty($archive->descriptionClass)) {
                $descriptionController = $this->useDescriptionController($archive->descriptionClass);
            } else {
                $descriptionController = $this->useDescriptionController('recordsManagement/description');
            }
            $archive->descriptionObject = $descriptionObject;

            $descriptionController->update($archive);
        }

        $this->sdoFactory->update($archive, 'recordsManagement/archive');
        
        $operationResult = true;
        $res = true;
        
        $this->logMetadataModification($archive, $operationResult);
            
        return $res;
    }

    /**
     * Add a relationship to the archive
     * @param recordsManagement/archiveRelationship $archiveRelationship The relationship of the archive
     *
     * @return bool The result of the operation
     */
    public function addRelationship($archiveRelationship)
    {
        $this->archiveRelationshipController->createRelationship($archiveRelationship);

        $archive = $this->retrieve($archiveRelationship->archiveId, $withBinary = false, $checkAccess = false);

        // Life cycle journal
        $this->logRelationshipAdding($archive, $archiveRelationship);
       
        return true;
    }

    /**
     * delete a relationship
     * @param recordsManagement/archiveRelationship $archiveRelationship The archive relationship object
     *
     * @return recordsManagement/archiveRelationship
     */
    public function deleteRelationship($archiveRelationship)
    {
        $this->archiveRelationshipController->deleteRelationship($archiveRelationship);

        $archive = $this->retrieve($archiveRelationship->archiveId, $withBinary = false, $checkAccess = false);

        // Life cycle journal
        $this->logRelationshipDeleting($archive, $archiveRelationship);

        return true;
    }


    /**
     * Index full text 
     * @param int $limit The maximum number of archive to index
     *
     * @return array The result of the operation
     */
    public function indexFullText($limit=200)
    {
        $res = [];
        $res['success'] = [];
        $res['fail'] = [];
        $archivesToIndex = $this->sdoFactory->find('recordsManagement/archive', "fullTextIndexation='requested'", [], null, null, $limit);
        if (isset(\laabs::configuration('recordsManagement')['stopWordsFilePath'])) {
            $stopWords = \laabs::configuration('recordsManagement')['stopWordsFilePath'];
            $stopWords = utf8_encode(file_get_contents($stopWords));
            $stopWords = preg_replace('/[\r\n]/', " ",$stopWords);
            $stopWords = explode(" ", $stopWords);
        }

        if (count($archivesToIndex)) {
            $descriptionController = $this->useDescriptionController('recordsManagement/description');

            foreach ($archivesToIndex as $archive) {
                $archive = $this->retrieve($archive->archiveId);

                try {
                    $fullText = $this->digitalResourceController->getFullTextByArchiveId($archive->archiveId);
                    $fullText = strtolower($fullText);
                    $fullText = preg_replace('/[.,\/#!?$%\^&\*;:{}=\-_\'`~()\r\n]|\s+/'," ", $fullText);

                    if (isset($stopWords)) {
                        $fullTextArray = explode(" ", $fullText);
                        $fullTextArray = array_diff($fullTextArray, $stopWords);
                        $fullText = implode(" ", $fullTextArray);
                    } else {
                        $fullText = preg_replace('/\b[a-z]{1,2}\b/', "",  $fullText);
                    }

                    $descriptionController->create($archive, $fullText);
                    $archive->fullTextIndexation = "indexed";
                    $this->sdoFactory->update($archive, 'recordsManagement/archiveIndexationStatus');

                    $operationResult = true;

                } catch(\Exception $e) {
                    $operationResult = false;
                    $archive->fullTextIndexation = "failed";
                    $this->sdoFactory->update($archive, 'recordsManagement/archiveIndexationStatus');
                }

                $this->logMetadataModification($archive, $operationResult);

                if ($operationResult) {
                    $res['success'][] = (string)$archive->archiveId;
                } else {
                    $res['error'][] = (string)$archive->archiveId;
                }
            }
        }

        return $res;
    }

    /**
     * Update archive with changed retention rule
     * @param int $limit The maximum number of archive to update
     */
    public function updateArchiveRetentionRule($limit = 1000)
    {
        $archives = $this->sdoFactory->find('recordsManagement/archive', 'retentionRuleCode != null AND retentionStartDate != null AND retentionDuration !=null AND retentionRuleStatus = "changed"', null, null, null, $limit);
        $retentionRules = [];

        foreach ($archives as $archive) {
            $retentionRule = new \stdClass();
            $retentionRule->archiveId = $archive->archiveId;
            $retentionRule->previousStartDate = $archive->retentionStartDate;
            $retentionRule->previousDuration = $archive->retentionDuration;
            $retentionRule->previousFinalDisposition = $archive->finalDisposition;

            if (!isset($retentionRules[$archive->retentionRuleCode])) {
                $retentionRules[$archive->retentionRuleCode] = $this->retentionRuleController->read($archive->retentionRuleCode);
            }

            $archive->retentionDuration =  $retentionRules[$archive->retentionRuleCode]->duration;
            $archive->disposalDate = $this->calculateDate($archive->retentionStartDate, $archive->retentionDuration);

            $retentionRule->retentionStartDate = $archive->retentionStartDate;
            $retentionRule->retentionDuration = $archive->retentionDuration;
            $retentionRule->finalDisposition = $archive->finalDisposition;

            $archive->retentionRuleStatus = "current";
            $this->sdoFactory->update($archive, 'recordsManagement/archiveRetentionRule');

            // Life cycle journal
            $this->logRetentionRuleModification($archive, $retentionRule, true);
        }
    }

    /**
     * Convert and store the resource
     *
     * @param string $archiveId   The archive identifier
     * @param string $contents    The resource contents
     * @param string $filename    The optional filename
     * @param string $checkAccess Check access to archive. If false caller MUST check access before.
     *
     * @return The new resource identifier
     */
    public function addResource($archiveId, $contents, $filename = false, $checkAccess = true)
    {
        // Valid URL file:// http:// data://
        if (filter_var($contents, FILTER_VALIDATE_URL)) {
            $contents = stream_get_contents($contents);
        } else if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $contents)) {
            $contents = base64_decode($contents);
        } elseif (is_file($contents)) {
            if (empty($filename)) {
                $filename = basename($contents);
            }
            $contents = file_get_contents($contents);
        }

        $digitalResource = $this->digitalResourceController->createFromContents($contents, $filename);

        $digitalResource->archiveId = $archiveId;
        $digitalResource->resId = \laabs::newId();

        $archive = $this->sdoFactory->read("recordsManagement/archive", $archiveId);

        // Check rights ?
        if ($checkAccess) {
            $this->checkRights($archive);
        }
        
        $this->useServiceLevel('deposit', $archive->serviceLevelReference);
    
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $this->digitalResourceController->openContainers($this->currentServiceLevel->digitalResourceClusterId, $archive->storagePath);
            $this->digitalResourceController->store($digitalResource);

            $this->logAddResource($archive, $digitalResource, true);
        } catch (\Exception $e) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            $this->logAddResource($archive, $digitalResource, false);

            throw $e;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $digitalResource->resId;
    }

    /**
     * Update user access
     * @param string $archiveId         The archive unit identifier
     * @param array  $userOrgRegNumbers The user org registration numbers
     * @param string $checkAccess       Check access to archive. If false caller MUST check access before.
     *
     * @return bool
     */
    public function updateUserOrgRegNumbers($archiveId, array $userOrgRegNumbers, $checkAccess = true)
    {
        $archive = $this->sdoFactory->read("recordsManagement/archive", $archiveId);

        // Check rights ?
        if ($checkAccess) {
            $this->checkRights($archive);
        }

        $archiveUserOrgRegNumbers = \laabs::newInstance('recordsManagement/archiveUserOrgRegNumbers');
        $archiveUserOrgRegNumbers->archiveId = $archiveId;
        $archiveUserOrgRegNumbers->userOrgRegNumbers = \laabs::newTokenList($userOrgRegNumbers);
        $archiveUserOrgRegNumbers->lastModificationDate = \laabs::newTimestamp();

        $this->sdoFactory->update($archiveUserOrgRegNumbers);
    }
}
