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
 * Class for ArchiveDestructionRequest message handling
 */
class ArchiveDestructionRequest 
    extends abstractBusinessRequestMessage
{
    /**
     * Generate a new archive delivery request
     * @param medona/message $message
     */
    public function generate($message)
    {
        parent::generate($message);

        $derogationElement = $this->message->xml->createElement('Derogation', (int) $message->derogation);
        $message->xml->documentElement->appendChild($derogationElement);
        
        $this->addUnitIdentifiers();

        $this->setOrganization($message->recipientOrgRegNumber, "ArchivalAgency");

        $this->setOrganization($message->senderOrgRegNumber, "Requester");
    }   

}