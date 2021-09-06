<?php
namespace bundle\medona\Model;
/**
 * Class model that represents a digital resource format 
 *
 * @package Seda
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 * 
 * @xmlns medona org:afnor:medona:1.0
 * 
 */
class Code
{
    /**
     * @var string
     * @xpath text()
     */
    public $value;

    /**
     * @var string
     * @xpath @medona:name
     */
    public $name;

    /**
     * @var string
     * @xpath @medona:listID
     */
    public $listID;

    /**
     * @var string
     * @xpath @medona:listName
     */
    public $listName;

    /**
     * @var string
     * @xpath @medona:listAgencyID
     */
    public $listAgencyID;

    /**
     * @var string
     * @xpath @medona:listAgencyName
     */
    public $listAgencyName;

    /**
     * @var string
     * @xpath @medona:listVersionID
     */
    public $listVersionID;

    /**
     * @var string
     * @xpath @medona:listDataURI
     */
    public $listSchemaURI;

    /**
     * @var string
     * @xpath @medona:listURI
     */
    public $listURI;

    /**
     * Contructor
     * @param string $value
     * @param string $name
     */
    public function __construct($value, $name=null)
    {
        $this->value = $value;
        $this->name = $name;
    }

    /**
     * Get string
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }
}
