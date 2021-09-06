<?php
/*
 * Copyright (C) 2018 Maarch
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
namespace bundle\medona\Controller;

/**
 * Archive transfer interface
 *
 * @package Medona
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
interface ArchiveTransferInterface
{
    /**
     * Receive message with all contents embedded
     *
     * @param string $message The message object
     *
     * @return mixed medona/message The acknowledgement
     */
    public function receive($message);

    /**
     * Validate message against schema and rules
     * @param string $messageId The message identifier
     * @param object $archivalAgreement The archival agreement
     *
     * @return boolean The validation result
     */
    public function validate($messageId, $archivalAgreement = null);

    /**
     * Process the archive transfer
     * @param mixed $messageId The message object or the message identifier
     *
     * @return string The reply message identifier
     */
    public function process($messageId);
}
