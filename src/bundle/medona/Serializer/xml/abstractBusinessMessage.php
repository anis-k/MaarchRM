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

namespace bundle\medona\Serializer\xml;

/**
 * Class for business messages
 *
 * @author Maarch Cyril Vazquez <cyril.vazquez@maarch.org>
 */
abstract class abstractBusinessMessage extends abstractMessage
{

    protected $archivalAgreements = array();

    protected $currentArchivalAgreement;

    protected $archiveXmlSerializer;

    protected function useArchivalAgreement($archivalAgreementReference)
    {
        $archivalAgreementController = \laabs::newController('medona/archivalAgreement');

        if (!isset($this->archivalAgreements[$archivalAgreementReference])) {
            $this->currentArchivalAgreement = $archivalAgreementController->getByReference($archivalAgreementReference);

            $this->archivalAgreements[$archivalAgreementReference] = $this->currentArchivalAgreement;
        } else {
            $this->currentArchivalAgreement = $this->archivalAgreements[$archivalAgreementReference];
        }

        return $this->currentArchivalAgreement;
    }

    protected function getArchivalAgreement()
    {
        if ($archivalAgreementElement = $this->message->xPath->query("medona:ArchivalAgreement")->item(0)) {
            return $archivalAgreementElement->nodeValue;
        }
    }

    protected function setArchivalAgreement($archivalAgreement)
    {
        $archivalAgreementText = $this->message->xml->createTextNode((string) $archivalAgreement);

        if (!$archivalAgreementElement = $this->message->xPath->query("medona:ArchivalAgreement")->item(0)) {
            $archivalAgreementElement = $this->message->xml->createElement('ArchivalAgreement');
            $this->message->xml->documentElement->appendChild($archivalAgreementElement);
        } else {
            $archivalAgreementElement->nodeValue = "";
        }

        $archivalAgreementElement->appendChild($archivalAgreementText);
    }

    protected function setAuthorizationRequestReplyIdentifier($authorizationReference)
    {
        $authorizationRequestReplyIdentifierText = $this->message->xml->createTextNode((string) $authorizationReference);

        if (!$authorizationRequestReplyIdentifierElement = $this->message->xPath->query("medona:AuthorizationRequestReplyIdentifier")->item(0)) {
            $authorizationRequestReplyIdentifierElement = $this->message->xml->createElement('AuthorizationRequestReplyIdentifier');
            $this->message->xml->documentElement->appendChild($authorizationRequestReplyIdentifierElement);
        } else {
            $authorizationRequestReplyIdentifierElement->nodeValue = "";
        }

        $authorizationRequestReplyIdentifierElement->appendChild($authorizationRequestReplyIdentifierText);
    }

    protected function addUnitIdentifiers()
    {
        if (isset($this->message->unitIdentifier)) {
            foreach ($this->message->unitIdentifier as $unitIdentifier) {
                $unitIdentifierElement = $this->message->xml->createElement('UnitIdentifier', (string) $unitIdentifier->objectId);
                $this->message->xml->documentElement->appendChild($unitIdentifierElement);
            }
        }
    }

    protected function addDataObjectPackage($withAttachments=true)
    {
        // Data object package
        $dataObjectPackageElement = $this->message->xml->createElement('DataObjectPackage');
        $this->message->xml->documentElement->appendChild($dataObjectPackageElement);

        $this->addArchives($dataObjectPackageElement, $withAttachments);

        // Set management metadata from first archive
        if (isset($this->message->archive[0])) {
            $archive = $this->message->archive[0];

            $managementMetadataElement = $this->message->xml->createElement('ManagementMetadata');
            $dataObjectPackageElement->appendChild($managementMetadataElement);

            $this->setArchivalProfile($archive, $managementMetadataElement);
            $this->setServiceLevel($archive, $managementMetadataElement);
            $this->setAccessRule($archive, $managementMetadataElement);
            $this->setAppraisalRule($archive, $managementMetadataElement);
        }
    }

