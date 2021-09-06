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
class Identifier
{
    /**
     * @var string
     * @xpath text()
     */
    public $value;

    /**
     * @var string
     * @xpath @medona:schemeID
     */
    public $schemeID;

    /**
     * @var string
     * @xpath @medona:schemeName
     */
    public $schemeName;

    /**
     * @var string
     * @xpath @medona:schemeAgencyID
     */
    public $schemeAgencyID;

    /**
     * @var string
     * @xpath @medona:schemeAgencyName
     */
    public $schemeAgencyName;

    /**
     * @var string
     * @xpath @medona:schemeVersionID
     */
    public $schemeVersionID;

    /**
     * @var string
     * @xpath @medona:schemeDataURI
     */
    public $schemeDataURI;

    /**
     * @var string
     * @xpath @medona:schemeURI
     */
    public $schemeURI;

    /**
     * Contructor
     * @param string $value
     */
    public function __construct($value)
    {
        $this->value = $value;
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
