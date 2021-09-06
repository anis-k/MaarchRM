<?php

/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of bundle mades.
 *
 * Bundle mades is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle mades is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle mades.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\mades\Controller;

/**
 * Class abstractMessage
 *
 * @package mades
 *
 */
abstract class abstractMessage
{
    protected $currentDigitalResources;

    protected $metadataBindings;

    protected function send($message)
    {
        $this->currentDigitalResources = [];

        $madesMessage = new \stdClass();

        $message->object = $madesMessage;

        $madesMessage->messageIdentifier = (string) $message->messageId;

        $madesMessage->date = $message->date;

        $madesMessage->replyCode = $message->replyCode;

        $madesMessage->comment = $message->comment;

        $madesMessage->version = "1.0";

        return $madesMessage;
    }

    protected function sendError($code, $message = false)
    {
        if ($message) {
            array_push($this->errors, new \core\Error($message, null, $code));
        } else {
            array_push($this->errors, new \core\Error($this->getReplyMessage($code), null, $code));
        }

        if ($this->replyCode == null) {
            $this->replyCode = $code;
        }
    }

    protected function getReplyMessage($code)
    {
        switch ((string) $code) {
            case "000":
                $name = "OK";
                break;
            case "001":
                $name = "OK (consulter les commentaires pour information)";
                break;
            case "002":
                $name = "OK (Demande aux autorités de contrôle effectuée)";
                break;

            case "101":
                $name = "Message mal formé.";
                break;
            case "102":
                $name = "Système momentanément indisponible.";
                break;
            case "103":
                $name = "Message déjà reçu.";
                break;
            case "104":
                $name = "Message en erreur.";
                break;

            case "200":
                $name = "Service producteur non reconnu.";
                break;
            case "201":
                $name = "Service d'archive non reconnu.";
                break;
            case "202":
                $name = "Service versant non reconnu.";
                break;
            case "203":
                $name = "Dépôt non conforme au profil de données.";
                break;
            case "204":
                $name = "Format de document non géré.";
                break;
            case "205":
                $name = "Format de document non conforme au format déclaré.";
                break;
            case "206":
                $name = "Signature du message invalide.";
                break;
            case "207":
                $name = "Empreinte(s) invalide(s).";
                break;
            case "208":
                $name = "Archive indisponible. Délai de communication non écoulé.";
                break;
            case "209":
                $name = "Archive absente (élimination, restitution, transfert)";
                break;
            case "210":
                $name = "Archive inconnue";
                break;
            case "211":
                $name = "Pièce attachée absente.";
                break;
            case "212":
                $name = "Dérogation refusée.";
                break;
            case "213":
                $name = "Identifiant de document incorrect";
                break;

            case "300":
                $name = "Convention invalide.";
                break;
            case "301":
                $name = "Dépôt non conforme à la convention. Quota des versements dépassé.";
                break;
            case "302":
                $name = "Dépôt non conforme à la convention. Identifiant du producteur non conforme.";
                break;
            case "303":
                $name = "Dépôt non conforme à la convention. Identifiant du service versant non conforme.";
                break;
            case "304":
                $name = "Dépôt non conforme à la convention. Identifiant du service d'archives non conforme.";
                break;
            case "305":
                $name = "Dépôt non conforme à la convention. Signature(s) de document(s) absente(s).";
                break;
            case "306":
                $name = "Dépôt non conforme à la convention. Volume non conforme.";
                break;
            case "307":
                $name = "Dépôt non conforme à la convention. Format non conforme.";
                break;
            case "308":
                $name = "Dépôt non conforme à la convention. Empreinte(s) non transmise(s).";
                break;
            case "309":
                $name = "Dépôt non conforme à la convention. Absence de signature du message.";
                break;
            case "310":
                $name = "Dépôt non conforme à la convention. Signature(s) de document(s) non valide(s).";
                break;
            case "311":
                $name = "Dépôt non conforme à la convention. Signature(s) de document(s) non vérifiée(s).";
                break;
            case "312":
                $name = "Dépôt non conforme à la convention. Dates de début ou de fin non respectées.";
                break;

            case "400":
                $name = "Demande rejetée.";
                break;
            case "404":
                $name = "Message non trouvé.";
                break;

            default:
                $name = null;
        }

        return $name;
    }

    protected function sendOrganization($orgOrganization)
    {
        $organization = new \StdClass();
        $organization->identifier = $orgOrganization->registrationNumber;
        $organization->name = $orgOrganization->orgName;

        if (isset($orgOrganization->address)) {
            foreach ($orgOrganization->address as $address) {
                $organization->address[] = $this->sendAddress($address);
            }
        }

        if (isset($orgOrganization->communication)) {
            foreach ($orgOrganization->communication as $communication) {
                $organization->communication[] = $this->sendCommunication($communication);
            }
        }

        if (isset($orgOrganization->contact)) {
            foreach ($orgOrganization->contact as $contact) {
                $organization->contact[] = $this->sendContact($contact);
            }
        }

        return $organization;
    }

