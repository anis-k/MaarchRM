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
 * Trait for all types of messages
 *
 * @author Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 */
abstract class abstractMessage
{
    protected $messageDirectory;

    protected $sdoFactory;

    protected $message;

    protected $orgController;
    
    protected $archiveController;

    /**
     * Constructor
     * @param string                  $messageDirectory
     * @param \dependency\sdo\Factory $sdoFactory
     */
    public function __construct($messageDirectory, \dependency\sdo\Factory $sdoFactory)
    {
        $this->messageDirectory = $messageDirectory;

        $this->sdoFactory = $sdoFactory;
        
        $this->orgController = \laabs::newController('organization/organization');
        $this->archiveController = \laabs::newController('recordsManagement/archive');
    }

    /**
     * Load a message
     * @param medona/message $message 
     */
    public function loadMessage($message)
    {
        $this->message = $message;

        //$this->messageDirectory = \laabs::configuration('medona')['messageDirectory'] . DIRECTORY_SEPARATOR . $this->message->messageId;

        $message->xPath->registerNamespace('medona', 'org:afnor:medona:1.0');
        $message->xPath->registerNamespace('recordsManagement', 'maarch.org:laabs:recordsManagement');
        $message->xPath->registerNamespace('digitalResource', 'maarch.org:laabs:digitalResource');
        $message->xPath->registerNamespace('organization', 'maarch.org:laabs:organization');
    }


    /**
     * Generate a new message header
     * @param medona/message $message
     */
    public function generate($message)
    {
        $this->loadMessage($message);
        $messageTypeElement = $this->message->xml->createElementNS('org:afnor:medona:1.0', $message->type);
        $this->message->xml->appendChild($messageTypeElement);

        // Comments
        if (!empty($this->message->comment)) {
            if (is_array($this->message->comment)) {
                foreach ($this->message->comment as $comment) {
                    $this->addComment($comment);
                }
            } else {
                $this->addComment((string) $this->message->comment);
            }
        }

        // Date
        $this->setDate($this->message->date);

        // Identifier
        $this->setMessageIdentifier($this->message->reference);

        // Code list
        $codeListVersionsElement = $this->message->xml->createElement('CodeListVersions');
        $this->message->xml->documentElement->appendChild($codeListVersionsElement);
    }

    protected function addComment($comment) 
    {
        $commentElement = $this->message->xml->createElement('Comment', $comment);
        $this->message->xml->documentElement->appendChild($commentElement);

        return $commentElement;
    }

    protected function setDate($date) 
    {
        $dateText = $this->message->xml->createTextNode($date->format('Y-m-d\TH:i:s'));

        if (!$dateElement = $this->message->xPath->query("medona:Date")->item(0)) {
            $dateElement = $this->message->xml->createElement('Date');
            $this->message->xml->documentElement->appendChild($dateElement);
        } else {
            $dateElement->nodeValue = "";
        }

        $dateElement->appendChild($dateText);
    }

    protected function setMessageIdentifier($reference) 
    {
        $identifierText = $this->message->xml->createTextNode((string) $reference);

        if (!$messageIdentifierElement = $this->message->xPath->query("medona:MessageIdentifier")->item(0)) {
            $messageIdentifierElement = $this->message->xml->createElement('MessageIdentifier');
            $this->message->xml->documentElement->appendChild($messageIdentifierElement);
        } else {
            $messageIdentifierElement->nodeValue = "";
        }

        $messageIdentifierElement->appendChild($identifierText);
    }

    
    protected function setOrganization($organization, $role) 
    {
        if (is_object($organization)) {
            $orgRegNumber = $organization->registrationNumber;
        } else {
            $orgRegNumber = $organization;
            $organization = $this->orgController->getOrgByRegNumber($orgRegNumber);
        }

        $descriptionMetadata = $this->setOrganizationDescriptionMetadata($organization);

        $orgIdentifierText = $this->message->xml->createTextNode((string) $orgRegNumber);

        if (!$orgElement = $this->message->xPath->query("medona:" . $role)->item(0)) {
            $orgElement = $this->message->xml->createElement($role);
            $this->message->xml->documentElement->appendChild($orgElement);

            $identifierElement = $this->message->xml->createElement('Identifier');
            $orgElement->appendChild($identifierElement);

        } else {
            $identifierElement = $this->message->xPath->query("medona:Identifier", $orgElement)->item(0);
            $identifierElement->nodeValue = "";
        }

        $identifierElement->appendChild($orgIdentifierText);

        $orgDescriptionMetadata = $this->message->xml->createElement('OrganizationDescriptiveMetadata');
        $orgElement->appendChild($orgDescriptionMetadata);

        $organizationNode = $this->setOrganizationDescriptionMetadata($organization);
        $orgDescriptionMetadata->appendChild($organizationNode);
    } 
    

    protected function setOrganizationDescriptionMetadata($organization) 
    {      
        $organizationNode = $this->message->xml->createElementNS('maarch.org:laabs:organization', 'organization');

        if (isset($organization->orgName)) {

            $orgNameElement = $this->message->xml->createElement('orgName', $organization->orgName);

            $organizationNode->appendChild($orgNameElement);
        }

        if (isset($organization->orgTypeCode)) {
            $orgTypeElement = $this->message->xml->createElement('orgTypeCode', $organization->orgTypeCode);

            $organizationNode->appendChild($orgTypeCodeElement);
        }

        if (isset($organization->legalClassification)) {
            $legalClassificationElement = $this->message->xml->createElement('legalClassification', $organization->legalClassification);

            $organizationNode->appendChild($legalClassificationElement);
        }

        if (isset($organization->taxIdentifier)) {
            $taxIdentifierElement = $this->message->xml->createElement('taxIdentifier', $organization->taxIdentifier);

            $organizationNode->appendChild($taxIdentifierElement);
        }

        return $organizationNode;
    }
}
