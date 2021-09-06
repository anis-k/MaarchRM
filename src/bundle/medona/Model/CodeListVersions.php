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
class CodeListVersions
{
    /**
     * @var medona/Code
     * @xpath medona:AuthorizationReasonCodeListVersion
     */
    public $authorizationReasonCodeListVersion;

    /**
     * @var medona/Code
     * @xpath medona:FileEncodingCodeListVersion
     */
    public $fileEncodingCodeListVersion;
    
    /**
     * @var medona/Code
     * @xpath medona:FileFormatCodeListVersion
     */
    public $fileFormatCodeListVersion;
    
    /**
     * @var medona/Code
     * @xpath medona:MessageDigestAlgorithmCodeListVersion
     */
    public $messageDigestAlgorithmCodeListVersion;

    /**
     * @var medona/Code
     * @xpath medona:RelationshipCodeListVersion
     */
    public $relationshipCodeListVersion;

    /**
     * @var medona/Code
     * @xpath medona:ReplyCodeListVersion
     */
    public $replyCodeListVersion;
    
    /**
     * @var medona/Code
     * @xpath medona:SignatureStatusCodeListVersion
     */
    public $signatureStatusCodeListVersion;
}
