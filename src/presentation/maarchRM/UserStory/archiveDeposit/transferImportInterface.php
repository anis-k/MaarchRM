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
namespace presentation\maarchRM\UserStory\archiveDeposit;

/**
 * User story for message deposit
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface transferImportInterface
{
    /**
     * Receive a new message
     *
     * @uses medona/ArchiveTransfer/create
     * @return medona/message/receive
     */
    public function createTransfer($messageFile, $attachments, $schema, $filename = null);

    /**
     * Receive message with all contents embedded
     *
     * @param mixed  $messageFile   The message binary contents OR a filename
     * @param string $connector        The schema used
     * @param array  $params        An array of params
     *
     * @uses medona/ArchiveTransfer/createSource
     *
     * @return medona/message/receive
     */
    public function createTransferSource($messageFile, $connector, $params = []);

    /**
     * Get the source inputs view
     *
     * @return medona/message/getSourceInputs
     */
    public function readTransfer_schema_Inputs_source_($schema, $source);

    /**
     * Get the message import view
     *
     * @return medona/message/messageimport
     */
    public function readTransfer();

    /**
     * Validate archive transfer by depositor from import screen
     *
     * @uses medona/archiveTransfer/updateRequestvalidate_messageId_
     * @return medona/message/validateArchiveTransfer
     */
    public function updateTransfervalidate_messageId_();
}
