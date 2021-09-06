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
class Organization
{
    /**
     * @var medona/Identifier
     * @xpath medona:Identifier
     */
    public $identifier;
    
    /**
     * @var object
     * @xpath medona:OrganizationDescriptiveMetadata/*
     */
    public $organizationDescriptiveMetadata;

    /**
     * @var string
     */
    public $organizationDescriptiveMetadataClass;
}