    protected function sendAddress($orgAddress)
    {
        $address = \laabs::newInstance('contact/address');
        $address->id = (string) $orgAddress->addressId;
        $address->blockName = $orgAddress->block;
        $address->buildingName = $orgAddress->building;
        $address->buildingNumber = $orgAddress->number;
        $address->cityName = $orgAddress->city;
        $address->citySubDivisionName = $orgAddress->citySubDivision;
        $address->country = $orgAddress->country;
        $address->floorIdentification = $orgAddress->floor;
        $address->postCode = $orgAddress->postCode;
        $address->postOfficeBox = $orgAddress->postBox;
        $address->roomIdentification = $orgAddress->room;
        $address->streetName = $orgAddress->street;

        return $address;
    }

    protected function sendCommunication($orgCommunication)
    {
        $communication = \laabs::newInstance('contact/communication');
        $communication->id = (string) $orgCommunication->communicationId;
        $communication->channel = $orgCommunication->comMeanCode;

        switch ($orgCommunication->comMeanCode) {
            case 'EM':
            case 'FTP':
                $communication->URIID = $orgCommunication->value;
                break;

            default:
                $communication->completeNumber = $orgCommunication->value;
        }

        return $communication;
    }

    protected function sendContact($orgContact)
    {
        $contact = \laabs::newInstance('contact/contact');
        $contact->id = (string) $orgContact->contactId;
        $contact->departmentName = $orgContact->service;
        $contact->identification = $orgContact->contactId;
        $contact->personName = $orgContact->displayName;
        $contact->responsibility = $orgContact->function;

        if (isset($orgContact->address)) {
            foreach ($orgContact->address as $address) {
                $contact->address[] = $this->sendAddress($address);
            }
        }

        if (isset($orgContact->communication)) {
            foreach ($orgContact->communication as $communication) {
                $contact->communication[] = $this->sendCommunication($communication);
            }
        }

        return $contact;
    }

    protected function sendUnitIdentifiers($message)
    {
        if (isset($message->unitIdentifier)) {
            foreach ($message->unitIdentifier as $unitIdentifier) {
                $message->object->unitIdentifier[] = $unitIdentifier->objectId;
            }
        }
    }

    protected function sendReplyCode($message)
    {
        if (isset($message->replyCode)) {
            $message->object->replyCode = $message->replyCode;
        }
    }

    protected function sendDataObjectPackage($message, $withBinaries = false)
    {
        $message->object->dataObjectPackage = new \stdClass();
        $message->object->dataObjectPackage->descriptiveMetadata = new \stdClass();
        $message->object->dataObjectPackage->binaryDataObjects = new \stdClass();

        if (is_array($message->archive)) {
            foreach ($message->archive as $archive) {
                $message->object->dataObjectPackage->descriptiveMetadata->{$archive->archiveId} =
                    $this->sendArchiveMetadata($archive);
            }
        }
        if ($withBinaries) {
            $this->sendArchiveBinaries($message);
        }
    }

