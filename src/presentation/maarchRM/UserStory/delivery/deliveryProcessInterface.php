<?php
/*
 * Copyright (C) 2015 Maarch
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
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\delivery;

/**
 * User story for delivery
 * @author Benjamin Rousseliere <benjamin.rousseliere@maarch.org>
 */
interface deliveryProcessInterface
{
    /**
     * Get delivery requests to process
     *
     * @uses medona/archiveDelivery/readDeliveryProcessList
     * @return medona/message/deliveryProcessList
     */
    public function readDeliveryProcess();

    /**
     * Process delivery
     *
     * @uses medona/archiveDelivery/updateDelivery_message_process
     * @return medona/message/processArchiveRestitution
     */
    public function updateDelivery_message_process();
}