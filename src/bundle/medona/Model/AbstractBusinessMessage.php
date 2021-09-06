<?php
namespace bundle\medona\Model;
/**
 * The abstract for all business message
 * 
 * @package Medona
 * @author  Alexandre MORIN (Maarch) <alexandre.morin@maarch.org>
 * 
 * @xmlns medona org:afnor:medona:1.0
 * 
 */
class AbstractBusinessMessage
{
    /**
     * The message id
     *
     * @var id
     * @xvalue generate-id
     */
    public $id;

    /**
     * The comments
     *
     * @var string[]
     * @xpath medona:Comment
     */
    public $comment;

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
     * @var medona/Identifier
     * @xpath medona:Signature
     */
    public $signature;
    
    /**
     * @var medona/Identifier
     * @xpath medona:ArchivalAgreement
     */
    public $archivalAgreement;

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
}
