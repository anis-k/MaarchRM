<?php
namespace bundle\medona\Model;
/**
 * The archive transfer message
 * 
 * @package Medona
 * @author  Alexandre MORIN (Maarch) <alexandre.morin@maarch.org>
 * 
 * @xmlns medona:org:afnor:medona:1
 * 
 */
class ArchiveTransferRequestReply
    extends AbstractBusinessMessage
{

    /**
     * @var string
     * @xpath medona:ReplyCode
     */
    public $replyCode;

    /**
     * @var medona/Identifier
     * @xpath medona:MessageRequestIdentifier
     */
    public $messageRequestIdentifier;
    
    /**
     * @var date
     * @xpath medona:TransfertDate
     */
    public $transfertDate;

    /**
     * @var medona/Organization
     * @xpath medona:ArchivalAgency
     */
    public $archivalAgency;

    /**
     * @var medona/Organization
     * @xpath medona:TransferringAgency
     */
    public $transferringAgency;
}
