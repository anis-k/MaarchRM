<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle medona
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
 * trait for archive transfer
 *
 * @package Medona
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class ArchiveTransfer extends abstractMessage
{
    protected $archiveParser;

    protected $droid;

    protected $jhove;

    /**
     * Receive message with all contents embedded
     * @param mixed  $messageFile The message binary contents OR a filename
     * @param array  $attachments  An array of attachment binary data
     * @param string $filename    The message file name
     *
     * @return medona/message
     *
     * @todo Remove files from sas when error on reception
     */
    public function receive($messageFile, $attachments = array(), $schema = null, $filename = false)
    {
        $message = $this->createNewMessage($schema);

        // Spécifique receive
        $this->receivePackage($message, $messageFile, $attachments, $filename);

        if (empty($schema)) {
            $this->detectSchema($message);
        }

        $this->receiveMessage($message);

        return $this->sendAcknowledgement($message);
    }

        /**
     * Receive message with all contents embedded
     * @param mixed  $package   The message binary contents OR a filename
     * @param string $connector The source name to use
     * @param array  $params    An array of params
     *
     * @return medona/message
     */
    public function receiveSource($package, $connector, $params = [])
    {
        if (!isset($this->packageConnectors[$connector]) || empty($this->packageConnectors[$connector])) {
            throw \laabs::newException('medona/invalidMessageException', "Invalid message: unknown connector", 400);
        }

        $connectorConf = $this->packageConnectors[$connector];

        if (!isset($connectorConf['schema']) || empty($connectorConf['schema']) || !isset($this->packageSchemas[$connectorConf['schema']])) {
            throw \laabs::newException('medona/invalidMessageException', "Invalid message: unknown schema", 400);
        }

        $schema = $connectorConf['schema'];
        $confParams = $connectorConf['params'];

        $params = $this->checkParamsConstraints($confParams, $params);

        $message = $this->createNewMessage($schema);

        if (isset($connectorConf['service'])) {
            $connectorService = \laabs::newService($connectorConf['service']);
            $message->path = $connectorService->receive(
                $package,
                $params,
                $this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId
            );
        } else {
            $messageFile = $package;
            $attachments = [];
        }


        // Traiter le schéma spécifique
        $this->receiveMessage($message);

        // envoyer l'AR
        return $this->sendAcknowledgement($message);
    }

    protected function checkParamsConstraints($confParams, $params)
    {
        foreach ($params as $name => $param) {
            if (!isset($confParams[$name])) {
                $this->sendError("404", 'The parameter %1$s is unknown in the configuration', [$name]);
                break;
            }
            $confParam = $confParams[$name];

            if (!isset($confParam["type"])) {
                $confParam["type"] = "text";
            }

            if (isset($confParam["default"]) && $confParam["type"] != "file" && $param == '') {
                $params[$name] = $confParam["default"];
            }

            if (isset($confParam["required"]) && $confParam["required"] && $param == '') {
                $this->sendError("404", 'The parameter %1$s is required', [$name]);
                continue;
            }

            if ($param == '') {
                continue;
            }

            switch ($confParam["type"]) {
                case 'number':
                    if (!is_numeric($param)) {
                        $this->sendError("405", 'The parameter %1$s needs to be a number', [$name]);
                    }
                    break;
                case 'boolean':
                    if (!is_bool($param)) {
                        $this->sendError("405", 'The parameter %1$s needs to be a boolean', [$name]);
                    }
                    break;
                case 'enum':
                    if (!in_array($param, $confParam["enumNames"])) {
                        $this->sendError("405", 'The parameter %1$s is not in the given list', [$name]);
                    }
                    break;
                case 'organization':
                    try {
                        $this->orgController->getOrgByRegNumber($param);
                    } catch (\Exception $e) {
                        $this->sendError("404", 'Organization identified by %1$s was not found', [$param]);
                    }
                    break;
                case 'archivalProfile':
                    try {
                        $this->archivalProfileController->getByReference($param);
                    } catch (\Exception $e) {
                        $this->sendError("404", 'Archival profile identified by %1$s was not found', [$param]);
                    }
                    break;
            }
        }
        if (count($this->errors) > 0) {
            $exception = \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            $exception->errors = $this->errors;
            throw $exception;
        }
        return $params;
    }

    protected function createNewMessage($schema = null)
    {
        $messageId = \laabs::newId();
        $message = \laabs::newInstance('medona/message');
        $message->messageId = $messageId;
        $message->type = "ArchiveTransfer";
        $message->receptionDate = \laabs::newTimestamp();
        $message->schema = $schema;
        $message->isIncoming = true;

        $messageDir = $this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId;
        if (!is_dir($messageDir)) {
            mkdir($messageDir, 0777, true);
        }

        return $message;
    }

    protected function receiveMessage($message)
    {
        try {
            if (empty($message->path)) {
                $this->sendError("202", "Name of zip and his content files doesn't match");
                throw \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            }

            $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
            $archiveTransferController = \laabs::newController("$namespace/ArchiveTransfer");
            $archiveTransferController->receive($message);

            if ($archiveTransferController->replyCode) {
                $this->replyCode = $archiveTransferController->replyCode;

                $this->errors = array_merge($this->errors, $archiveTransferController->errors);

                throw \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            }

            try {
                $message->senderOrg = $this->orgController->getOrgByRegNumber($message->senderOrgRegNumber);
                $message->senderOrgName = $message->senderOrg->orgName;
            } catch (\Exception $e) {
                $this->sendError("202", "Le service versant identifié par '".$message->senderOrgRegNumber."' est inconnu du système.");

                throw \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            }

            try {
                $message->recipientOrg = $this->orgController->getOrgByRegNumber($message->recipientOrgRegNumber);
                $message->recipientOrgName = $message->recipientOrg->orgName;
            } catch (\Exception $e) {
                $this->sendError("201", "Le service d'archive identifié par '".$message->recipientOrgRegNumber."' est inconnu du système.");

                throw \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            }

            $message->status = "received";
            $this->create($message);
        } catch (\Exception $e) {
            $event = $this->lifeCycleJournalController->logEvent(
                'medona/reception',
                'medona/message',
                $message->messageId,
                $message,
                false
            );

            // Remove files from sas
            $messageURI = $this->messageDirectory.DIRECTORY_SEPARATOR.$message->messageId;
            if (is_dir($messageURI)) {
                \laabs\rmdir($messageURI, true);
            }

            if ($e->getCode() != 0) {
                $exception = \laabs::newException('medona/invalidMessageException', "Invalid message", $e->getCode());
            } else {
                $exception = \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            }

            if (isset($e->errors) && is_array($e->errors)) {
                $exception->errors = array_merge($this->errors, $e->errors);
            } else {
                $exception->errors = $this->errors;
            }

            throw $exception;
        }
    }

    /**
     * sendAcknowledgement message
     *
     * @param  medona/message $message
     *
     * @return medona/message $acknowledgement
     */
    protected function sendAcknowledgement($message)
    {
        $event = $this->lifeCycleJournalController->logEvent(
            'medona/reception',
            'medona/message',
            $message->messageId,
            $message,
            true
        );

        $acknowledgementController = \laabs::newController('medona/Acknowledgement');
        $acknowledgement = $acknowledgementController->send($message);
        $acknowledgement->receivedMessageId = $message->messageId;

        return $acknowledgement;
    }

    protected function receivePackage($message, $messageFile, $attachments, $filename)
    {
        if (is_object($messageFile)) {
            $this->receiveObject($message, $messageFile, $attachments, $filename);
        } else {
            $this->receiveStream($message, $messageFile, $attachments, $filename);
        }
    }

    protected function receivePackageSource($message, $messageFile, $attachments)
    {
        if ($messageFile instanceof \core\Type\StringFile) {
            $data = $messageFile->getData();
        } elseif ($messageFile instanceof \ore\Type\StreamFile) {
            $data = stream_get_contents($messageFile->getStream());
        }
        $this->receiveFiles($message, $data, $attachments, $messageFile->getName(), $mediatype);
    }

    protected function receiveObject($message, $messageFile, $attachments, $filename)
    {
        $data = json_encode($messageFile);

        $this->receiveFiles($message, $data, $attachments, $filename, 'application/json');
    }

    protected function receiveStream($message, $messageFile, $attachments, $filename)
    {
        switch (true) {
            case is_string($messageFile)
                && (filter_var(substr($messageFile, 0, 10), FILTER_VALIDATE_URL) || is_file($messageFile)):
                $data = file_get_contents($messageFile);
                break;

            case is_string($messageFile) &&
                preg_match('%^[a-zA-Z0-9\\\\/+]*={0,2}$%', $messageFile):
                $data = base64_decode($messageFile);
                break;

            case is_resource($messageFile):
                $handler = \core\Encoding\Base64::decode($messageFile);
                $data = stream_get_contents($handler);
                break;
        }

        $mediatype = $this->finfo->buffer($data);
        switch ($mediatype) {
            case 'application/zip':
            case 'application/octet-stream':
            case 'application/x-7z-compressed':
                $this->receiveZip($message, $data, $filename);
                break;

            default:
                $this->receiveFiles($message, $data, $attachments, $filename, $mediatype);
        }
    }

    protected function receiveZip($message, $data, $filename)
    {
        $messageDir = $this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId;

        $zipfile = \laabs\tempnam();
        file_put_contents($zipfile, $data);
        $tmpdir = \laabs::getTmpDir().DIRECTORY_SEPARATOR.$message->messageId;
        if (!is_dir($tmpdir)) {
            mkdir($tmpdir, 0777, true);
        }

        $zip = \laabs::newService('dependency/fileSystem/plugins/zip');

        try {
            $zip->extract($zipfile, $tmpdir);
        } catch (\Exception $e) {
            $this->sendError("400", "An error occurred during the opening of the zip");
            $exception = \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            $exception->errors = $this->errors;

            throw $exception;
        }

        $zipContents = scandir($tmpdir);

        if (!isset($zipContents[2])) {
            throw \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
        }

        $message->attachments = [];

        if (is_dir($tmpdir.DIRECTORY_SEPARATOR.$zipContents[2])) {
            $zipFolder = $tmpdir.DIRECTORY_SEPARATOR.$zipContents[2];
        } else {
            $zipFolder = $tmpdir;
        }

        $messagePathinfo = pathinfo($filename, PATHINFO_FILENAME);

        foreach (scandir($zipFolder) as $file) {
            if ($file != "." && $file != "..") {
                if (is_link($zipFolder.DIRECTORY_SEPARATOR.$file)) {
                    $this->sendError("202", "The container file contains symbolic links");
                    $exception = \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
                    $exception->errors = $this->errors;

                    throw $exception;
                }
                rename($zipFolder.DIRECTORY_SEPARATOR.$file, $messageDir.DIRECTORY_SEPARATOR.$file);

                if (pathinfo($file, PATHINFO_FILENAME) == pathinfo($filename, PATHINFO_FILENAME)) {
                    $message->path = $messageDir.DIRECTORY_SEPARATOR.$file;
                } else {
                    $message->attachments[] = $file;
                }
            }
        }

        unlink($zipfile);
        rmdir($zipFolder);
    }

    protected function receiveFiles($message, $data, $attachments, $filename = false, $mediatype = null)
    {
        $messageDir = $this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId;

        if (!$filename) {
            $filename = (string) $message->messageId;

            if ($mediatype) {
                $filename .= '.'.\laabs\basename($mediatype);
            }
        }

        file_put_contents($messageDir.DIRECTORY_SEPARATOR.$filename, $data);

        $message->path = $messageDir.DIRECTORY_SEPARATOR.$filename;

        $this->receiveAttachments($message, $data, $attachments);
    }

    protected function receiveAttachments($message, $data, $attachments)
    {
        $messageDir = $this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId;

        $message->attachments = [];

        if (count($attachments)) {
            foreach ($attachments as $attachment) {
                if (is_string($attachment)) {
                    if (is_file($attachment)) {
                        copy($attachment, $messageDir.DIRECTORY_SEPARATOR.basename($attachment));
                        $message->attachments[] = $attachment;
                    } elseif (is_dir($attachment)) {
                        $folderFileNames = glob($attachment.DIRECTORY_SEPARATOR."*");
                        foreach ($folderFileNames as $folderFileName) {
                            if (basename($folderFileName) === basename($attachment)) {
                                continue;
                            }
                            copy($folderFileName, $messageDir.DIRECTORY_SEPARATOR.basename($folderFileName));

                            $message->attachments[] = basename($folderFileName);
                        }
                    }
                } elseif (is_object($attachment)) {
                    if (is_resource($attachment->data)) {
                        $data = base64_decode(stream_get_contents($attachment->data));
                    } elseif (filter_var($attachment->data, FILTER_VALIDATE_URL)) {
                        $data = stream_get_contents($attachment->data);
                    } elseif (preg_match('%^[a-zA-Z0-9\\\\/+]*={0,2}$%', $attachment->data)) {
                        $data = base64_decode($attachment->data);
                    } elseif (is_file($attachment->data)) {
                        $data = file_get_contents($attachment->data);
                    }
                    file_put_contents($messageDir.DIRECTORY_SEPARATOR.$attachment->filename, $data);
                    $message->attachments[] = $attachment->filename;
                } elseif (is_resource($attachment)) {
                    $handler = \core\Encoding\Base64::decode($attachment);
                    $data = stream_get_contents($handler);
                    file_put_contents($messageDir.DIRECTORY_SEPARATOR.$attachment->filename, $data);
                    $message->attachments[] = $attachment->filename;
                }
            }
        }
    }

    protected function detectSchema($message)
    {
        $mediatype = $this->finfo->file($message->path);
        if ($mediatype == 'application/xml' || $mediatype === 'text/xml') {
            $xml = new \DOMDocument();
            $xml->load($message->path);
            $messageNamespace = $xml->documentElement->namespaceURI;
            foreach ($this->packageSchemas as $name => $info) {
                if (isset($info->xmlNamespace) && $info->xmlNamespace == $messageNamespace) {
                    $schema = $name;
                    break;
                }
            }

            if (empty($schema)) {
                $schema = \laabs::resolveXmlNamespace($messageNamespace);
            }

            if (empty($schema)) {
                throw \laabs::newException('medona/invalidMessageException', "Unknown message schema'.$messageNamespace", 400);
            }

            $message->schema = $schema;
        } else {
            $message->schema = 'recordsManagement';
        }
    }

    protected function receiveXmlFile($message, $messageFile, $attachments = array())
    {
        $messageDir = $this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId;
        if (!is_dir($messageDir)) {
            mkdir($messageDir, 0777, true);
        }

        // Load Xml from file
        $this->loadXmlFile($message, $messageFile);

        // Save to message directory
        $this->save($message);

        if (count($attachments)) {
            foreach ($attachments as $attachment) {
                if (is_file($attachment)) {
                    copy($attachment, $messageDir.DIRECTORY_SEPARATOR.basename($attachment));
                } elseif (is_dir($attachment)) {
                    $filenames = glob(dirname($messageFile).DIRECTORY_SEPARATOR."*.*");
                    foreach ($filenames as $filename) {
                        if (basename($filename) === basename($messageFile)) {
                            continue;
                        }
                        copy($filename, $messageDir.DIRECTORY_SEPARATOR.basename($filename));
                    }
                }
            }
        }
    }

    protected function receiveXml($message, $messageFile, $attachments = array())
    {
        $messageDir = $this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId;
        if (!is_dir($messageDir)) {
            mkdir($messageDir, 0777, true);
        }

        // Load file xml
        $this->loadXml($message, $messageFile);

        // Save to message directory
        $this->save($message);

        if (count($attachments)) {
            foreach ($attachments as $attachment) {
                $attachment->data = base64_decode($attachment->data);
                file_put_contents($messageDir.DIRECTORY_SEPARATOR.$attachment->filename, $attachment->data);
            }
        }
    }


    /**
     * Validate the messages
     *
     * @return medona/message $message
     */
    public function validateBatch()
    {
        $results = [];

        $messageIds = $this->sdoFactory->index("medona/message", ["messageId"], "(status='received' OR status='modified') AND type='ArchiveTransfer' AND active=true");

        // Avoid paralleling processes
        foreach ($messageIds as $messageId) {
            $message = $this->sdoFactory->read('medona/message', (string) $messageId);

            if (!in_array($message->status, ['received', 'modified'])) {
                continue;
            }

            $this->changeStatus($message->messageId, "processing");

            $this->loadData($message);

            try {
                $results[(string) $message->messageId] = $this->validate($message);
            } catch (\Exception $e) {
                $results[(string) $message->messageId] = $e;
            }
        }

        return $results;
    }

    /**
     * Validate message against schema and rules
     * @param mixed  $messageId         The message identifier
     * @param object $archivalAgreement The archival agreement
     *
     * @return boolean
     */
    public function validate($messageId, $archivalAgreement = null)
    {
        $this->errors = array();
        $this->replyCode = null;

        $message = $this->sdoFactory->read('medona/message', $messageId);
        $this->loadData($message);

        if (in_array($message->status, ["error", "validationError"])) {
            $this->sendError('104', "The message is in error status");
            $exception = \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            $exception->errors = $this->errors;

            throw $exception;
        }

        try {
            $this->validateMessageHeaders($message);
            $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
            $archiveTransferController = \laabs::newController("$namespace/ArchiveTransfer");
            $res = $archiveTransferController->validate($message, $this->currentArchivalAgreement);

            if ($archiveTransferController->replyCode) {
                $this->replyCode = $archiveTransferController->replyCode;
            }

            $this->errors = array_merge($this->errors, $archiveTransferController->errors);
            $this->infos = array_merge($this->infos, $archiveTransferController->infos);

        } catch (\Exception $exception) {
            $this->errors[] = new \core\Error($exception->getMessage());
            $this->sendValidationError($message);

            $exception = \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            $exception->errors = $this->errors;

            throw $exception;
        }

        // Non blocking errors
        if (count($this->errors) > 0) {
            if ($res = -1) {
                $this->sendValidationError($message, false, 'toBeModified');
            } else {
                $this->sendValidationError($message, false, 'validationError');
            }

            $exception = \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            $exception->errors = $this->errors;

            throw $exception;
        } else {
            $message->status = "valid";

            $eventInfo = get_object_vars($message);
            foreach ((array) $this->infos as $info) {
                $eventInfo['code'] = "OK";
                $eventInfo['info'] = $info;

                $event = $this->lifeCycleJournalController->logEvent(
                    'medona/validation',
                    'medona/message',
                    $message->messageId,
                    $eventInfo,
                    true
                );
            }

            $this->update($message);
        }

        if ($this->currentArchivalAgreement && $this->currentArchivalAgreement->autoTransferAcceptance) {
            $this->accept((string) $message->messageId);
        }

        return true;
    }

    /**
     * Validate message header info
     */
    protected function validateMessageHeaders($message)
    {
        // Check sender (depositor) roles
        $this->validateDepositor($message);

        // Check recipient (archiver) roles
        $this->validateArchiver($message);

        if (isset($message->archivalAgreementReference)) {
            $this->validateArchivalAgreement($message);
        }
    }

    /**
     * Validate message against schema and rules for a draft message with no status change nor reply
     * @param mixed $messageId The message identifier
     *
     * @return boolean
     */
    public function validateDraft($messageId)
    {
        $this->errors = array();
        $this->replyCode = null;

        $message = $this->sdoFactory->read('medona/message', $messageId);
        $this->loadData($message);

        try {
            $this->validateMessageHeaders($message);
            $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
            $archiveTransferController = \laabs::newController("$namespace/ArchiveTransfer");
            $archiveTransferController->validate($message, $this->currentArchivalAgreement);

            $this->errors = array_merge($this->errors, $archiveTransferController->errors);
            $this->infos = array_merge($this->infos, $archiveTransferController->infos);

        } catch (\Exception $exception) {
            $this->errors[] = new \core\Error($exception->getMessage());

            $exception = \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            $exception->errors = $this->errors;

            throw $exception;
        }

        // Non blocking errors
        if (count($this->errors) > 0) {

            $exception = \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
            $exception->errors = $this->errors;

            throw $exception;
        }

        return true;
    }

    protected function sendValidationError($message, $sendReply = true, $status = null)
    {
        if ($status) {
            $message->status = $status;
        } else {
            $message->status = "invalid";
        }

        $eventInfo = get_object_vars($message);
        foreach ((array) $this->errors as $error) {
            $eventInfo['code'] = $error->getCode();
            $eventInfo['info'] = $error->getMessage();

            $event = $this->lifeCycleJournalController->logEvent(
                'medona/validation',
                'medona/message',
                $message->messageId,
                $eventInfo,
                false
            );
        }

        if (isset($message->comment)) {
            $message->comment = json_encode($message->comment);
        }

        $this->update($message);

        if ($sendReply) {
            $archiveTransferReplyController = \laabs::newController('medona/ArchiveTransferReply');
            $archiveTransferReplyController->send($message, $this->replyCode);
        }
    }

    protected function validateDepositor($message)
    {
        try {
            $senderOrg = $this->orgController->getOrgByRegNumber($message->senderOrgRegNumber);
        } catch (\Exception $e) {
            $this->sendError("202", "Le service versant identifié par '".$message->senderOrgRegNumber."' est inconnu du système.");

            throw \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
        }

        $senderRoles = (array) $senderOrg->orgRoleCodes;
        if (!in_array("depositor", $senderRoles)) {
            $this->sendError("202", "Le service versant identifié par '".$message->senderOrgRegNumber."' ne possède pas le rôle d'acteur adéquat dans le système.");
        }

        return true;
    }

    protected function validateArchiver($message)
    {
        try {
            $recipientOrg = $this->orgController->getOrgByRegNumber($message->recipientOrgRegNumber);
        } catch (\Exception $e) {
            $this->sendError("201", "Le service d'archive identifié par '".$message->recipientOrgRegNumber."' est inconnu du système.");

            throw \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
        }

        $recipientRoles = (array) $recipientOrg->orgRoleCodes;
        if (!in_array("archiver", $recipientRoles) && !in_array("owner", $recipientRoles)) {
            $this->sendError("202", "Le service d'archives identifié par '".$message->recipientOrgRegNumber."' ne possède pas le rôle d'acteur adéquat dans le système.");
        }

        return true;
    }

    protected function validateArchivalAgreement($message)
    {

        try {
            $this->useArchivalAgreement($message->archivalAgreementReference);

        } catch (\Exception $e) {
            $this->sendError("300", "L'accord de versement '$message->archivalAgreementReference' n'a pas pu être lu.");

            throw \laabs::newException('medona/invalidMessageException', "Invalid message", 400);
        }

        // Check actor orgnizations
        if ($this->currentArchivalAgreement->depositorOrgRegNumber != $message->senderOrgRegNumber) {
            $this->sendError("303", "Le service versant n'est pas conforme à celui indiqué dans l'accord de versement.");
        }

        if ($this->currentArchivalAgreement->archiverOrgRegNumber != $message->recipientOrgRegNumber) {
            $this->sendError("304", "Le service d'archives n'est pas conforme à celui indiqué dans l'accord de versement.");
        }

        if ($message->schema == 'medona') {
            if (empty($this->currentArchivalAgreement->originatorOrgIds)) {
                $this->sendError("300", "Il n'y a pas de service producteur.");
            } else {
                $archivalAgreementOriginators = [];

                foreach ((array) $this->currentArchivalAgreement->originatorOrgIds as $orgId) {
                    $organization = $this->orgController->read($orgId);
                    $archivalAgreementOriginators[] = $organization->registrationNumber;
                }

                $originators = (array) $message->object->dataObjectPackage->managementMetadata->accessRule->originatorOrgRegNumber;

                if (is_array($message->object->dataObjectPackage->descriptiveMetadata->archive)) {
                    foreach ($message->object->dataObjectPackage->descriptiveMetadata->archive as $archive) {
                        if ($archive->originatorOrgRegNumber) {
                            $originators[] = $archive->originatorOrgRegNumber;
                        }
                    }
                }

                foreach ($originators as $originator) {
                    if (!in_array($originator, $archivalAgreementOriginators)) {
                        $this->sendError("302", "Le service producteur identifié par $originator ne correspond pas à l'accord de versement.");
                    }
                }
            }
        }

        $today = \laabs::newDate();
        // Check dates and activity
        if (isset($this->currentArchivalAgreement->beginDate)) {
            if ($this->currentArchivalAgreement->beginDate->diff($today)->invert) {
                $this->sendError("312", "L'accord de versement n'est pas encore en cours de validité.");
            }
        }

        if (isset($this->currentArchivalAgreement->endDate)) {
            if (!$this->currentArchivalAgreement->endDate->diff($today)->invert) {
                $this->sendError("312", "L'accord de versement n'est plus en cours de validité.");
            }
        }

        if ($this->currentArchivalAgreement->enabled != true) {
            $this->sendError("300", "L'accord de versement n'est pas actif.");
        }

        if ($this->currentArchivalAgreement->maxSizeTransfer > 0 && $message->size > ($this->currentArchivalAgreement->maxSizeTransfer*1048576)) {
            $this->sendError("301", "La taille maximale par tranfert de l'accord de versement est dépassée.");
        }

        if ($this->currentArchivalAgreement->maxSizeDay > 0) {
            $res = $this->sdoFactory->summarise("medona/message", "archivalAgreementReference", "size", ["receptionDate > :today and archivalAgreementReference='".$this->currentArchivalAgreement->reference."'", ["today" => $today]]);
            if (count($res) && ($res[$this->currentArchivalAgreement->reference]+$message->size) > ($this->currentArchivalAgreement->maxSizeDay*1048576)) {
                $this->sendError("301", "La taille maximale par jour de l'accord de versement est dépassée.");
            }
        }

        if ($this->currentArchivalAgreement->maxSizeWeek > 0) {
            $aWeekAgo = $today->shift(new \core\Type\Duration("-P7D"));
            $res = $this->sdoFactory->summarise("medona/message", "archivalAgreementReference", "size", ["receptionDate > :aWeekAgo and archivalAgreementReference='".$this->currentArchivalAgreement->reference."'", ["aWeekAgo" => $aWeekAgo]]);
            if (count($res) && ($res[$this->currentArchivalAgreement->reference]+$message->size) > ($this->currentArchivalAgreement->maxSizeWeek*1048576)) {
                $this->sendError("301", "La taille maximale par semaine de l'accord de versement est dépassée.");
            }
        }

        if ($this->currentArchivalAgreement->maxSizeMonth > 0) {
            $aMonthAgo = $today->shift(new \core\Type\Duration("-P1M"));
            $res = $this->sdoFactory->summarise("medona/message", "archivalAgreementReference", "size", ["receptionDate > :aMonthAgo and archivalAgreementReference='".$this->currentArchivalAgreement->reference."'", ["aMonthAgo" => $aMonthAgo]]);
            if (count($res) && ($res[$this->currentArchivalAgreement->reference]+$message->size) > ($this->currentArchivalAgreement->maxSizeMonth*1048576)) {
                $this->sendError("301", "La taille maximale par mois de l'accord de versement est dépassée.");
            }
        }

        if ($this->currentArchivalAgreement->maxSizeYear > 0) {
            $aYearAgo = $today->shift(new \core\Type\Duration("-P1Y"));
            $res = $this->sdoFactory->summarise("medona/message", "archivalAgreementReference", "size", ["receptionDate > :aYearAgo and archivalAgreementReference='".$this->currentArchivalAgreement->reference."'", ["aYearAgo" => $aYearAgo]]);
            if (count($res) && ($res[$this->currentArchivalAgreement->reference]+$message->size) > ($this->currentArchivalAgreement->maxSizeYear*1048576)) {
                $this->sendError("301", "La taille maximale par année de l'accord de versement est dépassée.");
            }
        }

        if (isset($this->currentArchivalAgreement->beginDate) && $this->currentArchivalAgreement->maxSizeAgreement > 0) {
            $archivalAgreementBeginDate = $this->currentArchivalAgreement->beginDate;
            $res = $this->sdoFactory->summarise("medona/message", "archivalAgreementReference", "size", ["receptionDate > :archivalAgreementBeginDate and archivalAgreementReference='".$this->currentArchivalAgreement->reference."'", ["archivalAgreementBeginDate" => $archivalAgreementBeginDate]]);
            if (count($res) && ($res[$this->currentArchivalAgreement->reference]+$message->size) > ($this->currentArchivalAgreement->maxSizeAgreement*1048576)) {
                $this->sendError("301", "La taille maximale de l'accord de versement est dépassée.");
            }
        }

        return true;
    }

    /**
     * Process the messages
     *
     * @return medona/message $message
     */
    public function processBatch()
    {
        $results = array();

        $messageIds = $this->sdoFactory->index("medona/message", ["messageId"], "status='accepted' AND type='ArchiveTransfer' AND active=true");

        foreach ($messageIds as $messageId) {
            // Avoid paralleling processing
            $message = $this->sdoFactory->read('medona/message', (string) $messageId);

            if ($message->status != 'accepted') {
                continue;
            }

            $this->changeStatus($message->messageId, "processing");
            $this->loadData($message);

            try {
                $results[(string) $message->messageId] = $this->process($message);
            } catch (\Exception $e) {
                $results[(string) $message->messageId] = $e;
            }
        }

        $logMessage = ["message" => "%s message(s) processed", "variables"=> count($results)];
        \laabs::notify(\bundle\audit\AUDIT_ENTRY_OUTPUT, $logMessage);

        return $results;
    }

    /**
     * Validate message against schema and rules
     * @param medona/message $messageId
     *
     * @return the result of process
     */
    public function process($messageId)
    {
        if (is_scalar($messageId)) {
            $message = $this->read($messageId);

            $this->loadData($message);
        } else {
            $message = $messageId;
        }

        $this->changeStatus((string) $message->messageId, "processing");

        if (!isset($this->finfo)) {
            $this->finfo = new \finfo();
        }

        try {
            $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
            $archiveTransferController = \laabs::newController("$namespace/ArchiveTransfer");
            list($archives, $archiveRelationships) = $archiveTransferController->process($message);

            $operationResult = true;
        } catch (\Exception $e) {
            $message->status = "processError";
            $this->update($message);

            $this->lifeCycleJournalController->logEvent(
                'medona/processing',
                'medona/message',
                $message->messageId,
                $message,
                false
            );

            throw $e;
        }

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $message->unitIdentifier = array();

            $originatorOrgs = [];
            $organizationController = \laabs::newController("organization/organization");

            $storagePath = $this->archiveController->resolveStoragePath(get_object_vars($message));

            foreach ($archives as $archive) {
                if (!isset($originatorOrgs[$archive->originatorOrgRegNumber])) {
                    $originatorOrg = $organizationController->getOrgByRegNumber($archive->originatorOrgRegNumber);
                    $originatorOrgs[$archive->originatorOrgRegNumber] = $originatorOrg;
                } else {
                    $originatorOrg = $originatorOrgs[$archive->originatorOrgRegNumber];
                }

                $archive->originatorOwnerOrgId = $originatorOrg->ownerOrgId;

                if (!empty($archive->archiveUnitProfile)) {
                    $this->archiveController->completeArchivalProfileCodes($archive);
                }

                if (!empty($archive->accessRuleCode)) {
                    $this->archiveController->completeAccessRule($archive);
                }

                if (!empty($archive->retentionRuleCode) || (!empty($archive->retentionDuration) && !empty($archive->retentionStartDate))) {
                    $this->archiveController->completeRetentionRule($archive);
                }

                if (!empty($archive->serviceLevelReference)) {
                    $this->archiveController->completeServiceLevel($archive);
                }

                $this->archiveController->manageFileplanPosition($archive);

                $this->archiveController->convertArchive($archive);

                $this->generateId($archive);

                $this->archiveController->deposit($archive, $storagePath);

                $unitIdentifier = \laabs::newInstance("medona/unitIdentifier");
                $unitIdentifier->messageId = $message->messageId;
                $unitIdentifier->objectId = (string) $archive->archiveId;
                $unitIdentifier->objectClass = "recordsManagement/archive";

                $this->sdoFactory->create($unitIdentifier);
                $message->unitIdentifier[] = $unitIdentifier;
            }

            //add archives relationships
            if (isset($archiveRelationships)) {
                foreach ($archiveRelationships as $relationship) {
                    $this->archiveRelationshipController->create($relationship);
                }
            }


            $message->status = "processed";
            $this->update($message);
        } catch (\Exception $e) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            $message->status = "processError";
            $operationResult = false;
            $this->update($message);

            $this->lifeCycleJournalController->logEvent(
                'medona/processing',
                'medona/message',
                $message->messageId,
                $message,
                false
            );

            throw $e;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        $this->lifeCycleJournalController->logEvent(
            'medona/processing',
            'medona/message',
            $message->messageId,
            $message,
            true
        );

        $archiveTransferReplyController = \laabs::newController('medona/ArchiveTransferReply');

        $replyMessage = $archiveTransferReplyController->send($message, $archives, "000");

        return (string) $replyMessage->messageId;
    }

    protected function generateId($archive)
    {
        if (!isset(\laabs::configuration('recordsManagement')['archiveIdGenerator']) || empty(\laabs::configuration('recordsManagement')['archiveIdGenerator'])) {
            return;
        }
        $generator = \laabs::configuration('recordsManagement')['archiveIdGenerator'];

        $generatorService = \laabs::newService($generator['service']);
        $generatorService->generate($archive);
    }

        /**
     * Get received archive tranfer message
     *
     * @return array Array of medona/message object
     */
    public function listReception()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $queryParts = [];
        $queryParts[] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts[] = "type='ArchiveTransfer'";
        $queryParts[] = "active=true";
        $queryParts[] = "isIncoming=true";
        $queryParts[] = "status != 'processed'
        AND status != 'error'
        AND status != 'invalid'
        AND status !='draft'
        AND status !='template'
        AND status !='rejected'
        AND status !='acknowledge'" ;

        $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        return $this->sdoFactory->find(
            'medona/message',
            implode(' and ', $queryParts),
            null,
            ">receptionDate",
            false,
            $maxResults
        );
    }

    /**
     * Get sending archive tranfer message
     *
     * @return array Array of medona/message object
     */
    public function listSending()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();
        $accountToken = \laabs::getToken("AUTH");

        $queryParts = [];
        $queryParts[] = "(accountId= '$accountToken->accountId' OR senderOrgRegNumber=$registrationNumber)";
        $queryParts[] = "type='ArchiveTransfer'";
        $queryParts[] = "active=true";
        $queryParts[] = "isIncoming=true";
        $queryParts[] = "status=['sent', 'valid', 'received','accepted']";

        $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        return $this->sdoFactory->find(
            'medona/message',
            implode(' and ', $queryParts),
            null,
            false,
            false,
            $maxResults
        );
    }

    /**
     * Get transfer history
     *
     * @param string $reference         Reference
     * @param string $archiver          Archiver
     * @param string $originator        Originator
     * @param string $depositor         Depositor
     * @param string $archivalAgreement Archival agreement
     * @param date   $fromDate          From date
     * @param date   $toDate            To date
     * @param string $status            Status
     *
     * @return array Array of medona/message object
     */
    public function history(
        $reference = null,
        $archiver = null,
        $originator = null,
        $depositor = null,
        $archivalAgreement = null,
        $fromDate = null,
        $toDate = null,
        $status = null
    ) {
        return $this->search(
            "ArchiveTransfer",
            $reference,
            $archiver,
            $originator,
            $depositor,
            $archivalAgreement,
            $fromDate,
            $toDate,
            $status,
            true
        );
    }

    /**
     * Accept a message
     * @param string $messageId The message identifier
     * @param string $comment   1 comment
     */
    public function accept($messageId, $comment = null)
    {
        $this->changeStatus($messageId, "accepted", $comment);

        $message = $this->sdoFactory->read('medona/message', $messageId);
        if (isset($message->archivalAgreementReference)) {
            $archivalAgreement = $this->sdoFactory->read(
                'medona/archivalAgreement',
                array("reference" => $message->archivalAgreementReference)
            );

            if ($archivalAgreement->processSmallArchive && $message->size <= $this->autoProcessSize) {
                $this->process($messageId);
            }
        }

        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));

        $this->lifeCycleJournalController->logEvent(
            'medona/acceptance',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }

    /**
     * Reject a message
     * @param string $messageId The message identifier
     * @param string $comment   a comment
     */
    public function reject($messageId, $comment = null)
    {
        $this->changeStatus($messageId, "rejected", $comment);
        $archiveTransferReplyController = \laabs::newController('medona/ArchiveTransferReply');
        $archiveTransferReplyController->send($messageId, null, "rejected", $comment);

        $message = $this->sdoFactory->read('medona/message', array('messageId' => $messageId));

        $this->lifeCycleJournalController->logEvent(
            'medona/rejection',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }


    /**
     * Count archive tranfer message
     *
     * @return array Number of received and sent messages
     */
    public function count()
    {
        $registrationNumber = $this->getCurrentRegistrationNumber();

        $res = array();
        $queryParts = array();

        $queryParts["type"] = "type='ArchiveTransfer'";
        $queryParts["registrationNumber"] = "recipientOrgRegNumber=$registrationNumber";
        $queryParts["active"] = "active=true";
        $res['received'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        $queryParts["registrationNumber"] = "senderOrgRegNumber=$registrationNumber";

        $res['sent'] = $this->sdoFactory->count('medona/message', implode(' and ', $queryParts));

        return $res;
    }
}
