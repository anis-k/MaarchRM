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
class DataObjectPackage
{
    /**
     * @var medona/BinaryDataObject[]
     * @xpath medona:BinaryDataObject
     */
    public $binaryDataObject;

    /**
     * @var medona/PhysicalDataObject[]
     * @xpath medona:PhysicalDataObject
     */
    public $physicalDataObject;
    
    /**
     * @var object
     * @xpath medona:DescriptiveMetadata/*
     */
    public $descriptiveMetadata;

    /**
     * @var string
     */
    public $descriptiveMetadataClass;
    
    /**
     * @var medona/ManagementMetadata
     * @xpath medona:ManagementMetadata
     */
    public $managementMetadata;
}
