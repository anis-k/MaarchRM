<?php

/*
 * Copyright (C) 2020 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\UserStory\archiveManagement;

/**
 * Interface for statistics
 */
interface StatisticsInterface
{
    /**
     * Display statistics page
     *
     * @param float  $sizeFilter
     *
     * @uses   Statistics/Statistics/index
     * @return Statistics/Statistics/index
     *
     */
    public function readStatistics();

    /**
     * Retrieve statistics
     *
     * @param string $operation
     * @param string $startDate
     * @param string $endDate
     * @param string $filter
     * @param float  $sizeFilter
     *
     * @uses Statistics/Statistics/retrieve
     * @return Statistics/Statistics/retrieveStats
     *
     */
    public function readStatisticsRetrieve($operation = null, $startDate = null, $endDate = null, $filter = null, $sizeFilter = 3);
}