    /**
     * View the contents of a package
     * @param medona/message $message
     * 
     * @return string The html
     */
    public function viewDataObjectPackage($message)
    {
        $dataObjectPackageElement = $this->message->xPath->query("medona:DataObjectPackage")->item(0);

        $xsltProcessor = new \XsltProcessor();
        $xslt = new \DOMDocument();
        $xslt->load(
            LAABS_BUNDLE . DIRECTORY_SEPARATOR.
            'medona'.DIRECTORY_SEPARATOR.
            LAABS_RESOURCE . DIRECTORY_SEPARATOR.
            'xml'.DIRECTORY_SEPARATOR.
            'medona2html_v10.xsl'
        );

        $notMedonaElements = $this->message->xPath->query('//*[namespace-uri() != "org:afnor:medona:1.0"]');
        $bundles = array();
        foreach ($notMedonaElements as $notMedonaElement) {
            $bundle = \laabs::resolveXmlNamespace($notMedonaElement->namespaceURI);
            $class = $notMedonaElement->localName;
            if (!isset($bundles[$bundle])) {
                $bundles[$bundle] = array();
            }
            if (!in_array($class, $bundles[$bundle])) {
                $bundles[$bundle][] = $class;

                $classXslFile =
                    str_replace(DIRECTORY_SEPARATOR, LAABS_URI_SEPARATOR, getcwd()).LAABS_URI_SEPARATOR
                    .LAABS_BUNDLE.LAABS_URI_SEPARATOR
                    .$bundle.LAABS_URI_SEPARATOR
                    .LAABS_RESOURCE . LAABS_URI_SEPARATOR
                    .'xml'.LAABS_URI_SEPARATOR
                    .$class.'.xsl';

                if (is_file($classXslFile)) {
                    $importElement = $xslt->createElementNS("http://www.w3.org/1999/XSL/Transform", 'xsl:import');
                    $importElement->setAttribute('href', 'file:///'.$classXslFile);

                    $xslt->documentElement->insertBefore($importElement, $xslt->documentElement->firstChild);
                }
            }
        }

        $xsltProcessor->importStylesheet($xslt);

        $html = $xsltProcessor->transformToXml($message->xml);

        return $html;
    }

    protected function addArchives($dataObjectPackageElement, $withAttachments)
    {
        // Add data objects with id = resId
        if (isset($this->message->archive) && is_array($this->message->archive)) {
            foreach ($this->message->archive as $archive) {
                $this->addArchiveBinaryDataObjects($archive, $dataObjectPackageElement, $withAttachments);
            }
        }

        $descriptiveMetadataElement = $this->message->xml->createElement('DescriptiveMetadata');
        $dataObjectPackageElement->appendChild($descriptiveMetadataElement);

        $descriptionPackageElement = $this->message->xml->createElementNS('maarch.org:laabs:medona', 'descriptionPackage');
        $descriptiveMetadataElement->appendChild($descriptionPackageElement);

        // Add description
        if (isset($this->message->archive)) {
            foreach ($this->message->archive as $archive) {
                $this->addArchiveDescriptiveMetadata($archive, $descriptionPackageElement);
            }
        }

        
    }

    protected function addArchiveBinaryDataObjects($archive, $dataObjectPackageElement, $withAttachments)
    {
        if (count($archive->document)) {
            foreach ($archive->document as $document) {
                $this->addBinaryDataObject($document->digitalResource, $dataObjectPackageElement, $withAttachments);
            }
        }

        if (isset($archive->contents) && count($archive->contents)) {
            foreach ($archive->contents as $subArchive) {
                $this->addArchiveBinaryDataObjects($subArchive, $dataObjectPackageElement, $withAttachments);
            }
        }
    }

    protected function addBinaryDataObject($resource, $dataObjectPackageElement, $withAttachments)
    {
        $binaryDataObjectElement = $this->message->xml->createElement('BinaryDataObject');
        $dataObjectPackageElement->appendChild($binaryDataObjectElement);

        $binaryDataObjectElement->setAttribute('xml:id', 'resId_' . (string) $resource->resId);

        if (isset($resource->relationship) && count($resource->relationship)) {
            foreach ($resource->relationship as $relationship) {
                $relationshipElement = $this->message->xml->createElement('Relationship');
                $binaryDataObjectElement->appendChild($relationshipElement);

                $relationshipElement->setAttribute('target', (string) $relationship->relatedResId);
                $relationshipElement->setAttribute('type', (string) $relationship->typeCode);

                $this->addBinaryDataObject($relationship->relatedResource, $dataObjectPackageElement, $withAttachments);
            }
        }

        if ($withAttachments) {
            $this->addAttachment($resource, $binaryDataObjectElement);
        }

        $formatElement = $this->message->xml->createElement('Format', $resource->mimetype);
        $binaryDataObjectElement->appendChild($formatElement);

        $messageDigestElement = $this->message->xml->createElement('MessageDigest', $resource->hash);
        $messageDigestElement->setAttribute('algorithm', $resource->hashAlgorithm);
        $binaryDataObjectElement->appendChild($messageDigestElement);

        $signatureStatusElement = $this->message->xml->createElement('SignatureStatus', 'valide');
        $binaryDataObjectElement->appendChild($signatureStatusElement);

        $sizeElement = $this->message->xml->createElement('Size', $resource->size);
        $this->message->size += $resource->size;
        $binaryDataObjectElement->appendChild($sizeElement);
    }

