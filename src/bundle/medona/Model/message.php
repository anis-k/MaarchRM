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
namespace bundle\medona\Model;

/**
 * Class model that represents a message's comment
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 *
 * @pkey [messageId]
 * @key [type, senderOrgRegNumber, reference]
 */
class message
{
    /**
     * The message identifier
     *
     * @var id
     */
    public $messageId;

    /**
     * The message schema
     *
     * @var string
     */
    public $schema;

    /**
     * The message type
     *
     * @var string
     */
    public $type;

    /**
     * The message status
     *
     * @var string
     */
    public $status;

    /**
     * The date of creation of the message
     *
     * @var timestamp
     */
    public $date;

    /**
     * The message reference
     *
     * @var string
     */
    public $reference;

    /**
     * The comment
     *
     * @var string
     */
    public $comment;

    /**
     * The message's creator identifier
     *
     * @var string
     */
    public $accountId;

    /**
     * The sender's registration number
     *
     * @var string
     */
    public $senderOrgRegNumber;

    /**
     * The sender's organization name
     *
     * @var string
     */
    public $senderOrgName;

    /**
     * The receiver's registration number
     *
     * @var string
     */
    public $recipientOrgRegNumber;

    /**
     * The recipient's organization name
     *
     * @var string
     */
    public $recipientOrgName;

    /**
     * The archival agreement reference
     *
     * @var string
     */
    public $archivalAgreementReference;

    /**
     * The reply code
     *
     * @var string
     */
    public $replyCode;

    /**
     * The date of reception of the message
     *
     * @var timestamp
     */
    public $receptionDate;

    /**
     * The date of the operation
     *
     * @var timestamp
     */
    public $operationDate;

    /**
     * The related message reference
     *
     * @var string
     */
    public $relatedReference;

    /**
     * The reply message identifier
     *
     * @var string
     */
    public $replyReference;

    /**
     * The request message identifier
     *
     * @var string
     */
    public $requestReference;

    /**
     * The authorization message identifier
     *
     * @var string
     */
    public $authorizationReference;
    
    /**
     * The authorization message reason
     *
     * @var string
     */
    public $authorizationReason;
    
    /**
     * The authorization message requester organization registration number
     *
     * @var string
     */
    public $authorizationRequesterOrgRegNumber;
    
    /**
     * Derogation
     *
     * @var boolean
     */
    public $derogation;

    /**
     * the number of archive of the message
     *
     * @var integer
     */
    public $dataObjectCount;

    /**
     * The size of the message
     *
     * @var integer
     */
    public $size;

    /**
     * The active status of the message
     *
     * @var boolean
     */
    public $active = true;

    /**
     * The message is archived
     *
     * @var boolean
     */
    public $archived = false;

    /**
     * Incoming message
     *
     * @var boolean
     */
    public $isIncoming = false;

    /**
     * The related archives identifier
     *
     * @var unitIdentifier[]
     */
    public $unitIdentifier;

    /**
     * The php object
     * @var string
     */
    public $data;

    /**
     * The path of exchange file
     * @var string
     */
    public $path;
}