    protected function sendArchiveMetadata($archive)
    {
        $this->metadataBindings = [
            'archiveName' => 'displayName',
            'originatingDate' => 'refDate',
            'archivalProfileReference' => 'profile',
            'description' => 'description',

            'fileplanLevel' => 'filing->level',
            'filePlanPosition' => 'filing->folder',
            'originatorOrgRegNumber' => 'filing->activity',
            'originatorOwnerOrgRegNumber' => 'filing->originator',
            'parentArchiveId' => 'filing->container',

            'status' => 'management->preservationStatus',
            'processingStatus' => 'management->processingStatus',
            'serviceLevelReference' => 'management->serviceLevel',

            'retentionRuleCode' => 'management->appraisalRule->code',
            'retentionStartDate' => 'management->appraisalRule->startDate',
            'finalDisposition' => 'management->appraisalRule->finalDisposition',
            'retentionDuration' => 'management->appraisalRule->duration',
            'retentionRuleStatus' => 'management->appraisalRule->status',
            'disposalDate' => 'management->appraisalRule->disposalDueDate',

            'accessRuleCode' => 'management->accessRule->code',
            'accessRuleStartDate' => 'management->accessRule->startDate',
            'accessRuleDuration' => 'management->accessRule->duration',
            'accessRuleStatus' => 'management->accessRule->status',
            'accessRuleComDate' => 'management->accessRule->disclosureDueDate',

            'classificationRuleCode' => 'management->classificationRule->code',
            'classificationRuleStartDate' => 'management->classificationRule->startDate',
            'classificationRuleDuration' => 'management->classificationRule->duration',
            'classificationLevel' => 'management->classificationRule->level',
            'classificationOwner' => 'management->classificationRule->owner',
            'classificationAudience' => 'management->classificationRule->audience',
            'classificationStatus' => 'management->classificationRule->status',
            'classificationReassessingDate' => 'management->classificationRule->reassessingDate',
            'classificationEndDate' => 'management->classificationRule->releaseDueDate',

            'depositDate' => 'control->creationDate',
            'lastModificationDate' => 'control->lastModificationDate'
        ];

        $archiveUnit = $this->getObjectFromBindingArray($archive, $this->metadataBindings);

        if (isset($archive->relationships)
            && is_array($archive->relationships)
            && (
                !empty($archive->relationships->parentRelationships)
                || !empty($archive->relationships->childrenRelationships)
            )) {
            $archiveUnit->relationships = [];
            foreach ($archive->relationships->parentRelationships as $parentRelationship) {
                $relationship = new \stdClass();
                $relationship->refId = $parentRelationship->relatedArchiveId;
                $relationship->type = $parentRelationship->typeCode;
                $relationship->description = $parentRelationship->description;
                $archiveUnit->relationships[] = $relationship;
            }
            foreach ($archive->relationships->childrenRelationships as $childrenRelationship) {
                $relationship = new \stdClass();
                $relationship->refId = $childrenRelationship->archiveId;
                $relationship->type = $childrenRelationship->typeCode;
                $relationship->description = $childrenRelationship->description;
                $archiveUnit->relationships[] = $relationship;
            }
        }

        foreach ($archive->digitalResources as $digitalResource) {
            $archiveUnit->dataObjectReferences[] = $digitalResource->resId;

            $this->currentDigitalResources[] = $digitalResource;
        }

        if (isset($archive->contents) && is_array($archive->contents)) {
            $archiveUnit->archiveUnits = new \stdClass();
            foreach ($archive->contents as $content) {
                $archiveUnit->archiveUnits->{$content->archiveId} = $this->sendArchiveMetadata($content);
            }
        }
        
        // $archiveUnit->security = new \stdClass();
        // $archiveUnit->security->user
        // $archiveUnit->security->group
        // $archiveUnit->security->org
        // $archiveUnit->security->accessControlList

        // $archiveUnit->control->lastUseDate = 
        // $archiveUnit->control->status = "active";

        // $archiveUnit->log = 

        return $archiveUnit;
    }

    protected function sendArchiveBinaries($message)
    {
        $dataObjectPackage = $message->object->dataObjectPackage;
        foreach ($this->currentDigitalResources as $digitalResource) {
            $dataObjectPackage->binaryDataObjects->{$digitalResource->resId} = $this->sendArchiveBinary($message, $digitalResource);
        }
    }

    protected function sendArchiveBinary($message, $digitalResource)
    {
        
        $binaryDataObject = new \stdClass();

        if (isset($digitalResource->resId)) {
            $binaryDataObject->attachment = new \stdClass();
            // $binaryDataObject->attachment->uri = $digitalResource->address[0]->path;
            if (isset($digitalResource->fileName)) {
                $binaryDataObject->attachment->filename = $digitalResource->fileName;
            } else {
                $binaryDataObject->attachment->filename = $digitalResource->resId;
            }
            
            $binaryDataObject->size = $digitalResource->size;
            $message->size += $binaryDataObject->size;
        }

        if (isset($digitalResource->mimetype)) {
            $binaryDataObject->format = new \stdClass();
            // $binaryDataObject->format->name =
            $binaryDataObject->format->mimeType = $digitalResource->mimetype;
            // $binaryDataObject->format->identifier = $digitalResource->puid;
            // $binaryDataObject->format->containerType =
        }

        if (isset($digitalResource->hash)) {
            $binaryDataObject->messageDigest = new \stdClass();
            // $binaryDataObject->messageDigest->uri =
            // $binaryDataObject->messageDigest->filename =
            $binaryDataObject->messageDigest->content = $digitalResource->hash;
            $binaryDataObject->messageDigest->algorithm = $digitalResource->hashAlgorithm;
        }

        if (isset($digitalResource->filename)) {
            $binaryDataObject->fileInformation = new \stdClass();
            $binaryDataObject->fileInformation->filename = $digitalResource->filename;
            // $binaryDataObject->fileInformation->application =
            // $binaryDataObject->fileInformation->creationDate =
            // $binaryDataObject->fileInformation->lastModificationDate =
        }

        // $binaryDataObject->technicalMetadata = new \stdClass();
        // $binaryDataObject->technicalMetadata->text =
        // $binaryDataObject->technicalMetadata->audio =
        // $binaryDataObject->technicalMetadata->video =
        // $binaryDataObject->technicalMetadata->image =
        // $binaryDataObject->technicalMetadata->document =
        // $binaryDataObject->technicalMetadata->{'3D'} =

        if (isset($digitalResource->relatedResId)) {
            $relationship = new \stdClass();
            $relationship->type = $digitalResource->relationshipType;
            $relationship->refId = $digitalResource->relatedResId;
            // $relationship->displayName =
            $binaryDataObject->relationships[] = $relationship;
        }

        return $binaryDataObject;
    }

