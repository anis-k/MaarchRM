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
class AppraisalRule
{
    /**
     * @var string
     * @xpath medona:AppraisalCode
     */
    public $appraisalCode;

    /**
     * @var string
     * @xpath medona:Duration
     */
    public $duration;
    
    /**
     * @var date
     * @xpath medona:StartDate
     */
    public $startDate;

}
