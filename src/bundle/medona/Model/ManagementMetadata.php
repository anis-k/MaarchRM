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
class ManagementMetadata
{
    /**
     * @var medona/Identifier
     * @xpath medona:ArchivalProfile
     */
    public $archivalProfile;
    
    /**
     * @var medona/DescriptionPackage
     * @xpath medona:DescriptionPackage
     */
    public $descriptionPackage;

    /**
     * @var medona/Identifier
     * @xpath medona:ServiceLevel
     */
    public $serviceLevel;
    
    /**
     * @var object
     * @xpath medona:AccessRule/*
     */
    public $accessRule;

    /**
     * @var string
     */
    public $accessRuleClass;
    
    /**
     * @var medona/AppraisalRule
     * @xpath medona:AppraisalRule
     */
    public $appraisalRule;
}
