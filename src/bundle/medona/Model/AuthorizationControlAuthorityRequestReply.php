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
class AuthorizationControlAuthorityRequestReply
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
     * @var medona/Organization
     * @xpath medona:ArchivalAgency
     */
    public $archivalAgency;

    /**
     * @var medona/Organization
     * @xpath medona:ControlAuthority
     */
    public $controlAuthority;
}
