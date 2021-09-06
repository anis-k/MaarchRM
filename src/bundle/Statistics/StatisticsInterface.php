<?php
/*
 * Copyright (C) 2020 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\Statistics;

/**
 * Interface for statistics
 *
 * @package Statistics
 * @author  Jérôme Boucher <jerome.boucher@maarch.org>
 */
interface StatisticsInterface
{
    /**
     * Retrieve default stats for screen
     *
     * @action Statistics/Statistics/index
     */
    public function index();

    /**
     * Retrieve basics stats
     *
     * @param string   $operation
     * @param string   $startDate
     * @param string   $endDate
     * @param string   $filter
     * @param integer  $sizeFilter
     *
     * @action Statistics/Statistics/retrieve
     */
    public function retrieve($operation = null, $startDate = null, $endDate = null, $filter = null, $sizeFilter = 0);
}