    protected function sendRequest($authorizationRequestContent)
    {
        $request = new \stdClass();

        $request->authorisationReason = $authorizationRequestContent->authorizationReason;
        $request->requestDate = $authorizationRequestContent->requestDate;

        foreach ($authorizationRequestContent->unitIdentifier as $unitIdentifier) {
            $request->unitIdentifier[] = $unitIdentifier->objectId;
        }

        $request->requester = $this->sendOrganization($authorizationRequestContent->requester);

        return $request;
    }

    /**
     * Get an attachment resource from a message
     * @param mades/message $message      The message
     * @param string         $attachmentId The attachment identifier
     *
     * @return digitalResource/digitalResource The resource
     */
    public function getAttachment($message, $attachmentId)
    {
        $attachment = $this->findAttachment($attachmentId, $message->object->dataObjectPackage->binaryDataObjects);

        if (!$attachment) {
            return false;
        }

        $resourceController = \laabs::newController('digitalResource/digitalResource');

        switch (true) {
            case isset($attachment->filename):
                $messageDir = dirname($message->path);
                $filepath = $messageDir.DIRECTORY_SEPARATOR.$attachment->filename;
                $handler = fopen($filepath, 'r');
                $resource = $resourceController->createFromStream($handler, $attachment->filename);
                break;

            case isset($attachment->uri):
                $handler = fopen($filepath, 'r');
                $resource = $resourceController->createFromStream($handler);
                break;

            case isset($attachment->value):
                $contents = base64_decode($attachment->value);
                $resource = $resourceController->createFromContents($contents);
                break;

            default:
                return false;
        }

        return $resource;
    }

    protected function findAttachment($attachmentId, $binaryDataObjects)
    {
        foreach ($binaryDataObjects as $key => $binaryDataObject) {
            if ($key === $attachmentId) {
                return $binaryDataObject->attachment;
            }
        }
    }

    protected function sendJSON($message)
    {
        $this->messageDirectory = \laabs::configuration('medona')['messageDirectory'];

        $messageDir = $this->messageDirectory.DIRECTORY_SEPARATOR.$message->messageId;
        if (!is_dir($messageDir)) {
            mkdir($messageDir, 0775, true);
        }

        // Documents
        foreach ($this->currentDigitalResources as $digitalResource) {
            if ($digitalResource->fileName) {
                $filename = $digitalResource->fileName;
            } else {
                $filename = $digitalResource->resId;

                if (isset($digitalResource->fileExtension)) {
                    $filename .= ".".$digitalResource->fileExtension;
                }
            }
            $handler = fopen($messageDir.DIRECTORY_SEPARATOR.$filename, 'w');
            stream_copy_to_stream($handler, $digitalResource->getHandler());
            fclose($handler);
        }
        
        $message->path = $messageDir.DIRECTORY_SEPARATOR.$message->messageId.'.json';
        file_put_contents($message->path, json_encode($message->object));
    }

    protected function getObjectFromBindingArray($sourceObject, $bindingArray)
    {
        $targetObject = new \stdClass();

        foreach ($bindingArray as $sourceProperty => $targetProperty) {
            if (isset($sourceObject->{$sourceProperty})) {
                if (substr_count($targetProperty, "->") > 0) {
                    $this->createObjectProperties($targetObject, $targetProperty, $sourceObject->{$sourceProperty});
                } else {
                    $targetObject->{$targetProperty} = $sourceObject->{$sourceProperty};
                }
            }
        }
        // var_dump($targetObject);

        return $targetObject;
    }

    protected function createObjectProperties($targetObject, $objectProperties, $valueToAdd)
    {
        $allProperties = explode("->", $objectProperties);
        $lastProperty = array_pop($allProperties);
        $objectToAdd = $targetObject;

        foreach ($allProperties as $objectProperty) {
            if (!property_exists($objectToAdd, $objectProperty)) {
                $objectToAdd->{$objectProperty} = new \stdClass();
            }
            $objectToAdd = $objectToAdd->{$objectProperty};
        }
        $objectToAdd->{$lastProperty} = $valueToAdd;
    }
}
