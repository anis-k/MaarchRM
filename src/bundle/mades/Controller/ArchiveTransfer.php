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
class ArchiveTransfer extends abstractMessage implements \bundle\medona\Controller\ArchiveTransferInterface
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
    protected $retentionRuleController;
    protected $accessRuleController;

    public function __construct()
    {
        $this->orgController = \laabs::newController('organization/organization');
        $this->archiveController = \laabs::newController('recordsManagement/archive');
        $this->archivalProfileController = \laabs::newController('recordsManagement/archivalProfile');
        $this->retentionRuleController = \laabs::newController('recordsManagement/retentionRule');
        $this->accessRuleController = \laabs::newController('recordsManagement/accessRule');
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
            $message->dataObjectCount += count(
                get_object_vars($archiveTransfer->dataObjectPackage->binaryDataObjects)
            );
            $this->receiveAttachments($message);
        }
        if (isset($archiveTransfer->dataObjectPackage->physicalDataObject)) {
            $message->dataObjectCount += count(
                get_object_vars($archiveTransfer->dataObjectPackage->physicalDataObjects)
            );
        }

        return $message;
    }

    protected function receiveAttachments($message)
    {
        $this->validateReference(
            $message->object->dataObjectPackage->descriptiveMetadata,
            $message->object->dataObjectPackage->binaryDataObjects
        );
        
        $dirname = dirname($message->path);
        
        $messageFiles = [$message->path];
        foreach ($message->object->dataObjectPackage->binaryDataObjects as $dataObjectId => $binaryDataObject) {
            $message->size += (integer) $binaryDataObject->size;

            if (!isset($binaryDataObject->attachment)) {
                $this->sendError("211", "Le document identifié par le nom '$dataObjectId' n'a pas été transmis.");

                continue;
            }

            $attachment = $binaryDataObject->attachment;

            if (isset($attachment->filename)) {
                $filename = $dirname.DIRECTORY_SEPARATOR.$attachment->filename;
                if (!is_file($filename)) {
                    $this->sendError(
                        "211",
                        "Le document identifié par le nom '$attachment->filename' n'a pas été trouvé."
                    );

                    continue;
                }

                if (filesize($filename) == 0) {
                    $this->sendError("211", "Le document identifié par le nom '$attachment->filename' est vide.");

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

                if (strlen($contents) == 0) {
                    $this->sendError("211", "Le contenu du document n'a pas pu être décodé.");

                    continue;
                }

                $filename = $dirname.DIRECTORY_SEPARATOR.$dataObjectId;
                file_put_contents($filename, $contents);
            }

            $messageFiles[] = $filename;

            // Validate hash
            $messageDigest = $binaryDataObject->messageDigest;
            if (strtolower($messageDigest->content) != strtolower(hash($messageDigest->algorithm, $contents))) {
                $this->sendError(
                    "207",
                    "L'empreinte numérique du document '".basename($filename)."' ne correspond pas à celle transmise."
                );

                continue;
            }
        }

        $receivedFiles = glob($dirname.DIRECTORY_SEPARATOR."*.*");

        // Check all files received are part of the message
        foreach ($receivedFiles as $receivedFile) {
            if (!in_array($receivedFile, $messageFiles)) {
                $this->sendError(
                    "101",
                    "Le fichier '".basename($receivedFile)."' n'est pas référencé dans le message."
                );
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
                $this->validateReference($archiveUnit->archiveUnits, $binaryDataObjects);
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
        
                $this->validateOriginators(
                    $message->object->dataObjectPackage->descriptiveMetadata,
                    $archivalAgreement
                );
            }

            if ($archivalAgreement->signed && !isset($message->object->signature)) {
                $this->sendError("309");
            }

            /*if (isset($archivalAgreement->archivalProfileReference)
                || isset($message->object->dataObjectPackage->managementMetadata->archivalProfile)) {
                $this->validateProfile($message, $archivalAgreement);
            }*/
        } else {
            $this->validateOriginators(
                $message->object->dataObjectPackage->descriptiveMetadata,
                $archivalAgreement
            );
        }

        $this->validateDataObjects($message, $archivalAgreement);
        $this->validateArchiveUnits($message->object->dataObjectPackage->descriptiveMetadata, $archivalAgreement);
      
        return true;
    }

    protected function validateOriginators($archiveUnitContainer, $archivalAgreement)
    {
        foreach ($archiveUnitContainer as $id => $archiveUnit) {
            if (isset($archiveUnit->filing->activity) && !isset($knownOrgUnits[$archiveUnit->filing->activity])) {
                $archiveOriginator = $archiveUnit->filing->activity;
                try {
                    $this->knownOrgUnits[$archiveOriginator] =
                    $orgUnit = $this->orgController->getOrgByRegNumber($archiveOriginator);
                } catch (\Exception $e) {
                    $this->sendError(
                        "200",
                        "Le producteur de l'archive identifié par '$archiveOriginator' n'est pas référencé dans le système."
                    );
                    continue;
                }
                
                if (!in_array('originator', (array) $orgUnit->orgRoleCodes)) {
                    $this->sendError(
                        "302",
                        "Le service identifié par '$archiveOriginator' n'est pas référencé comme producteur dans le système."
                    );
                
                    continue;
                }

                if (!is_null($archivalAgreement) && !in_array((string) $orgUnit->orgId, (array) $archivalAgreement->originatorOrgIds)) {
                    $this->sendError(
                        "302",
                        "Le producteur de l'archive identifié par '$archiveOriginator' n'est pas indiqué dans l'accord de versement."
                    );
                }

                if (!$orgUnit->enabled) {
                    $this->sendError("302", "Le service producteur '$archiveOriginator' est désactivé : veuillez le réactiver pour y verser des archives");
                }
            }

            if (!empty($archiveUnit->archiveUnits)) {
                $this->validateOriginators($archiveUnit, $archivalAgreement);
            }
        }
    }

    protected function validateDataObjects($message, $archivalAgreement)
    {
        $serviceLevelController = \laabs::newController("recordsManagement/serviceLevel");

        if (isset($message->object->dataObjectPackage->managementMetadata->serviceLevel)) {
            $serviceLevelReference = $message->object->dataObjectPackage->managementMetadata->serviceLevel->value;
            $serviceLevel = $serviceLevelController->getByReference($serviceLevelReference);
        } elseif (isset($archivalAgreement)) {
            $serviceLevelReference = $archivalAgreement->serviceLevelReference;
            $serviceLevel = $serviceLevelController->getByReference($serviceLevelReference);
        } else {
            $serviceLevel = $serviceLevelController->readDefault();
        }

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
                    $this->sendError(
                        "205",
                        "Le format du document '".basename($filepath)."' n'a pas pu être déterminé"
                    );
                } else {
                    $puid = $format->puid;
                    $fileInfo->format = $format;
                }
            }

            // Validate format is allowed
            if (count($allowedFormats) && isset($puid) && !in_array($puid, $allowedFormats)) {
                $this->sendError(
                    "307",
                    "Le format du document '".basename($filepath)."' ".$puid." n'est pas autorisé par l'accord de versement."
                );
            }

            // Validate format
            if (strpos($serviceLevel->control, 'formatValidation') !== false) {
                $validation = $formatController->validateFormat($filepath);
                if (!$validation !== true && is_array($validation)) {
                    $this->sendError(
                        "307",
                        "Le format du document '".basename($filepath)."' n'est pas valide : ".implode(', ', $validation)
                    );
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

    protected function validateArchiveUnits($archiveUnitContainer, $archivalAgreement)
    {
        foreach ($archiveUnitContainer as $archiveUnit) {
            $this->validateFiling($archiveUnit);
            
            if (isset($archiveUnit->profile)) {
                $archivalProfile = $this->useArchivalProfile($archiveUnit->profile);

                $this->archiveController->validateDescriptionModel($archiveUnit->description, $archivalProfile);
            }

            if (isset($archiveUnit->management)) {
                $this->validateManagementMetadata($archiveUnit->management);
            }
        }

        if (!empty($archiveUnit->archiveUnits)) {
            $this->validateArchiveUnits($archiveUnit->archiveUnits);
        }
    }

    protected function validateFiling($archiveUnit)
    {
        $this->validateFilingActivity($archiveUnit);

        if (!empty($archiveUnit->filing->container)) {
            $this->validateFilingContainer($archiveUnit);
        }

        if (isset($archiveUnit->archiveUnits) && count($archiveUnit->archiveUnits) > 0) {
            $this->validateFilingContents($archiveUnit);
        }
    }

    protected function validateFilingActivity($archiveUnit)
    {
        // Validate insertion
        $profile = null;
        if (isset($archiveUnit->profile)) {
            $profile = $archiveUnit->profile;
        }

        if ($this->orgController->checkProfileInOrgAccess($profile, $archiveUnit->filing->activity)) {
            return;
        }

        throw new \core\Exception\BadRequestException(
            "The activity %s can not produce this type of archive units",
            400,
            null,
            $archiveUnit->filing->activity
        );
    }

    protected function useArchivalProfile($archivalProfileReference)
    {
        // Load parent archive profile
        if (!isset($this->archivalProfiles[$archivalProfileReference])) {
            $archivalProfile = $this->archivalProfileController->getByReference($archivalProfileReference);
            $this->archivalProfiles[$archivalProfileReference] = $archivalProfile;
        }

        return $this->archivalProfiles[$archivalProfileReference];
    }

    protected function validateFilingContainer($archiveUnit)
    {
        $containerArchive = $this->archiveController->read($archiveUnit->filing->container);

        // Check level in file plan
        if ($containerArchive->fileplanLevel == 'item') {
            throw new \core\Exception\BadRequestException("Parent archive is an item and can not contain items.");
        }

        // No profile on parent, accept any profile
        if (empty($containerArchive->archivalProfileReference)) {
            return;
        }

        $parentArchivalProfile = $this->useArchivalProfile($containerArchive->archivalProfileReference);

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

        throw new \core\Exception\BadRequestException("Archive unit can not be added in this container.");
    }

    protected function validateFilingContents($archiveUnit)
    {
        if (isset($archiveUnit->filing->level) && $archiveUnit->filing->level == 'item') {
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
                        throw new \core\Exception\BadRequestException(
                            "Invalid contained archiveUnit profile %s",
                            400,
                            null,
                            $containedArchiveUnit->profile
                        );
                    }
                } elseif (!in_array($containedArchiveUnit->profile, $containedProfiles)) {
                    throw new \core\Exception\BadRequestException(
                        "Invalid contained archiveUnit profile %s",
                        400,
                        null,
                        $containedArchiveUnit->profile
                    );
                }
            }
        }
    }

    protected function validateManagementMetadata($administration)
    {
        if (isset($administration->appraisalRule->code)) {
            try {
                $this->retentionRuleController->read($administration->appraisalRule->code);
            } catch (\Exception $exception) {
                throw new \core\Exception\NotFoundException(
                    "The retention rule %s not found",
                    400,
                    null,
                    $administration->appraisalRule->code
                );
            }
        }

        if (isset($administration->accessRule->code)) {
            try {
                $this->accessRuleController->index($administration->accessRule->code);
            } catch (\Exception $exception) {
                throw new \core\Exception\NotFoundException(
                    "The access rule %s not found",
                    400,
                    null,
                    $administration->accessRule->code
                );
            }
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

        foreach ($message->object->dataObjectPackage->descriptiveMetadata as $key => $archiveUnit) {
            $archive = $this->processArchiveUnit($archiveUnit, $message);

            $this->processedArchives[] = $archive;
        }

        return [
            $this->processedArchives,
            $this->processedRelationships
        ];
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
            $archive->archivalProfileReference =
                $archiveTransfer->dataObjectPackage->managementMetadata->archivalProfile;
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
            $this->processClassificationRule(
                $archive,
                $message->dataObjectPackage->managementMetadata->classificationRule
            );
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
        if (isset($archiveUnit->filing->container)) {
            $archive->parentArchiveId = $archiveUnit->filing->container;
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
                $handler = fopen(dirname($message->path).DIRECTORY_SEPARATOR.$binaryDataObject->attachment->filename, 'r');
                $digitalResource->setHandler($handler);
            } elseif (isset($binaryDataObject->attachment->uri)) {
                $handler = fopen($binaryDataObject->attachment->uri, 'r');
                $digitalResource->setHandler($handler);
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
