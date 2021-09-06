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
class BinaryObject
{
    /**
     * @var string
     * @xpath text()
     */
    public $value;

    /**
     * @var string
     * @xpath @uri
     */
    public $uri;

    /**
     * @var string
     * @xpath @filename
     */
    public $filename;

    /**
     * Contructor
     * @param string $value
     */
    public function __construct($value=null)
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
