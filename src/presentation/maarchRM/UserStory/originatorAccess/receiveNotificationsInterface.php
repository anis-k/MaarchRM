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
namespace presentation\maarchRM\UserStory\originatorAccess;

/**
 * User story restitution request
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface receiveNotificationsInterface
{
    /**
     * List notification
     *
     * @param string $reference
     * @param string $archiver
     * @param string $originator
     * @param string $depositor
     * @param string $archivalAgreement
     * @param date   $fromDate
     * @param date   $toDate
     *
     * @uses medona/archiveNotification/readList
     * @return medona/message/notificationHistory
     */
    public function readNotifications();

    /**
     * Search form
     * @param string $reference
     * @param string $archiver
     * @param string $originator
     * @param string $depositor
     * @param string $archivalAgreement
     * @param date   $fromDate
     * @param date   $toDate
     *
     * @uses medona/archiveNotification/readHistory
     * @return medona/message/notificationHistory
     */
    public function readNotificationsHistory($reference = null, $archiver = null, $originator = null, $depositor = null, $archivalAgreement = null, $fromDate = null, $toDate = null);

}