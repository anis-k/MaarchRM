<?php
/*
 * Copyright (C) 2015 Maarch
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
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\journal;

/**
 * User story of audit
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface auditInterface
{
   /**
     * Get search form for entries
     *
     * @return audit/event/index
     */
    public function readEventSearch();

    /**
     * Get search form for entries
     *@param string $eventType Type of event
     *@param Date   $fromDate  Start date
     *@param Date   $toDate    End date
     *@param string $accountId Id of account
     *@param string $event     Variables
     *@param string $term      Term to search
     *@param string $wording   Wording to search
     *
     *
     * @uses audit/event/readSearch
     * @return audit/event/search
     */
    public function readEvents($eventType = null, $fromDate = null, $toDate = null, $accountId = null, $event = null, $status = null, $term = null, $wording = null);

        /**
     * Get event
     *
     * @uses audit/event/read_eventId_
     * @return audit/event/getEvent
     */
    public function readEvent_eventId_();
}