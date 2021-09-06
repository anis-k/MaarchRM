<?php
namespace bundle\medona\Model;
/**
 * The archive transfer message
 * 
 * @package Medona
 * @author  Alexandre MORIN (Maarch) <alexandre.morin@maarch.org>
 * 
 * @xmlns medona org:afnor:medona:1.0
 * @xmlns rm maarch.org:laabs:recordsManagement
 * 
 */
class archivePackage
{
    /**
     * The descriptive metadata contents
     * @var recordsManagement/archive[]
     * @xpath rm:archive
     */
    public $archive;

    /**
     * The descriptive metadata contents
     * @var recordsManagement/archiveRelationship[]
     * @xpath rm:archiveRelationship
     */
    public $archiveRelationship;
}
