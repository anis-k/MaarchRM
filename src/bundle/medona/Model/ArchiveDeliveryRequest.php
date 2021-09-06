<?php
namespace bundle\medona\Model;
/**
 * The archive transfer message
 * 
 * @package Medona
 * @author  Alexandre MORIN (Maarch) <alexandre.morin@maarch.org>
 * 
 * @xmlns medona org:afnor:medona:1.0
 * 
 */
class ArchiveDeliveryRequest
    extends AbstractBusinessMessage
{
    /**
     * @var boolean
     * @xpath medona:Derogation
     */
    public $derogation;

    /**
     * @var medona/Identifier[]
     * @xpath medona:UnitIdentifier
     */
    public $unitIdentifier;

    /**
     * @var medona/Organization
     * @xpath medona:ArchivalAgency
     */
    public $archivalAgency;

    /**
     * @var medona/Organization
     * @xpath medona:Requester
     */
    public $requester;
}
