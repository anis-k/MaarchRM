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
 * Trait for notification
 *
 * @author Maarch Cyril Vazquez <cyril.vazquez@maarch.org>
 */
abstract class abstractBusinessNotificationMessage extends abstractBusinessMessage
{
    
    /**
     * Add binary data objects for documents type CDO only
     * @param recordsManagement/archive $archive
     * @param object                    $dataObjectPackageElement
     */
    protected function addArchiveBinaryDataObjectss($archive, $dataObjectPackageElement)
    {
        if (count($archive->document)) {
            foreach ($archive->document as $document) {
                if ($document->type == 'CDO') {
                    $this->addBinaryDataObject($document->digitalResource, $dataObjectPackageElement, false);
                }
            }
        }

        if (isset($archive->contents) && count($archive->contents)) {
            foreach ($archive->contents as $subArchive) {
                $this->addArchiveBinaryDataObjects($subArchive, $dataObjectPackageElement);
            }
        }
    }

    /**
     * Add binary data objects attachments for transfer reply as a certificate of deposit
     * @param object $resource
     * @param object $binaryDataObjectElement
     */
    protected function addAttachment($resource, $binaryDataObjectElement)
    {
        $attachmentElement = $this->message->xml->createElement('Attachment');
        $binaryDataObjectElement->appendChild($attachmentElement);

        $address = $resource->address[0];
        $uri = str_replace('\\', LAABS_URI_SEPARATOR, $address->path);

        $attachmentElement->setAttribute('uri', $uri);
    }

    /**
     * Add archive identifiers
     * @param recordsManagement/archive $archive
     * @param object                    $parentElement
     */
    protected function addArchiveDescriptiveMetadata($archive, $parentElement)
    {
        $archiveElement = $this->message->xml->createElement('archive');
        $archiveElement->setAttribute('xml:id', 'archiveId_' . $archive->archiveId);

        $parentElement->appendChild($archiveElement);

        if (count($archive->document)) {
            foreach ($archive->document as $document) {
                if ($document->type == 'CDO') {
                    $archiveElement->setAttribute('oid', (string) $document->digitalResource->resId);
                }
            }
        }

        if (isset($archive->contents) && count($archive->contents)) {
            foreach ($archive->contents as $subArchive) {
                $this->addArchiveDescriptiveMetadata($subArchive, $archiveElement);
            }
        }
    }

    protected function addProfileDataObjectPackage()
    {
        // Data object package
        $dataObjectPackageElement = $this->message->xml->createElement('DataObjectPackage');
        $this->message->xml->documentElement->appendChild($dataObjectPackageElement);

        $this->addProfileDescriptiveMetadata($dataObjectPackageElement);
    }

    protected function addProfileDescriptiveMetadata($dataObjectPackageElement){
        $descriptiveMetadataElement = $this->message->xml->createElement('ManagementMetadata');
        $dataObjectPackageElement->appendChild($descriptiveMetadataElement);

        $descriptionPackageElement = $this->message->xml->createElementNS('maarch.org:laabs:medona', 'DescriptionPackage');
        $descriptiveMetadataElement->appendChild($descriptionPackageElement);
        $this->addProfileDescriptionMetadata($this->message->profile, $descriptionPackageElement);

    }

    /**
     * Add archive identifiers
     * @param recordsManagement/archive $archivalProfile
     */
    protected function addProfileDescriptionMetadata($archivalProfile, $parentElement)
    {
        $profileXmlSerializer = \laabs::newSerializer("recordsManagement/archivalProfile", "xml");
        $xml = $profileXmlSerializer->read($archivalProfile);
        $descriptionFragment = $this->message->xml->createDocumentFragment();
        $descriptionFragment->appendXml($xml);
        $parentElement->appendChild($descriptionFragment);
    }

}
