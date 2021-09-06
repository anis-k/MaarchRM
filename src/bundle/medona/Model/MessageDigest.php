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
class MessageDigest
{
    /**
     * @var string
     * @xpath text()
     */
    public $value;

    /**
     * @var string
     * @xpath @algorithm
     */
    public $algorithm;

    /**
     * Contructor
     * @param string $value
     * @param string $algorithm
     */
    public function __construct($value, $algorithm)
    {
        $this->value = $value;
        $this->algorithm = $algorithm;
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
