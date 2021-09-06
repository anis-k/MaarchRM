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
 * Message Xml serializer
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class message
{

    protected $sdoFactory;

    protected $xml;

    /**
     * Constructor of message class
     * @param \dependency\xml\Document $xml
     * @param object                   $sdoFactory
     */
    public function __construct(\dependency\xml\Document $xml, \dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->xml = $xml;
        $this->xml->formatOutput = true;

        $this->orgController = \laabs::newController('organization/organization');
        $this->lifeCycleJournalController = \laabs::newController('lifeCycle/journal');

    }

    /**
     * Return new digital resource for an archive
     * @param medona/message $message
     * @param bool           $includeContents
     *
     * @return string
     */
    public function restitute($message, $includeContents = true)
    {

        
        $fragment = $this->xml->createDocumentFragment();
        $fragment->appendFile('medona/view/restitution.xml');
        $messageNode = $this->xml->appendChild($fragment);

        if ($message->archiverOrgRegNumber) {
            $message->archiver = $this->orgController->getOrgByRegNumber($message->archiverOrgRegNumber);
        }

        if ($message->depositorOrgRegNumber) {
            $message->depositor = $this->orgController->getOrgByRegNumber($message->depositorOrgRegNumber);
        }

        if ($message->controlAuthorityOrgRegNumber) {
            $message->controlAuthority = $this->orgController->getOrgByRegNumber($message->controlAuthorityOrgRegNumber);
        }

        $this->xml->setSource('message', $message);
        $this->xml->merge();

        $archiveFragment = $this->xml->createDocumentFragment();
        $archiveFragment->appendFile('recordsManagement/view/archive/restitution.xml');

        foreach ($message->archive as $archive) {
            $archive->lifeCycleEvent = $this->lifeCycleJournalController->getObjectEvents($archive->archiveId, "recordsManagement/archive");
            $archive->relationships = $this->sdoFactory->readChildren('recordsManagement/archiveRelationship', $archive);

            $archiveFragmentNode = $archiveFragment->cloneNode(true);
            $archiveNode = $messageNode->appendChild($archiveFragmentNode);

            $this->xml->setSource('archive', $archive);
            $this->xml->setSource('includeContents', $includeContents);

            $this->xml->merge($archiveNode);

            if (!empty($archive->descriptionClass) && !empty($archive->descriptionObject)) {
                $descriptionXml = \laabs::callOutputRoute(
                    'READ ' . $archive->descriptionClass . LAABS_URI_SEPARATOR . $archive->archiveId,
                    "xml",
                    $archive->descriptionObject
                );

                $descriptionfragment = $this->xml->createDocumentFragment();
                $descriptionfragment->appendXml($descriptionXml);
                $archiveNode->appendChild($descriptionfragment);
            }
        }

        return $this->xml->saveXml();
    }
}
