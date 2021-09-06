<?php
/*
 * Copyright (C) 2015 Maarch
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
 * Class for Acknowledgement message handling
 */
class Acknowledgement extends abstractMessage implements \bundle\medona\Controller\AcknowledgementInterface
{

    /**
     * Generate a new acknowledgement
     * @param medona/message $message The ack message
     */
    public function send($message)
    {
        $acknowledgement = abstractMessage::send($message);

        $acknowledgement->messageIdentifier = $message->reference;

        $acknowledgement->messageReceivedIdentifier = $message->requestReference;

        $acknowledgement->receiver = $this->sendOrganization($message->recipientOrg);

        $acknowledgement->sender = $this->sendOrganization($message->senderOrg);
    }
}
