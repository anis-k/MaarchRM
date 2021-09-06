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
class AuthorizationOriginatingAgencyRequestReply
    extends AbstractBusinessMessage
{
   
    /**
     * @var medona/Identifier
     * @xpath medona:MessageyRequestIdentifier
     */
    public $messageyRequestIdentifier;
    
    /**
     * @var string
     * @xpath medona:ReplyCode
     */
    public $replyCode;
    
    /**
     * @var medona/Organization
     * @xpath medona:ArchivalAgency
     */
    public $archivalAgency;

    /**
     * @var medona/Organization
     * @xpath medona:OriginatingAgency
     */
    public $originatingAgency;
    
}
