<?php
/* 
 * Copyright (C) Maarch
 *
 * This file is part of bundle Mades
 *
 * Bundle Mades is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle Mades is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle Mades. If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\mades\Controller;

/**
 * Class for archive transfer
 *
 * @package Mades
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class ArchiveTransfer extends Message implements \bundle\medona\Controller\ArchiveTransferInterface
{
    public $errors = [];
    public $infos = [];
    public $replyCode;
    public $filePlan = [];
    public $originatorOrgs = [];
    public $processedArchives = [];
    public $processedRelationships = [];

    protected $orgController;
    protected $archiveController;
    protected $archivalProfileController;

    public function __construct()
    {
        $this->orgController = \laabs::newController('organization/organization');
        $this->archiveController = \laabs::newController('recordsManagement/archive');
        $this->archivalProfileController = \laabs::newController('recordsManagement/archivalProfile');
    }

    /**
     * Receive message with all contents embedded
     * @param string $message The message object
     *
     * @return medona/message The acknowledgement
     */
    public function receive($message)
    {
        $data = file_get_contents($message->path);

        $message->object = $archiveTransfer = json_decode($data);

        if (isset($archiveTransfer->comment)) {
            $message->comment = $archiveTransfer->comment;
        }
        $message->date = $archiveTransfer->date;

        $message->senderOrgRegNumber = $archiveTransfer->transferringAgency->identifier;
        $message->recipientOrgRegNumber = $archiveTransfer->archivalAgency->identifier;

        $message->reference = $archiveTransfer->messageIdentifier;
        
        if (isset($archiveTransfer->archivalAgreement)) {
            $message->archivalAgreementReference = $archiveTransfer->archivalAgreement;
        }

        $binaryDataObjects = $physicalDataObjects = [];
        $message->dataObjectCount = 0;

        if (isset($archiveTransfer->dataObjectPackage->binaryDataObjects)) {
            $message->dataObjectCount += count($archiveTransfer->dataObjectPackage->binaryDataObjects);
            $this->receiveAttachments($message);
        }
        if (isset($archiveTransfer->dataObjectPackage->physicalDataObject)) {
            $message->dataObjectCount += count($archiveTransfer->dataObjectPackage->physicalDataObjects);
        }

        return $message;
    }

    protected function receiveAttachments($message)
    {
        $this->validateReference($message->object->dataObjectPackage->descriptiveMetadata, $message->object->dataObjectPackage->binaryDataObjects);
        
        $dirname = dirname($message->path);
        
        $messageFiles = [$message->path];
        foreach ($message->object->dataObjectPackage->binaryDataObjects as $dataObjectId => $binaryDataObject) {
            $message->size += (integer) $binaryDataObject->size;

            if (!isset($binaryDataObject->attachment->filename)) {
                $this->sendError("211", "Le document identifié par le nom '$dataObjectId' n'a pas été trouvé.");

                continue;
            }

            $attachment = $binaryDataObject->attachment;

            if (isset($attachment->filename)) {
                $filename = $dirname.DIRECTORY_SEPARATOR.$attachment->filename;
                if (!is_file($filename)) {
                    $this->sendError("211", "e document identifié par le nom '$attachment->filename' n'a pas été trouvé.");

                    continue;
                }

                $contents = file_get_contents($filename);
            } elseif (isset($attachment->uri)) {
                $contents = file_get_contents($attachment->uri);

                if (!$contents) {
                    $this->sendError("211", "Le document à l'adresse '$attachment->uri' est indisponible.");

                    continue;
                }

                $filename = $dirname.DIRECTORY_SEPARATOR.$dataObjectId;
                file_put_contents($filename, $contents);

            } elseif (isset($attachment->content)) {
                if (strlen($attachment->content) == 0) {
                    $this->sendError("211", "Le contenu du document n'a pas été transmis.");

                    continue;
                }

                $contents = base64_decode($attachment->content);
                $filename = $dirname.DIRECTORY_SEPARATOR.$dataObjectId;
                file_put_contents($filename, $contents);
            }

            $messageFiles[] = $filename;

            // Validate hash
            $messageDigest = $binaryDataObject->messageDigest;
            if (strtolower($messageDigest->content) != strtolower(hash($messageDigest->algorithm, $contents))) {
                $this->sendError("207", "L'empreinte numérique du document '".basename($filename)."' ne correspond pas à celle transmise.");

                continue;
            }
        }

        $receivedFiles = glob($dirname.DIRECTORY_SEPARATOR."*.*");

        // Check all files received are part of the message
        foreach ($receivedFiles as $receivedFile) {
            if (!in_array($receivedFile, $messageFiles)) {
                $this->sendError("101", "Le fichier '".basename($receivedFile)."' n'est pas référencé dans le message.");
            }
        }
    }

    protected function validateReference($archiveUnitContainer, $binaryDataObjects)
    {
        foreach ($archiveUnitContainer as $archiveUnit) {
            if (!empty($archiveUnit->dataObjectReferences)) {
                foreach ($archiveUnit->dataObjectReferences as $dataObjectId) {
                    if (!isset($binaryDataObjects->{$dataObjectId})) {
                        $this->sendError("213", "Le document identifié par '$dataObjectId' est introuvable.");

                        continue;
                    }
                }
            }

            if (!empty($archiveUnit->archiveUnit)) {
                $this->validateReference($archiveUnit, $binaryDataObjects);
            }
        }
    }

    /**
     * Validate message against schema and rules
     * @param string $messageId The message identifier
     * @param object $archivalAgreement The archival agreement
     *
     * @return boolean The validation result
     */
    public function validate($message, $archivalAgreement = null)
    {
        $this->errors = array();
        $this->replyCode = null;

        if (!empty($archivalAgreement)) {
            if ($archivalAgreement->originatorOrgIds) {
                $this->knownOrgUnits = [];
        
                $this->validateOriginators($message->object->dataObjectPackage->descriptiveMetadata, $archivalAgreement);
            }

            if ($archivalAgreement->signed && !isset($message->object->signature)) {
                $this->sendError("309");
            }

            /*if (isset($archivalAgreement->archivalProfileReference)
                || isset($message->object->dataObjectPackage->managementMetadata->archivalProfile)) {
                $this->validateProfile($message, $archivalAgreement);
            }*/
        }

        $this->validateDataObjects($message, $archivalAgreement);
        $this->validateArchiveUnits($message, $archivalAgreement);
      
        return true;
    }

    protected function validateOriginators($archiveUnitContainer, $archivalAgreement)
    {
        foreach ($archiveUnitContainer as $id => $archiveUnit) {
            if (isset($archiveUnit->filing->activity) && !isset($knownOrgUnits[$archiveUnit->filing->activity])) {
                try {
                    $this->knownOrgUnits[$archiveUnit->filing->activity] = $orgUnit = $this->orgController->getOrgByRegNumber($archiveUnit->filing->activity);
                } catch (\Exception $e) {
                    $this->sendError("200", "Le producteur de l'archive identifié par '$archiveUnit->filing->activity' n'est pas référencé dans le système.");

                    continue;
                }
                
                if (!in_array('originator', (array) $orgUnit->orgRoleCodes)) {
                    $this->sendError("302", "Le service identifié par '$archiveUnit->filing->activity' n'est pas référencé comme producteur dans le système.");
                
                    continue;
                }

                if (!in_array((string) $orgUnit->orgId, (array) $archivalAgreement->originatorOrgIds)) {
                    $this->sendError("302", "Le producteur de l'archive identifié par '$archiveUnit->filing->activity' n'est pas indiqué dans l'accord de versement.");
                }
            }

            if (!empty($archiveUnit->archiveUnits)) {
                $this->validateOriginators($archiveUnit, $archivalAgreement);
            }
        }
    }

    protected function validateAttachments($message, $archivalAgreement)
    {
        $serviceLevelController = \laabs::newController("recordsManagement/serviceLevel");

        if (isset($message->object->dataObjectPackage->managementMetadata->serviceLevel)) {
            $serviceLevelReference = $message->object->dataObjectPackage->managementMetadata->serviceLevel->value;
        } elseif (isset($archivalAgreement)) {
            $serviceLevelReference = $archivalAgreement->serviceLevelReference;
        }

        $serviceLevel = $serviceLevelController->getByReference($serviceLevelReference);

        $formatController = \laabs::newController("digitalResource/format");
        if ($archivalAgreement) {
            $allowedFormats = \laabs\explode(' ', $archivalAgreement->allowedFormats);
        } else {
            $allowedFormats = [];
        }

        $binaryDataObjects = $message->object->dataObjectPackage->binaryDataObjects;

        $messageDir = dirname($message->path);
        
        foreach ($binaryDataObjects as $dataObjectId => $binaryDataObject) {
            if (!isset($binaryDataObject->attachment)) {
                continue;
            }

            $attachment = $binaryDataObject->attachment;

            if (isset($attachment->filename)) {
                $filepath = $messageDir.DIRECTORY_SEPARATOR.$attachment->filename;
            } else {
                $filepath = $messageDir.DIRECTORY_SEPARATOR.$dataObjectId;
            }

            $contents = file_get_contents($filepath);

            // Get file format information
            $fileInfo = new \stdClass();

            if (strpos($serviceLevel->control, 'formatDetection') !== false) {
                $format = $formatController->identifyFormat($filepath);

                if (!$format) {
                    $this->sendError("205", "Le format du document '".basename($filepath)."' n'a pas pu être déterminé");
                } else {
                    $puid = $format->puid;
                    $fileInfo->format = $format;
                }
            }

            // Validate format is allowed
            if (count($allowedFormats) && isset($puid) && !in_array($puid, $allowedFormats)) {
                $this->sendError("307", "Le format du document '".basename($filepath)."' ".$puid." n'est pas autorisé par l'accord de versement.");
            }

            // Validate format
            if (strpos($serviceLevel->control, 'formatValidation') !== false) {
                $validation = $formatController->validateFormat($filepath);
                if (!$validation !== true && is_array($validation)) {
                    $this->sendError("307", "Le format du document '".basename($filepath)."' n'est pas valide : ".implode(', ', $validation));
                }
                $this->infos[] = (string) \laabs::newDateTime().": Validation du format par JHOVE 1.11";
            }

            if (($arr = get_object_vars($fileInfo)) && !empty($arr)) {
                file_put_contents(
                    $messageDir.DIRECTORY_SEPARATOR.$dataObjectId.'.info',
                    json_encode($fileInfo, \JSON_PRETTY_PRINT)
                );
            }
        }
    }

    protected function validateArchiveUnits($archiveUnitContainer, $message, $archivalAgreement)
    {
        foreach ($archiveUnitContainer as $archiveUnit) {
            $this->validateFiling($archiveUnit);
            
            if (isset($archiveUnit->profile)) {
                $this->archiveController->validateDescriptionModel($archiveUnit->description, $archiveUnit->profile);
            }

            if (isset($archiveUnit->management)) {
                $this->validateManagementMetadata($archiveUnit);
            }
        }

        if (!empty($archiveUnit->archiveUnit)) {
            $this->validateArchiveUnits($archiveUnit);
        }
    }

    protected function validateFiling($archiveUnit, $profile)
    {
        // No parent, check orgUnit can deposit with the profile
        if (empty($archiveUnit->filing->container)) {
            return $this->validateFilingActivity($archiveUnit);
        } 

        $this->validateFilingContainer($archiveUnit);

        $this->validateFilingContents($archiveUnit);
        
    }

    protected function validateFilingActivity($archiveUnit)
    {
        // Validate insertion
        $profile = null;
        if (isset($archiveUnit->profile)) {
            $profile = $archiveUnit->profile;
        }

        if ($this->orgController->checkProfileInOrgAccess($profile, $archive->originatorId)) {
            return;
        }

        throw new \core\Exception\BadRequestException("Invalid archive profile");
    }

    protected function validateFilingContainer($archiveUnit)
    {
        $containerArchive = $archiveController->read($archiveUnit->filing->container);      

        // Check level in file plan
        if ($containerArchive->fileplanLevel == 'item') {
            throw new \core\Exception\BadRequestException("Parent archive is an item and can not contain items.");
        }

        // No profile on parent, accept any profile
        if (empty($containerArchive->archivalProfileReference)) {
            return;
        }

        // Load parent archive profile
        if (!isset($this->archivalProfiles[$containerArchive->archivalProfileReference])) {
            $parentArchivalProfile = $this->archivalProfileController->getByReference($containerArchive->archivalProfileReference);
            $this->archivalProfiles[$containerArchive->archivalProfileReference] = $parentArchivalProfile;
        } else {
            $parentArchivalProfile = $this->archivalProfiles[$containerArchive->archivalProfileReference];
        }

        // No profile : check parent profile accepts archives without profile
        if (!isset($archiveUnit->profile) && $parentArchivalProfile->acceptArchiveWithoutProfile) {
            return;
        }

        // Profile on content : check profile is accepted
        foreach ($parentArchivalProfile->containedProfiles as $containedProfile) {
            if ($containedProfile->reference == $archiveUnit->profile) {
                return;
            }
        }

        throw new \core\Exception\BadRequestException("Invalid archive profile");
    }

    protected function validateFilingContents($archiveUnit)
    {
        if (isset($archiveUnit->filing->level) && $archiveUnit->filing->level == 'item' && count($archiveUnit->archiveUnits) > 0) {
            throw new \core\Exception\BadRequestException("Invalid contained archiveUnit profile %s", 400);
        }

        if (isset($archiveUnit->profile)) {
            if (!isset($this->archivalProfiles[$archiveUnit->profile])) {
                $this->archivalProfiles[$archiveUnit->profile] = $this->archivalProfileController->getByReference($archiveUnit->profile);
            }

            $archivalProfile = $this->archivalProfiles[$archiveUnit->profile];
            $containedProfiles = [];
            foreach ($archivalProfile->containedProfiles as $containedProfile) {
                $containedProfiles[] = $containedProfile->reference;
            }

            foreach ($archiveUnit->archiveUnits as $containedArchiveUnit) {
                if (empty($containedArchiveUnit->profile)) {
                    if (!$archivalprofile->acceptArchiveWithoutProfile) {
                        throw new \core\Exception\BadRequestException("Invalid contained archiveUnit profile %s", 400, null, $containedArchiveUnit->profile);
                    }
                } elseif (!in_array($containedArchiveUnit->profile, $containedProfiles)) {
                    throw new \core\Exception\BadRequestException("Invalid contained archiveUnit profile %s", 400, null, $containedArchiveUnit->profile);
                }
            }
        }
    }

    protected function validateArchiveDescriptionObject($archive)
    {
        if (empty($archiveUnit->profile)) {
            return;
        }

        $archivalProfile = $this->archivalProfileController->getByReference($archiveUnit->profile);

        if (!empty($archivalProfile->descriptionClass)) {
            $archive->descriptionObject = \laabs::castObject($archive->descriptionObject, $archivalProfile->descriptionClass);

            $this->validateDescriptionClass($archive->descriptionObject, $archivalProfile);
        } else {
            $this->validateDescriptionModel($archive->descriptionObject, $archivalProfile);
        }
    }

    protected function validateDescriptionClass($object, $archivalProfile)
    {
        if (\laabs::getClass($object)->getName() != $archivalProfile->descriptionClass) {
            throw new \bundle\recordsManagement\Exception\archiveDoesNotMatchProfileException('The description class does not match with the archival profile.');
        }

        foreach ($archivalProfile->archiveDescription as $description) {
            $fieldName = explode(LAABS_URI_SEPARATOR, $description->fieldName);
            $propertiesList = array($object);

            foreach ($fieldName as $name) {
                $newPropertiesList = array();
                foreach ($propertiesList as $propertyValue) {
                    if (isset($propertyValue->{$name})) {
                        if (is_array($propertyValue->{$name})) {
                            foreach ($propertyValue->{$name} as $value) {
                                $newPropertiesList[] = $value;
                            }
                        } else {
                            $newPropertiesList[] = $propertyValue->{$name};
                        }
                    } else {
                        $newPropertiesList[] = null;
                    }
                }
                $propertiesList = $newPropertiesList;
            }

            foreach ($propertiesList as $propertyValue) {
                if ($description->required && $propertyValue == null) {
                    throw new \core\Exception\BadRequestException('The description class does not match with the archival profile.');
                }
            }
        }
    }

    protected function validateDescriptionModel($object, $archivalProfile)
    {
        $names = [];

        foreach ($archivalProfile->archiveDescription as $archiveDescription) {
            $name = $archiveDescription->fieldName;
            $names[] = $name;
            $value = null;
            if (isset($object->{$name})) {
                $value = $object->{$name};
            }

            $this->validateDescriptionMetadata($value, $archiveDescription);
        }

        foreach ($object as $name => $value) {
            if (!in_array($name, $names) && !$archivalProfile->acceptUserIndex) {
                throw new \core\Exception\BadRequestException('Metadata %1$s is not allowed', 400, null, [$name]);
            }
        }
    }

    protected function validateDescriptionMetadata($value, $archiveDescription)
    {
        if (is_null($value)) {
            if ($archiveDescription->required) {
                throw new \core\Exception\BadRequestException('Null value not allowed for metadata %1$s', 400, null, [$archiveDescription->fieldName]);
            }

            return;
        }

        $descriptionField = $archiveDescription->descriptionField;

        $type = $descriptionField->type;
        switch ($type) {
            case 'name':
                if (!empty($descriptionField->enumeration) && !in_array($value, $descriptionField->enumeration)) {
                    throw new \core\Exception\BadRequestException('Forbidden value for metadata %1$s', 400, null, [$archiveDescription->fieldName]);
                }
                break;

            case 'text':
                break;

            case 'number':
                if (!is_int($value) && !is_float($value)) {
                    throw new \core\Exception\BadRequestException('Invalid value for metadata %1$s', 400, null, [$archiveDescription->fieldName]);
                }
                break;

            case 'boolean':
                if (!is_bool($value) && !in_array($value, [0, 1])) {
                    throw new \core\Exception\BadRequestException('Invalid value for metadata %1$s', 400, null, [$archiveDescription->fieldName]);
                }
                break;

            case 'date':
                if (!is_string($value)) {
                    throw new \core\Exception\BadRequestException('Invalid value for metadata %1$s', 400, null, [$archiveDescription->fieldName]);
                }
                break;
        }
    }

    protected function validateManagementMetadata($administration)
    {
        if (isset($administration->appraisalRule->code) && !$this->retentionRuleController->read($administration->appraisalRule->code)) {
            throw new \core\Exception\NotFoundException("The retention rule not found");
        }

        if (isset($administration->accessRule->code) && !$this->accessRuleControler->edit($administration->accessRule->code)) {
            throw new \core\Exception\NotFoundException("The access rule not found");
        }
    }

    /**
     * Process the archive transfer
     * @param mixed $message The message object or the message identifier
     *
     * @return string The reply message identifier
     */
    public function process($message)
    {
        $this->processedArchives = [];

        foreach ($message->object->descriptiveMetadata as $key => $archiveUnit) {
            $archive = $this->processArchiveUnit($archiveUnit, $message);

            $this->processedArchives[] = $archive;
        }

        return [$this->processedArchives, $this->processedRelationships];
    }

    protected function processArchiveUnit($archiveUnit, $message)
    {
        $archive = \laabs::newInstance('recordsManagement/archive');

        $archiveTransfer = $message->object;

        if (isset($archiveUnit->identifier)) {
            $archive->originatorArchiveId = $archiveUnit->identifier;
        }

        if (isset($archiveUnit->displayName)) {
            $archive->archiveName = $archiveUnit->displayName;
        }

        if (isset($archiveUnit->refDate)) {
            $archive->originatingDate = $archiveUnit->refDate;
        }

        if (isset($archiveUnit->profile)) {
            $archive->archivalProfileReference = $archiveUnit->profile;
        } elseif (isset($archiveTransfer->dataObjectPackage->managementMetadata->archivalProfile)) {
            $archive->archivalProfileReference = $archiveTransfer->dataObjectPackage->managementMetadata->archivalProfile;
        }

        if (isset($archiveUnit->description)) {
            $archive->descriptionObject = $archiveUnit->description;
        }

        $archive->depositorOrgRegNumber = $archiveTransfer->transferringAgency->identifier;
        $archive->archiverOrgRegNumber = $archiveTransfer->archivalAgency->identifier;
        
        if (isset($archiveUnit->filing->activity)) {
            $archive->originatorOrgRegNumber = $archiveUnit->filing->activity;
        } else {
            $archive->originatorOrgRegNumber = $archive->depositorOrgRegNumber;
        }
        
        $this->processManagementMetadata($archive, $archiveUnit, $archiveTransfer);

        $this->processFiling($archive, $archiveUnit);

        $this->archiveController->completeMetadata($archive);

        if (!empty($archiveUnit->dataObjectReferences)) {
            $this->processBinaryDataObjects($archive, $archiveUnit->dataObjectReferences, $message);
        }
        
        if (!empty($archiveUnit->log)) {
            $this->processLifeCycleEvents($archive, $archiveUnit);
        }

        if (!empty($archiveUnit->archiveUnitReferences)) {
            $this->processRelationships($archive, $archiveUnit->archiveUnitReferences, $archiveTransfer);
        }
        
        if (!empty($archiveUnit->archiveUnit)) {
            foreach ($archiveUnit->archiveUnit as $key => $subArchiveUnit) {
                $subArchive = $this->processArchiveUnit($subArchiveUnit, $message);
                $subArchive->parentArchiveId = $archive->archiveId;

                $this->processedArchives[] = $subArchive;
            }
        }
        
        return $archive;
    }

    protected function processManagementMetadata($archive, $archiveUnit, $message)
    {
        if (isset($archiveUnit->management->accessRule)) {
            $this->processAccessRule($archive, $archiveUnit->management->accessRule);
        } elseif (isset($message->dataObjectPackage->managementMetadata->accessRule)) {
            $this->processAccessRule($archive, $message->dataObjectPackage->managementMetadata->accessRule);
        }

        if (isset($archiveUnit->management->appraisalRule)) {
            $this->processAppraisalRule($archive, $archiveUnit->management->appraisalRule);
        } elseif (isset($message->dataObjectPackage->managementMetadata->appraisalRule)) {
            $this->processAppraisalRule($archive, $message->dataObjectPackage->managementMetadata->appraisalRule);
        }

        if (isset($archiveUnit->management->classificationRule)) {
            $this->processClassificationRule($archive, $archiveUnit->management->classificationRule);
        } elseif (isset($message->dataObjectPackage->managementMetadata->classificationRule)) {
            $this->processClassificationRule($archive, $message->dataObjectPackage->managementMetadata->classificationRule);
        }
    }

    protected function processAccessRule($archive, $accessRule)
    {
        if (!empty($accessRule->code)) {
            $archive->accessRuleCode = $accessRule->code;
        }

        if (!empty($accessRule->duration)) {
            $archive->accessRuleDuration = $accessRule->duration;
        }

        if (!empty($accessRule->startDate)) {
            $archive->accessRuleStartDate = $accessRule->startDate;
        }
    }

    protected function processAppraisalRule($archive, $appraisalRule)
    {
        if (!empty($appraisalRule->code)) {
            $archive->retentionRuleCode = $appraisalRule->code;
        }

        if (!empty($appraisalRule->duration)) {
            $archive->retentionDuration = $appraisalRule->duration;
        }

        if (!empty($appraisalRule->startDate)) {
            $archive->retentionStartDate = $appraisalRule->startDate;
        }

        if (!empty($appraisalRule->finalDisposition)) {
            $archive->finalDisposition = $appraisalRule->finalDisposition;
        }
    }

    protected function processClassificationRule($archive, $classificationRule)
    {
        if (!empty($classificationRule->code)) {
            $archive->classificationRuleCode = $classificationRule->code;
        }

        if (!empty($classificationRule->duration)) {
            $archive->classificationDuration = $classificationRule->duration;
        }

        if (!empty($classificationRule->startDate)) {
            $archive->classificationStartDate = $classificationRule->startDate;
        }

        if (!empty($classificationRule->owner)) {
            $archive->classificationOnwer = $classificationRule->owner->identifier;
        }

        if (!empty($classificationRule->level)) {
            $archive->classificationLevel = $classificationRule->level;
        }
    }

    
    protected function processFiling($archive, $archiveUnit)
    {
        if (isset($archiveUnit->filing->folder)) {
            $archive->filePlanPosition = $archiveUnit->filing->folder;
        }
    }

    protected function processBinaryDataObjects($archive, $dataObjectReferences, $message)
    {
        foreach ($dataObjectReferences as $dataObjectId) {
            $binaryDataObject = $message->object->dataObjectPackage->binaryDataObjects->{$dataObjectId};

            $digitalResource = \laabs::newInstance("digitalResource/digitalResource");
            $digitalResource->archiveId = $archive->archiveId;
            $digitalResource->resId = \laabs::newId();
            $digitalResource->size = $binaryDataObject->size;
            
            if (isset($binaryDataObject->format->puid)) {
                $digitalResource->puid = $binaryDataObject->format->puid;
            }

            if (isset($binaryDataObject->format->mimetype)) {
                $digitalResource->mimetype = $binaryDataObject->format->mimetype;
            }

            if (isset($binaryDataObject->messageDigest)) {
                $digitalResource->hash = $binaryDataObject->messageDigest->content;
                $digitalResource->hashAlgorithm = $binaryDataObject->messageDigest->algorithm;
            }

            if (isset($binaryDataObject->fileInformation->filename)) {
                $digitalResource->fileName = $binaryDataObject->fileInformation->filename;
            } elseif (isset($binaryDataObject->attachment->filename)) {
                $digitalResource->fileName = basename($binaryDataObject->attachment->filename);
            }

            if (isset($binaryDataObject->attachment->content)) {
                $digitalResource->setContents(base64_decode($binaryDataObject->attachment->content));
            } elseif (isset($binaryDataObject->attachment->filename)) {
                $digitalResource->setHandler(fopen(dirname($message->path).DIRECTORY_SEPARATOR.$binaryDataObject->attachment->filename, 'r'));
            } elseif (isset($binaryDataObject->attachment->uri)) {
                $digitalResource->setHandler(fopen($binaryDataObject->attachment->uri, 'r'));
            }
            
            $archive->digitalResources[] = $digitalResource;
        }
    }

    protected function processLifeCycleEvents($archive, $archiveUnit)
    {
        if (empty($archiveUnit->lifeCycleEvents)) {
            return;
        }

        foreach ($archiveUnit->lifeCycleEvents as $event) {
            $newEvent = \laabs::newInstance("lifeCycle/event");
            $newEvent->eventType = $event->type;
            $newEvent->objectClass = "recordsManagement/archive";
            $newEvent->objectId = $archive->archiveId;
            $newEvent->description = $event->description;
            $newEvent->eventInfo = $event->eventInfo;

            $archive->lifeCycleEvents[] = $newEvent;
        }
    }

    protected function processRelationships($archive, $archiveUnit, $message)
    {
        foreach ($archiveUnit->archiveUnitReferences as $archiveUnitReference) {
            $archiveRelationship = \laabs::newInstance("recordsManagement/archiveRelationship");
            $archiveRelationship->archiveId = $archiveUnit->archiveId;
            $archiveRelationship->relatedArchiveId = $archiveUnitReference->refId;
            $archiveRelationship->typeCode = $archiveUnitReference->type;
            //$archiveRelationship->description = $archiveUnitReference->description;

            $this->processedRelationships[] = $archiveRelationship;
        }
    }
}
