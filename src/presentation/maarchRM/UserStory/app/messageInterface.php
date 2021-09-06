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
namespace presentation\maarchRM\UserStory\app;

/**
 * Standard interface for log archive description class
 *
 * @package Medona
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface messageInterface
{

    /**
     * Set archive delivery messages
     *
     * @return medona/message/index
     * @requires [archiveDeposit/processTransfer, archiveDeposit/transferHistory, archiveDeposit/transferImport, archiveDeposit/transferSend, delivery/*, destruction/destructionAuthorizationRequest, destruction/destructionHistory, destruction/destructionProcess, restitution/*, definitiveTransfer/*, originatorAccess/*]
     */
    public function readMedonaList();

    /**
     * Set archive delivery messages
     *
     * @uses medona/message/readCount
     * @return medona/message/countMessages
     */
    public function readMedonaCount();

    /**
     * Read a message
     * @param string $messageId The message identifier
     *
     * @uses medona/message/read_messageId_
     * @return medona/message/display
     */
    public function readMedonaMessage_messageId_($messageId);

    /**
     * Read a message
     * @param string $messageId The message identifier
     *
     * @uses medona/message/read_messageId_
     * @return medona/message/displayInHistory
     */
    public function readMedonaHistorymessage_messageId_($messageId);

    /**
     * Read a message as certificate
     * @param string $messageId The message identifier
     *
     * @uses medona/message/read_messageId_
     * @return medona/message/getCertificate
     */
    public function readMedonaMessageCertificate_messageId_($messageId);

    /**
     * Read a message
     * @param id $messageId    The message identifier
     * @param id $attachmentId The attachment identifier
     *
     * @uses medona/message/read_messageId_Attachment_attachmentId_
     * @return medona/message/getDataObjectAttachment
     */
    public function readMedonaMessage_messageId_Attachment_attachmentId__filename_($messageId, $attachmentId);

    /**
     * get a form to search resource
     * @param string $mimetype The mime type
     *
     * @return medona/message/showDataObjectAttachmentsContent
     * @uses medona/message/readDataObjectAttachmentsContent
     */
    public function readMedonaShowdataobjectattachmentscontent_mimetype_($mimetype);

    /**
     * End a transaction
     * @param id $messageId
     *
     * @uses medona/message/updateEndtransaction_messageId_
     * @return medona/message/endTransaction
     */
    public function updateEndtransaction_messageId_($messageId);

    /**
     * Get the message import view
     * @uses medona/message/read_messageId_Export
     * @return medona/message/messageExport
     */
    public function readMedonaMessage_messageId_Export();

    /**
     * Retry message
     * @uses medona/message/read_messageId_Retry
     * @return medona/message/retry
     */
    public function updateMedonaMessage_messageId_Retry();


}
