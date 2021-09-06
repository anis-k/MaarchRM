<?php
namespace bundle\medona\Model;
/**
 * The archive transfer message
 * 
 * @package Medona
 * @author Alexandre MORIN (Maarch) <alexandre.morin@maarch.org>
 * 
 * @xmlns medona org:afnor:medona:1.0
 * 
 */
class AuthorizationRequestContent
{
    /**
     * @var string
     * @xpath medona:AuthorizationReason
     */
    public $authorizationReason;
    
    /**
     * @var medona/Identifier[]
     * @xpath medona:Comment
     */
    public $comment;

    /**
     * @var date
     * @xpath medona:RequestDate
     */
    public $requestDate;
    
    /**
     * @var medona/Identifier[]
     * @xpath medona:UnitIdentifier
     */
    public $unitIdentifier;
    
    /**
     * @var medona/Organization
     * @xpath medona:Requester
     */
    public $requester;
    
    /**
     * @var medona/AuthorizationRequestReply[]
     * @xpath medona:AuthorizationRequestReply
     */
    public $authorizationRequestReply;
}
