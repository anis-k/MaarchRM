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
 * Class for reply messages
 *
 * @author Maarch Cyril Vazquez <cyril.vazquez@maarch.org>
 */
abstract class abstractBusinessReplyMessage
    extends abstractBusinessMessage
{

    protected function setReplyCode($replyCode) 
    {
        $replyCodeText = $this->message->xml->createTextNode((string) $replyCode);

        if (!$replyCodeElement = $this->message->xPath->query("medona:ReplyCode")->item(0)) {
            $replyCodeElement = $this->message->xml->createElement('ReplyCode');
            $this->message->xml->documentElement->appendChild($replyCodeElement);
        } else {
            $replyCodeElement->nodeValue = "";
        }

        $replyCodeElement->appendChild($replyCodeText);
    }


    protected function setMessageRequestIdentifier($requestReference) 
    {
        $messageRequestIdentifierText = $this->message->xml->createTextNode((string) $requestReference);

        if (!$messageRequestIdentifierElement = $this->message->xPath->query("medona:MessageRequestIdentifier")->item(0)) {
            $messageRequestIdentifierElement = $this->message->xml->createElement('MessageRequestIdentifier');
            $this->message->xml->documentElement->appendChild($messageRequestIdentifierElement);
        } else {
            $messageRequestIdentifierElement->nodeValue = "";
        }

        $messageRequestIdentifierElement->appendChild($messageRequestIdentifierText);
    }
}
