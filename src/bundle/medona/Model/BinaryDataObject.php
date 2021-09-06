<?php
namespace bundle\medona\Model;
/**
 * The archive transfer message
 * 
 * @package Medona
 * @author  Alexandre MORIN (Maarch) <alexandre.morin@maarch.org>
 * 
 * @xmlns medona org:afnor:medona:1.0
 * @xmlns xml http://www.w3.org/XML/1998/namespace
 * 
 */
class BinaryDataObject
{
    /**
     * @var string
     * @xpath @xml:id
     */
    public $id;

    /**
     * @var medona/Identifier[]
     * @xpath medona:Relationship
     */
    public $relationship;

    /**
     * @var medona/BinaryObject
     * @xpath medona:Attachment
     */
    public $attachment;
    
    /**
     * @var string
     * @xpath medona:Format
     */
    public $format;
    
    /**
     * @var medona/MessageDigest
     * @xpath medona:MessageDigest
     */
    public $messageDigest;
    
    /**
     * @var string
     * @xpath medona:SignatureStatus
     */
    public $signatureStatus;
    
    /**
     * @var integer
     * @xpath medona:Size
     */
    public $size;
    
}
