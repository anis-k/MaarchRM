<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle medona.
 *
 * Bundle medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\medona\Model;

/**
 * Class model that represents an archival agreement between archival services and transferring/originating/requester
 *
 * @package medona
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 *
 * @pkey [archivalAgreementId]
 * @key [reference]
 */
class archivalAgreement
{

    /**
     * The archival agreement identifier
     *
     * @var id
     * @notempty
     */
    public $archivalAgreementId;

    /**
     * The archival agreement reference
     *
     * @var string
     * @notempty
     */
    public $reference;

    /**
     * The archival agreement name
     *
     * @var string
     * @notempty
     */
    public $name;

    /**
     * The description
     *
     * @var string
     */
    public $description;

    /**
     * The organization registration number of archiver
     *
     * @var string
     * @notempty
     */
    public $archiverOrgRegNumber;

    /**
     * The array of organization originator identifier
     *
     * @var tokenlist
     * @notempty
     */
    public $originatorOrgIds;

    /**
     * The organization registration number of archiver
     *
     * @var string
     * @notempty
     */
    public $depositorOrgRegNumber;

    /**
     * The begin date for agreement
     *
     * @var date
     */
    public $beginDate;

    /**
     * The end date of agreement
     *
     * @var date
     */
    public $endDate;

    /**
     * The list of allowed file format puids separated by spaces
     *
     * @var string
     */
    public $allowedFormats;

    /**
     * Indicates wheter the agreement is valid or not
     *
     * @var bool
     */
    public $enabled = true;

    /**
     * The archival profile reference
     *
     * @var string
     */
    public $archivalProfileReference;

    /**
     * The archival profile reference
     *
     * @var string
     */
    public $serviceLevelReference;

    /**
     * The maximum size of digital archives for the agreement
     *
     * @var integer
     */
    public $maxSizeAgreement = 0;

    /**
     * The maximum size of digital archives per transfer
     *
     * @var integer
     */
    public $maxSizeTransfer = 0;

    /**
     * The maximum size of digital archives per day
     *
     * @var integer
     */
    public $maxSizeDay = 0;

    /**
     * The maximum size of digital archives per month
     *
     * @var integer
     */
    public $maxSizeMonth = 0;

    /**
     * The maximum size of digital archives per week
     *
     * @var integer
     */
    public $maxSizeWeek = 0;

    /**
     * The maximum size of digital archives per year
     *
     * @var integer
     */
    public $maxSizeYear = 0;

    /**
     * Define if the archival agreement accept signed archives only
     *
     * @var boolean
     */
    public $signed = false;

    /**
     * Define if the archival agreement accept automatically archives tranfer
     *
     * @var boolean
     */
    public $autoTransferAcceptance = false;

    /**
     * Define if the archival agreement process automatically the small archives tranfer
     *
     * @var boolean
     */
    public $processSmallArchive = false;
}