    protected function addAttachment($resource, $binaryDataObjectElement)
    {
        $attachmentElement = $this->message->xml->createElement('Attachment');
        $binaryDataObjectElement->appendChild($attachmentElement);

        /*if (isset($resource->address[0])) {
            $address = $resource->address[0];
            $uri = str_replace('\\', LAABS_URI_SEPARATOR, $address->path);
            if (isset($address->repository)) {
                $uri = str_replace('\\', LAABS_URI_SEPARATOR, $address->repository->repositoryUri).LAABS_URI_SEPARATOR.$uri;
            }
             $attachmentElement->setAttribute('uri', $uri);
        }*/

        if (!$resource->fileName) {
            $resource->fileName = $resource->resId;
        }

        $attachmentElement->setAttribute('filename', $resource->fileName);

        $attachmentFilename = $this->messageDirectory.DIRECTORY_SEPARATOR.$this->message->messageId.DIRECTORY_SEPARATOR.$resource->fileName;
        $handler = fopen($attachmentFilename, 'w+');
        stream_copy_to_stream($resource->getHandler(), $handler);
        fclose($handler);
    }

    protected function addArchiveDescriptiveMetadata($archive, $parentElement)
    {
        if (!isset($this->archiveXmlSerializer)) {
            $this->archiveXmlSerializer = \laabs::newSerializer("recordsManagement/archive", "xml");
        } 
        $this->archiveXmlSerializer->getDescription($archive, $parentElement);
    }

    protected function setArchivalProfile($archive, $managementMetadataElement)
    {
        if (isset($archive->archivalProfileReference)) {
            $archivalProfileElement = $this->message->xml->createElement('ArchivalProfile', $archive->archivalProfileReference);
            $managementMetadataElement->appendChild($archivalProfileElement);
        }
    }

    protected function setServiceLevel($archive, $managementMetadataElement)
    {
        if (isset($archive->serviceLevelReference)) {
            $serviceLevelElement = $this->message->xml->createElement('ServiceLevel', $archive->serviceLevelReference);
            $managementMetadataElement->appendChild($serviceLevelElement);
        }
    }

    protected function setAccessRule($archive, $managementMetadataElement)
    {
        $accessRuleElement = $this->message->xml->createElement('AccessRule');
        $managementMetadataElement->appendChild($accessRuleElement);

        $rmAccessRuleElement = $this->message->xml->createElementNS('maarch.org:laabs:recordsManagement', 'accessRule');
        $accessRuleElement->appendChild($rmAccessRuleElement);

        $rmCodeElement = $this->message->xml->createElement('code', $archive->accessRuleCode);
        $rmAccessRuleElement->appendChild($rmCodeElement);

        if (isset($archive->accessRuleStartDate)) {
            $rmStartDateElement = $this->message->xml->createElement('startDate', $archive->accessRuleStartDate->format('Y-m-d'));
            $rmAccessRuleElement->appendChild($rmStartDateElement);
        }

        $rmDurationElement = $this->message->xml->createElement('duration', (string) $archive->accessRuleDuration);
        $rmAccessRuleElement->appendChild($rmDurationElement);

        $this->setOrganization($archive->originatorOrgRegNumber, 'originatingAgency', $accessRuleElement);
    }

    protected function setAppraisalRule($archive, $managementMetadataElement)
    {
        $appraisalRuleElement = $this->message->xml->createElement('AppraisalRule');
        $managementMetadataElement->appendChild($appraisalRuleElement);

        $appraisalCodeElement = $this->message->xml->createElement('AppraisalCode', $archive->finalDisposition);
        $appraisalRuleElement->appendChild($appraisalCodeElement);

        $durationElement = $this->message->xml->createElement('Duration', (string) $archive->retentionDuration);
        $appraisalRuleElement->appendChild($durationElement);

        if (isset($archive->retentionStartDate)) {
            $startDateElement = $this->message->xml->createElement('StartDate', $archive->retentionStartDate->format('Y-m-d'));
        } else {
            $startDateElement = $this->message->xml->createElement('StartDate');
        }
        $appraisalRuleElement->appendChild($startDateElement);
    }
}
