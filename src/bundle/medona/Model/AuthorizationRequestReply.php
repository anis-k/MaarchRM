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
class AuthorizationRequestReply
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
}
