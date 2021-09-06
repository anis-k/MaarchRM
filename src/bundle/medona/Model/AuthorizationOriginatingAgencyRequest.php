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
class AuthorizationOriginatingAgencyRequest
    extends AbstractBusinessMessage
{

    /**
     * @var medona/AuthorizationRequestContent
     * @xpath medona:AuthorizationRequestContent
     */
    public $authorizationRequestContent;
    
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
