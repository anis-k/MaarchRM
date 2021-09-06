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
class PhysicalDataObject
{
    /**
     * @var medona/Identifier[]
     * @xpath medona:Relationship
     */
    public $relationship;
    
    /**
     * @var string
     * @xpath medona:Size
     */
    public $size;
}
