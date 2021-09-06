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
class ArchiveTransferReply extends abstractMessage
{
    public function send($message)
    {
        $archiveTransferReply = abstractMessage::send($message);

        $archiveTransferReply->replyCode = $this->sendReplyCode($message->replyCode);
       
        $archiveTransferReply->messageRequestIdentifier = $message->requestReference;

        $archiveTransferReply->messageIdentifier = $message->reference;

        $archiveTransferReply->grantDate = $message->operationDate;

        $archiveTransferReply->archivalAgency = $this->sendOrganization($message->senderOrg);

        $archiveTransferReply->transferringAgency = $this->sendOrganization($message->recipientOrg);
    }
}