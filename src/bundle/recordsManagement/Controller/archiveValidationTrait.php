<?php

/*
 *  Copyright (C) 2017 Maarch
 *
 *  This file is part of bundle recordsManagement.
 *  Bundle recordsManagement is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Bundle recordsManagement is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\recordsManagement\Controller;

/**
 * Archive entry controller
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
trait archiveValidationTrait
{
    /**
     * Validate the archive compliance
     *
     * @param recordsManagement/archive $archive The archive to validate
     */
    public function validateCompliance($archive)
    {
        $this->validateFileplan($archive);
        $this->validateArchiveDescriptionObject($archive);
        $this->validateManagementMetadata($archive);
        $this->validateAttachments($archive);
        $this->validateProcessingStatus($archive);
    }

    /**
     * Check and set the processing status
     *
     * @param recordsManagement/archive $archive The archive to setting
     */
    public function validateProcessingStatus($archive)
    {
        if (empty($archive->processingStatus) || !isset($this->currentArchivalProfile)) {
            return;
        }

        $processingStatuses = json_decode($this->currentArchivalProfile->processingStatuses);

        if (!isset($processingStatuses->{$archive->processingStatus})) {
            throw new \core\Exception\BadRequestException("The processing status isn't initial");
        }

        $archiveProcessingStatus = $processingStatuses->{$archive->processingStatus};

        if ($archiveProcessingStatus->type != 'initial') {
            throw new \core\Exception\BadRequestException("The processing status isn't initial");
        }
    }

    /**
     * Validate archive description object
     *
     * @param recordsManagement/archive $archive The archive object
     */
    protected function validateArchiveDescriptionObject($archive)
    {
        if (!isset($this->currentArchivalProfile) || !is_object($archive->descriptionObject)) {
            return;
        }

        $this->validateDescriptionModel($archive->descriptionObject, $this->currentArchivalProfile);
    }

    public function validateDescriptionModel($object, $archivalProfile)
    {
        $descriptionSchemeProperties = $this->descriptionSchemeController->getDescriptionFields($archivalProfile->descriptionClass);

        $archivalProfileFields = [];

        foreach ($archivalProfile->archiveDescription as $archiveDescription) {
            if (!isset($object->{$archiveDescription->fieldName}) && $archiveDescription->required) {
                throw new \core\Exception\BadRequestException('Null value not allowed for metadata %1$s', 400, null, [$archiveDescription->fieldName]);
            }

            $archivalProfileFields[$archiveDescription->fieldName] = $archiveDescription;
        }
        foreach ($object as $name => $value) {
            if (!isset($archivalProfileFields[$name]) && !$archivalProfile->acceptUserIndex) {
                throw new \core\Exception\BadRequestException('Metadata %1$s is not allowed', 400, null, [$name]);
            }

            if (isset($descriptionSchemeProperties[$name])) {
                $this->validateDescriptionField($value, $descriptionSchemeProperties[$name]);
            }
        }
    }

    protected function validateDescriptionField($value, $descriptionField)
    {
        switch ($descriptionField->type) {
            case 'name':
                $this->validateName($value, $descriptionField);
                break;

            case 'text':
                $this->validateText($value, $descriptionField);
                break;

            case 'number':
                $this->validateNumber($value, $descriptionField);
                break;

            case 'boolean':
                $this->validateBoolean($value, $descriptionField);
                break;

            case 'date':
            case 'datetime':
                $this->validateDate($value, $descriptionField);
                break;

            case 'object':
                $this->validateObject($value, $descriptionField);
                break;

            case 'array':
                $this->validateArray($value, $descriptionField);
                break;

            default:
                if (is_string($descriptionField->type) && $descriptionField->type[0] == '#') {
                    $descriptionField->properties = $this->descriptionSchemeController->getDescriptionFields(substr($descriptionField->type, 1));
                    $this->validateObject($value, $descriptionField);
                }
        }
    }

    protected function validateName($value, $descriptionField)
    {
        if (!empty($descriptionField->enumeration) && !in_array($value, $descriptionField->enumeration) && $value != '') {
            throw new \core\Exception\BadRequestException('Forbidden value for metadata %1$s', 400, null, [$descriptionField->name]);
        }
        if (!empty($descriptionField->ref) && $descriptionField->ref) {
            $descriptionRefController = \laabs::newController('recordsManagement/descriptionRef');
            if (empty($descriptionRefController->get($descriptionField->name, $value))) {
                throw new \core\Exception\BadRequestException("Invalid value %s supplied for referentiel %s", 404, null, [$value, $descriptionField->label]);
            }

            return true;
        }
    }

    protected function validateText($value, $descriptionField)
    {
        return true;
    }

    protected function validateNumber($value, $descriptionField)
    {
        if (!is_numeric($value)) {
            throw new \core\Exception\BadRequestException('Invalid value for metadata %1$s', 400, null, [$descriptionField->name]);
        }
    }

    protected function validateBoolean($value, $descriptionField)
    {
        if (!is_bool($value) && !in_array($value, [0, 1])) {
            throw new \core\Exception\BadRequestException('Invalid value for metadata %1$s', 400, null, [$descriptionField->name]);
        }
    }

    protected function validateDate($value, $descriptionField)
    {
        if (!preg_match('#^\d{4}-\d{2}-\d{2}(T\d{2}:\d{2}(:\d{2})?(\.\d+)?(([+-]\d{2}:\d{2})|Z)?)?$#', $value)) {
            throw new \core\Exception\BadRequestException('Invalid value for metadata %1$s', 400, null, [$descriptionField->name]);
        }

        try {
            new \DateTime($value);
        } catch (\Exception $e) {
            throw new \core\Exception\BadRequestException('Invalid value for metadata %1$s', 400, null, [$descriptionField->name]);
        }
    }

    protected function validateObject($object, $descriptionField)
    {
        // Validate object against type properties
        if (isset($descriptionField->properties)) {
            foreach ($descriptionField->properties as $name => $property) {
                if (!isset($object->{$name})) {
                    if (isset($property->required)) {
                        throw new \core\Exception\BadRequestException('Null value not allowed for metadata %1$s', 400, null, [$descriptionField->name.'-'.$name]);
                    }
                    continue;
                }

                $value = $object->{$name};

                $this->validateDescriptionField($value, $property);
            }
        }

        // Validate additionnal properties
        foreach ($object as $name => $value) {
            if (isset($descriptionField->properties) && !array_key_exists($name, $descriptionField->properties) && !isset($descriptionField->additionnalProperties)) {
                throw new \core\Exception\BadRequestException('Metadata %1$s is not allowed', 400, null, [$descriptionField->name.'-'.$name]);
            }
        }
    }

    protected function validateArray($array, $descriptionField)
    {
        if (isset($descriptionField->minItems) && count($array) < $descriptionField->minItems) {
            throw new \core\Exception\BadRequestException('Metadata %1$s does not have enough values', 400, null, [$descriptionField->name.'-'.$name]);
        }

        if (isset($descriptionField->maxItems) && count($array) > $descriptionField->maxItems) {
            throw new \core\Exception\BadRequestException('Metadata %1$s has too many values', 400, null, [$descriptionField->name.'-'.$name]);
        }

        if (isset($descriptionField->itemType)) {
            if (is_string($descriptionField->itemType)) {
                $descriptionField = (object) [
                    "type" => $descriptionField->itemType
                ];
            } else {
                $descriptionField = $descriptionField->itemType;
            }
            foreach ($array as $name => $value) {
                $this->validateDescriptionField($value, $descriptionField);
            }
        }
    }

    /**
     * Validate the archive management metadata
     *
     * @param \bundle\recordsManagement\Controller\recordsManagement/archive $archive
     */
    public function validateManagementMetadata($archive)
    {
        $organization = $this->sdoFactory->read('organization/organization', ['registrationNumber' => $archive->originatorOrgRegNumber]);

        if (!is_null($organization->enabled) && $organization->enabled === false) {
            throw new \core\Exception("The deposit has been blocked because activity is disabled.");
        }

        $this->checkRights($archive);

        if (isset($archive->archivalProfileReference) && !$this->sdoFactory->exists("recordsManagement/archivalProfile", ["reference"=>$archive->archivalProfileReference])) {
            throw new \core\Exception\NotFoundException("The archival profile reference not found");
        }

        if (isset($archive->retentionRuleCode) && !$this->sdoFactory->exists("recordsManagement/retentionRule", $archive->retentionRuleCode)) {
            throw new \core\Exception\NotFoundException("The retention rule not found");
        }

        if (isset($archive->accessRuleCode) && !$this->sdoFactory->exists("recordsManagement/accessRule", $archive->accessRuleCode)) {
            throw new \core\Exception\NotFoundException("The access rule not found");
        }

        $nbArchiveObjects = 0;

        if (!empty($archive->contents)) {
            $nbArchiveObjects = count($archive->contents);
        }

        if ($nbArchiveObjects) {
            $containedProfiles = [];
            if (isset($archive->archivalProfileReference)) {
                $this->useArchivalProfile($archive->archivalProfileReference);
                foreach ($this->currentArchivalProfile->containedProfiles as $profile) {
                    $containedProfiles[] = $profile->reference;
                }
            }

            for ($i = 0; $i < $nbArchiveObjects; $i++) {
                if (isset($archive->archivalProfileReference)) {
                    if (empty($archive->contents[$i]->archivalProfileReference)) {
                        if (!$this->currentArchivalProfile->acceptArchiveWithoutProfile) {
                            throw new \core\Exception\BadRequestException("Invalid contained archive profile %s", 400, null, $archive->contents[$i]->archivalProfileReference);
                        }
                    } elseif (!in_array($archive->contents[$i]->archivalProfileReference, $containedProfiles)) {
                        throw new \core\Exception\BadRequestException("Invalid contained archive profile %s", 400, null, $archive->contents[$i]->archivalProfileReference);
                    }
                }

                $this->validateManagementMetadata($archive->contents[$i]);
            }
        }
    }

    /**
     * Validate archival profile of a child archive
     *
     * @param \bundle\recordsManagement\Controller\recordsManagement/archive $archive
     */
    public function validateFileplan($archive)
    {
        // No parent, check orgUnit can deposit with the profile
        if (empty($archive->parentArchiveId)) {
            if (!$this->organizationController->checkProfileInOrgAccess($archive->archivalProfileReference, $archive->originatorOrgRegNumber)) {
                throw new \core\Exception\BadRequestException("Invalid archive profile");
            }

            return;
        }

        // Parent : read and check fileplan
        $parentArchive = $this->sdoFactory->read('recordsManagement/archive', $archive->parentArchiveId);

        // Check level in file plan
        if ($parentArchive->fileplanLevel == 'item') {
            throw new \core\Exception\BadRequestException("Parent archive is an item and can not contain items.");
        }

        // No profile on parent, accept any profile
        if (empty($parentArchive->archivalProfileReference)) {
            return;
        }

        // Load parent archive profile
        if (!isset($this->archivalProfiles[$parentArchive->archivalProfileReference])) {
            $parentArchivalProfile = $this->archivalProfileController->getByReference($parentArchive->archivalProfileReference);
            $this->archivalProfiles[$parentArchive->archivalProfileReference] = $parentArchivalProfile;
        } else {
            $parentArchivalProfile = $this->archivalProfiles[$parentArchive->archivalProfileReference];
        }

        // No profile : check parent profile accepts archives without profile
        if (empty($archive->archivalProfileReference) && $parentArchivalProfile->acceptArchiveWithoutProfile) {
            return;
        }

        // Profile on content : check profile is accepted
        foreach ($parentArchivalProfile->containedProfiles as $containedProfile) {
            if ($containedProfile->reference == $archive->archivalProfileReference) {
                return;
            }
        }

        throw new \core\Exception\BadRequestException("Invalid archive profile");
    }


    /**
     * Validate the archive management metadata
     *
     * @param \bundle\recordsManagement\Controller\recordsManagement/archive $archive
     */
    protected function validateAttachments($archive)
    {
        if (!$archive->digitalResources) {
            $archive->digitalResources = [];
        }

        foreach ($archive->digitalResources as $digitalResource) {
            if (empty($digitalResource->archiveId)) {
                $digitalResource->archiveId = $archive->archiveId;
            }

            if (empty($digitalResource->resId)) {
                $digitalResource->resId = \laabs::newId();
            }

            $this->validateDigitalResource($digitalResource);

            if (empty($digitalResource->hash) || empty($digitalResource->hashAlgorithm)) {
                $this->digitalResourceController->getHash($digitalResource, $this->hashAlgorithm);
            }
        }

        $nbArchiveObjects = 0;
        if (!empty($archive->contents)) {
            $nbArchiveObjects = count($archive->contents);
        }

        for ($i = 0; $i < $nbArchiveObjects; $i++) {
            $this->validateAttachments($archive->contents[$i]);
        }
    }

    /**
     * Validate resource content
     *
     * @param \bundle\digitalResource\digitalResource $digitalResource resource
     *
     * @return
     */
    public function validateDigitalResource($digitalResource)
    {
        // Create temp file
        $handler = $digitalResource->getHandler();
        $filename = tempnam(sys_get_temp_dir(), 'digitalResource.format');
        $temp = fopen($filename, 'w');
        stream_copy_to_stream($handler, $temp);
        rewind($handler);
        fclose($temp);

        $digitalResource->size = filesize($filename);

        if ($digitalResource->size == 0) {
            unlink($filename);
            throw new \bundle\recordsManagement\Exception\invalidArchiveException('Resource size is null', 400);
        }

        $finfo = new \finfo();
        $digitalResource->mimetype = $finfo->file($filename, FILEINFO_MIME_TYPE);

        $formatDetection = strrpos($this->currentServiceLevel->control, "formatDetection") === false ? false : true;
        if ($formatDetection) {
            $format = $this->formatController->identifyFormat($filename);

            if ($format) {
                $digitalResource->puid = $format->puid;
            }
        }

        $formatValidation = strrpos($this->currentServiceLevel->control, "formatValidation") === false ? false : true;
        if ($formatValidation) {
            $validation = $this->formatController->validateFormat($filename);
            if (!$validation !== true && is_array($validation)) {
                unlink($filename);
                throw new \core\Exception\BadRequestException("Invalid format attachments for %s", 404, null, [$digitalResource->fileName]);
            }
        }
        unlink($filename);
    }
}
