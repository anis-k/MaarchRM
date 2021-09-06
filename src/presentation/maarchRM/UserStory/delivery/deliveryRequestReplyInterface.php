<?php
/*
 * Copyright (C) 2016 Maarch
 *
 * This file is part of medona.
 *
 * medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona. If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\UserStory\delivery;

/**
 * User story - delivery request reply
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface deliveryRequestReplyInterface
{
    /**
     * Get the deliveries messages
     *
     * @uses medona/archiveDelivery/readRequestReplyList
     * @return medona/message/DeliveryRequestReplyList
     */
    public function readDeliveryList();

    /**
     * Download delivery request reply
     *
     * @uses medona/archiveDelivery/read_messageId_exportArchive
     * @return medona/message/messageExport
     */
    public function readDelivery_messageId_Export();
}
