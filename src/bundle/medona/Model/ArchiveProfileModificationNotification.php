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
class ArchiveProfileModificationNotification
{    
    /**
     * The date
     *
     * @var datetime
     * @xpath medona:Date
     */
    public $date;
    
    /**
     * The message identifier
     *
     * @var medona/Identifier
     * @xpath medona:MessageIdentifier
     */
    public $messageIdentifier;
    
    /**
     * @var medona/CodeListVersions
     * @xpath medona:CodeListVersions
     */
    public $codeListVersions;
    
    /**
     * @var medona/DataObjectPackage
     * @xpath medona:DataObjectPackage
     */
    public $dataObjectPackage;
    
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
