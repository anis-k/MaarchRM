<?php
/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of bundle mades.
 *
 * Bundle mades is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle mades is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle mades.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\mades\Message;

/**
 * Class model that represents a management metadata
 *
 * @package Mades
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
class ManagementMetadata
{
    /**
     *
     *
     * @var string
     */
    public $archivalProfile;

    /**
     *
     *
     * @var string
     */
    public $serviceLevel;

    /**
     *
     *
     * @var recordsManagement/accessRule
     */
    public $accessRule;

    /**
     *
     *
     * @var mades/AppraisalRule
     */
    public $appraisalRule;

    /**
     *
     *
     * @var mades/ClassificationRule
     */
    public $classificationRule;
}
