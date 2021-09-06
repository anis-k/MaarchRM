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

namespace bundle\medona\Controller;

/**
 * Class for message management
 *
 * @author Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class message
{
    /**
     * @var organization/organization
     */
    protected $orgControler;

    protected $archiveController;

    protected $archiveRelationshipController;

    protected $lifeCycleJournalController;

    protected $digitalResourceController;

    protected $archivalAgreementController;

    protected $archivalProfileController;

    protected $sdoFactory;

    protected $messageTypeController;

    protected $messageTypeParser;

    protected $messageTypeSerializer;

    protected $messageDirectory;

    protected $removeMessageTask;

    protected $profilesDirectory;

    protected $archivalAgreements = array();

    protected $currentArchivalAgreement;

    protected $autoValidateSize;

    protected $autoProcessSize;

    public $errors = [];

    public $infos = [];

    public $replyCode;

    protected $message;

    protected $orgController;

    protected $parentsDerogation;

    protected $finfo;

    protected $packageSchemas = [];
    protected $packageConnectors = [];

    /**
     * Constructor
     * @param string                  $messageDirectory  The message directory
     * @param \dependency\sdo\Factory $sdoFactory        The dependency Sdo Factory
     * @param bool                    $parentsDerogation See the child org messages
     * @param array                   $removeMessageTask Tasks to remove medona messages directories
     * @param integer                 $autoValidateSize  Min size for auto-validation
     * @param integer                 $autoProcessSize   Min size for auto-process
     */
    public function __construct(
        $messageDirectory,
        \dependency\sdo\Factory $sdoFactory,
        $parentsDerogation = true,
        $removeMessageTask = null,
        $autoValidateSize = 0,
        $autoProcessSize = 0,
        $packageSchemas = [],
        $packageConnectors = []
    ) {
        $this->orgController = \laabs::newController('organization/organization');
        $this->archiveController = \laabs::newController('recordsManagement/archive');
        $this->archiveRelationshipController = \laabs::newController('recordsManagement/archiveRelationship');
        $this->lifeCycleJournalController = \laabs::newController('lifeCycle/journal');
        $this->digitalResourceController = \laabs::newController('digitalResource/digitalResource');
        $this->archivalAgreementController = \laabs::newController('medona/archivalAgreement');
        $this->archivalProfileController = \laabs::newController('recordsManagement/archivalProfile');

        $this->profilesDirectory = \laabs::configuration('recordsManagement')['profilesDirectory'];

        $this->messageDirectory = $messageDirectory;
        $this->removeMessageTask = $removeMessageTask;

        if (!is_dir($messageDirectory)) {
            mkdir($messageDirectory, 0777, true);
        }

        $this->sdoFactory = $sdoFactory;

        $this->parentsDerogation = (bool) $parentsDerogation;

        $this->autoValidateSize = $autoValidateSize;
        $this->autoProcessSize = $autoProcessSize;

        $this->errors = array();
        
        $this->finfo = new \finfo(\FILEINFO_MIME_TYPE);

        $this->packageSchemas = $packageSchemas;
        $this->packageConnectors = $packageConnectors;
    }

    /**
     * Extract message info the specific schema model objects
     * @param medona/message $message
     */
    public function extract($message)
    {
        if (is_scalar($message)) {
            $messageId = $message;
            $message = $this->sdoFactory->read('medona/message', $messageId);

            $this->loadXml($message);
        }
    }

    /**
     * Load message xml file, orgnization information and message object
     * @param medona/message $message  The medona message object
     * @param string         $filename The path to a message xml file
     *
     * @return object The Xml document
     */
    public function loadData($message, $filename = null)
    {
        $this->readOrgs($message);

        if (!empty($message->data)) {
            $message->object = json_decode($message->data);
        }

        if (!empty($message->comment) && is_string($message->comment)) {
            $message->comment = json_decode($message->comment);
        }
    }

    /**
     * Get processed archive history message
     *
     * @param string $type              Type
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
    public function search(
        $type,
        $reference = null,
        $archiver = null,
        $originator = null,
        $depositor = null,
        $archivalAgreement = null,
        $fromDate = null,
        $toDate = null,
        $status = null,
        $isIncoming = null
    ) {
        $queryParams = array();
        $queryString = $this->searchMessage(
            $type,
            $reference,
            $archiver,
            $originator,
            $depositor,
            $archivalAgreement,
            $fromDate,
            $toDate,
            $status,
            $isIncoming,
            $queryParams
        );

        $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        return $this->sdoFactory->find('medona/message', $queryString, $queryParams, ">receptionDate", false, $maxResults);
    }

    /**
     * Search message by sender / recipient / reference / date
     *
     * @param string $type              Type
     * @param string $reference         Reference
     * @param string $archiver          Archiver
     * @param string $originator        Originator
     * @param string $depositor         Depositor
     * @param string $archivalAgreement Archival agreement
     * @param date   $fromDate          From date
     * @param date   $toDate            To date
     * @param string $status            Status
     * @param bool   $isIncoming        Is incoming
     * @param string $queryParams       Status
     *
     * @return medona/message[]
     */
    public function searchMessage(
        $type,
        $reference = null,
        $archiver = null,
        $originator = null,
        $depositor = null,
        $archivalAgreement = null,
        $fromDate = null,
        $toDate = null,
        $status = null,
        $isIncoming = null,
        &$queryParams
    ) {
        $queryParts = array();
        $currentService = \laabs::getToken("ORGANIZATION");
        if (!isset($currentService) || is_null($currentService)) {
            throw \laabs::newException(
                "medona/noOrganizationException",
                "User has no organization. Please contact your administrator",
                409
            );
        }
        $currentService->orgRoleCodes = (array) $currentService->orgRoleCodes;

        $isOriginator = in_array('originator', $currentService->orgRoleCodes);
        $isArchiver = in_array('archiver', $currentService->orgRoleCodes);
        $isControlAuthority = in_array('controlAuthority', $currentService->orgRoleCodes);

        if ($archiver || $originator || $depositor) {
            $query = [];

            if ($archiver) {
                $query[] = "(senderOrgRegNumber= :archiver OR recipientOrgRegNumber= :archiver)";
                $queryParams['archiver'] = $archiver;
            }
            if ($originator) {
                $query[] = "(senderOrgRegNumber= :originator OR recipientOrgRegNumber= :originator)";
                $queryParams['originator'] = $originator;
            }
            if ($depositor) {
                $query[] = "(senderOrgRegNumber= :depositor OR recipientOrgRegNumber= :depositor)";
                $queryParams['depositor'] = $depositor;
            }

            $size = sizeof($query);
            if ($size == 1) {
                $queryParts[] = $query[0];
            } elseif ($size == 2) {
                $queryParts[] = '('.$query[0].' AND '.$query[1].')';
            } else {
                $queryParts[] = '('.$query[0].' AND '.$query[1].' AND '.$query[2].')';
            }
        }
        if ($archivalAgreement) {
            $queryParts[] = "archivalAgreementReference= :archivalAgreementReference";
            $queryParams['archivalAgreementReference'] = $archivalAgreement;
        }
        if ($fromDate) {
            $fromDate = \laabs::newDate($fromDate, "Y-m-d H:i:s");
            $queryParts[] = "date >= :fromDate";
            $queryParams['fromDate'] = $fromDate;
        }
        if ($toDate) {
            $toDate = \laabs::newDate($toDate, "Y-m-d H:i:s");
            $toDate->add(new \DateInterval("PT23H59M59S"));
            $queryParts[] = "date <= :toDate";
            $queryParams['toDate'] = $toDate;
        }

        if ($status) {
            $queryParts[] = "status= :status";
            $queryParams['status'] = $status;
        }

        if ($reference) {
            $queryParts[] = "reference= :reference";
            $queryParams['reference'] = $reference;
        }
        if ($isIncoming !== null) {
            switch ($isIncoming) {
                case true:
                    $queryParts[] = "isIncoming= true";
                    break;
                case false:
                    $queryParts[] = "isIncoming= false";
                    break;
            }
        }
        if ($type) {
            $clause = [];
            switch ($type) {
                case "ArchiveDelivery":
                    if ($isOriginator || $isArchiver) {
                        $clause[] = "type='*$type*'";
                    }

                    if (in_array('controlAuthority', $currentService->orgRoleCodes)) {
                        $clause[] =  "type='AuthorizationControlAuthorityRequest' 
                        AND authorizationReason='ArchiveDeliveryRequest'";
                    }
                    break;

                case "ArchiveDestruction":
                    if ($isArchiver) {
                        $clause[] = "type='*$type*'";
                    }

                    if ($isOriginator) {
                        $clause[] = "type='ArchiveDestructionRequest' 
                        OR (type='AuthorizationOriginatingAgencyRequest' 
                        AND authorizationReason='ArchiveDestructionRequest')";
                    }

                    if ($isControlAuthority) {
                        $clause[] = "type='AuthorizationControlAuthorityRequest' 
                        AND authorizationReason='ArchiveDestructionRequest'";
                    }

                    if (!$isArchiver && !$isOriginator && !$isControlAuthority) {
                    }
                    break;

                case "ArchiveRestitution":
                    $clause[] = "type=['ArchiveRestitutionRequest','ArchiveRestitution']";
                    break;

                case "ArchiveNotification":
                    $clause[] = "type=['ArchiveModificationNotification',
                    'ArchiveDestructionNotification',
                    'ArchivalProfileModificationNotification']";
                    break;

                case "ArchiveModificationRequest":
                    $clause[] = "type='ArchiveModificationRequest'";
                    break;

                default:
                    $clause[] = "type= :type AND status !='template'";
                    $queryParams['type'] = $type;
                    break;
            }

            if (!empty($clause)) {
                $queryParts[] =
                    '('
                    .\laabs\implode(') OR (', $clause)
                    .')';
            }
        }

        $currentServiceOrgRegNumber = $currentService->registrationNumber;
        $queryParts[] = "(recipientOrgRegNumber= :currentServiceOrgRegNumber 
        OR senderOrgRegNumber= :currentServiceOrgRegNumber)";
        $queryParams['currentServiceOrgRegNumber'] = $currentServiceOrgRegNumber;

        return '('.implode(') and (', $queryParts).')';
    }

    /**
     * Get current registration number
     *
     * @return string
     */
    protected function getCurrentRegistrationNumber()
    {
        if ($this->parentsDerogation) {
            $userPositionController = \laabs::newController('organization/userPosition');
            $childrenOrgs = $userPositionController->listMyCurrentDescendantServices();

            if ($childrenOrgs == null) {
                $registrationNumber = "''";
            } else {
                $registrationNumber = "['".\laabs\implode("', '", $childrenOrgs)."']";
            }
        } else {
            $organization = \laabs::getToken("ORGANIZATION");

            if (!$organization) {
                $registrationNumber = "''";
            } else {
                $registrationNumber = "'$organization->registrationNumber'";
            }
        }

        return $registrationNumber;
    }

    /**
     * Save xml file
     * @param medona/message $message The Object message
     */
    public function save($message)
    {
        if (isset($message->xml)) {
            $messageFile = $this->messageDirectory.DIRECTORY_SEPARATOR.
                (string) $message->messageId.DIRECTORY_SEPARATOR.
                (string) $message->messageId.'.xml';

            if (!is_dir($this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId)) {
                mkdir($this->messageDirectory.DIRECTORY_SEPARATOR.(string) $message->messageId, 0777, true);
            }

            $message->xml->save($messageFile);

            $message->xml->uri = $messageFile;
        }
    }

    /**
     * Create a message
     * @param medona/message $message The message object
     *
     * @throws \Exception
     *
     * @return string The new message identifier
     */
    public function create($message)
    {
        // Check unique
        $unique = array(
            'type' => $message->type,
            'senderOrgRegNumber' => $message->senderOrgRegNumber,
            'reference' => $message->reference,
        );

        if ($this->sdoFactory->exists("medona/message", $unique)) {
            $this->sendError(
                "103",
                "The message has already been received ('%s' Ref. '%s' from '%s')",
                array($message->type,
                    $message->reference,
                    $message->senderOrgRegNumber)
            );
            throw \laabs::newException(
                "medona/invalidMessageException",
                "The message has already been received ('%s' Ref. '%s' from '%s')",
                409,
                null,
                array($message->type,
                    $message->reference,
                    $message->senderOrgRegNumber)
            );
        }

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            if ($accountToken = \laabs::getToken('AUTH')) {
                $message->accountId = $accountToken->accountId;
            }

            if (isset($message->object)) {
                $message->data = json_encode($message->object);
            }

            if (is_array($message->comment)) {
                $message->comment = json_encode($message->comment);
            }

            $this->sdoFactory->create($message, 'medona/message');

            if (is_array($message->unitIdentifier)) {
                foreach ($message->unitIdentifier as $unitIdentifier) {
                    $this->sdoFactory->create($unitIdentifier);
                }
            }
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $message->messageId;
    }

    /**
     * Update a message
     * @param medona/message $message The message object
     *
     * @return string
     *
     * @throws \Exception
     * @throws \dependency\sdo\Exception
     */
    public function update($message)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            if (isset($message->object)) {
                $message->data = json_encode($message->object);
            }
            if (isset($message->comment) && !is_string($message->comment)) {
                $message->comment = json_encode($message->comment);
            }

            $this->sdoFactory->update($message, 'medona/message');
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $message->messageId;
    }

    /**
     * Read a message
     * @param string $messageId The message identifier
     *
     * @return medona/message The message object
     */
    public function read($messageId)
    {
        $message = $this->sdoFactory->read('medona/message', $messageId);

        $message->unitIdentifier = $this->sdoFactory->readChildren('medona/unitIdentifier', $message);

        $message->lifeCycleEvent = $this->lifeCycleJournalController->getObjectEvents(
            $message->messageId,
            'medona/message'
        );

        $this->loadData($message);

        // Parent request to child reply
        try {
            $replyMessages = $this->sdoFactory->find(
                'medona/message',
                "type='".$message->type."Reply' AND recipientOrgRegNumber='".$message->senderOrgRegNumber."' AND senderOrgRegNumber='".$message->recipientOrgRegNumber."' AND requestReference='".$message->reference."'");
            if (count($replyMessages) > 0) {
                $replyMessage = $replyMessages[0];
                $this->loadData($replyMessage);

                $message->replyMessage = $replyMessage;
            }
        } catch (\Exception $e) {
        }

        // Ack
        try {
            $ackMessages = $this->sdoFactory->find(
                'medona/message',
                "type='Acknowledgement' AND recipientOrgRegNumber='".$message->senderOrgRegNumber."' AND senderOrgRegNumber='".$message->recipientOrgRegNumber."' AND requestReference='".$message->reference."'");
            if (count($ackMessages) > 0) {
                $ackMessage = $ackMessages[0];
                $this->loadData($ackMessage);

                $message->acknowledgement = $ackMessage;
            }
        } catch (\Exception $e) {
        }

        // Related authorization messages for communication and destruction requests
        try {
            $authorizationMessages = $this->sdoFactory->find(
                'medona/message',
                "type=['AuthorizationOriginatingAgencyRequest', 'AuthorizationControlAuthorityRequest'] AND authorizationReason='".$message->type."' AND authorizationRequesterOrgRegNumber='".$message->senderOrgRegNumber."' AND authorizationReference='".$message->reference."'", null, "date");
            if (count($authorizationMessages) > 0) {
                foreach ($authorizationMessages as $authorizationMessage) {
                    if ($message->messageId != $authorizationMessage->messageId) {
                        $authorization = $this->read($authorizationMessage->messageId);

                        $message->authorizationMessages[] = $authorization;

                        // Read reply
                        if (isset($authorization->replyMessage)) {
                            $message->authorizationMessages[] = $authorization->replyMessage;
                        }
                    }
                }
            }
        } catch (\Exception $e) {
        }

        return $message;
    }

    protected function readOrgs($message)
    {
        $message->recipientOrg = $this->readOrg($message->recipientOrgRegNumber);
        $message->recipientOrgName = $message->recipientOrg->orgName;

        $message->senderOrg = $this->readOrg($message->senderOrgRegNumber);
        $message->senderOrgName = $message->senderOrg->orgName;
    }

    protected function readOrg($regNumber)
    {
        $organization = $this->orgController->getOrgByRegNumber($regNumber);

        // Address
        $organization->address = $this->orgController->getAddresses($organization->orgId);

        // Communication
        $organization->communication = $this->orgController->getCommunications($organization->orgId);

        // Contact
        $organization->contact = $this->orgController->getContacts($organization->orgId);

        return $organization;
    }

    /**
     * Compose a new message
     * @param medona/message $message The message Object
     */
    public function generate($message)
    {
        $message->xml = \laabs::newService('dependency/xml/Document');
        $message->xPath = new \DOMXPath($message->xml);

        if (!\laabs::getXmlNamespace($message->schema)) {
            throw new \Exception('Unknown message schema '.$message->schema, 400);
        }

        $messageTypeSerializer = $this->getMessageTypeSerializer($message);

        $messageTypeSerializer->generate($message);

        if (!isset($message->object)) {
            $this->extract($message);
        }
    }

    /**
     * End a transaction
     * @param string $messageId The message identifier
     *
     * @return boolean The result of the operation
     */
    public function endTransaction($messageId)
    {
        $message = $this->sdoFactory->read('medona/message', $messageId);

        $type = substr($message->type, -5);

        if ($type != "Reply") {
            // EXCEPTION
        }

        $message->active = false;
        if (isset($message->requestReference)) {
            $parentKey = array(
                "reference" => $message->requestReference,
                "senderOrgRegNumber" => $message->recipientOrgRegNumber,
                "type" => substr($message->type, 0, -5),
            );

            $parentMessage = $this->sdoFactory->read('medona/message', $parentKey);
            $parentMessage->active = false;
            $this->update($parentMessage);
        }

        $this->update($message);

        return true;
    }

    /**
     * Count active messages for each type
     *
     * @return array The number of active messages sorted by type
     */
    public function countActiveMessages()
    {
        $count = [];

        $count['deposit'] = \laabs::newController('medona/ArchiveTransfer')->count();
        $count['notification'] = \laabs::newController('medona/ArchiveNotification')->count();
        $count['communication'] = \laabs::newController('medona/ArchiveDeliveryRequest')->count();
        $count['restitution'] = \laabs::newController('medona/ArchiveRestitutionRequest')->count();
        $count['restitution']['processed'] = \laabs::newController('medona/ArchiveRestitution')->count();
        $count['destruction'] = \laabs::newController('medona/ArchiveDestructionRequest')->count();

        $authorizationController = \laabs::newController('medona/AuthorizationRequest');

        $count['communicationAuthorization'] = $authorizationController->countCommunication();
        $count['destructionAuthorization'] = $authorizationController->countDestruction();

        return $count;
    }

    /**
     * Export a message
     * @param string $messageId The message identifier
     *
     * @return string The zip of message xml + attachments
     */
    public function export($messageId)
    {
        $message = $this->read($messageId);

        $zip = \laabs::newService('dependency/fileSystem/plugins/zip');

        $messageDirectory = $this->messageDirectory.DIRECTORY_SEPARATOR.$message->messageId;

        $zipfile = $this->messageDirectory.DIRECTORY_SEPARATOR.$message->messageId.".zip";
        if (!is_file($zipfile)) {
            if (is_dir($messageDirectory)) {
                $zip->add($zipfile, $messageDirectory.DIRECTORY_SEPARATOR."*");
            }
        }

        $zipContents = file_get_contents($zipfile);

        //unlink($zipfile);

        return $zipContents;
    }

    /**
     * Retry a message
     * @param string $messageId The message identifier
     */
    public function retry($messageId)
    {
        $message = $message = $this->sdoFactory->read('medona/message', $messageId);
        $message->object = json_decode($message->data);

        $fromStatus = ['error','processError', 'validationError', 'processing','toBeModified'];
        if (!in_array($message->status, $fromStatus)) {
            $this->sendError("101", "Le statut du message est incorrect.");
        }

        switch ($message->status) {
            case 'toBeModified':
                $this->changeStatus($messageId, 'modified');
                break;
            case 'validationError':
                $this->changeStatus($messageId, 'received');
                break;
            case 'processError':
                $this->changeStatus($messageId, 'accepted');
                break;
            default:
                $this->changeStatus($messageId, 'received');
                break;
        }

        $this->lifeCycleJournalController->logEvent(
            'medona/retry',
            'medona/message',
            $message->messageId,
            $message,
            true
        );
    }

    /**
     * Get the data object attachment
     * @param string $messageId    The message identifier
     * @param string $attachmentId The attachment identifier
     * @param string $format       The format of message
     *
     * @return string
     */
    public function getDataObjectAttachment($messageId, $attachmentId, $format = "xml")
    {
        $message = $this->sdoFactory->read('medona/message', $messageId);
        $message->object = json_decode($message->data);

        if ($message->schema != 'medona') {
            $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
            $messageController = \laabs::newController("$namespace/".$message->type);

            $resource = $messageController->getAttachment($message, $attachmentId);

            return $resource;
        }

        // Medona attachment
        $attachment = false;

        foreach ($message->object->dataObjectPackage->binaryDataObject as $binaryDataObject) {
            if ($binaryDataObject->id == $attachmentId) {
                $attachment = $binaryDataObject->attachment;
            }
        }

        if (!$attachment) {
            return false;
        }

        $resourceController = \laabs::newController('digitalResource/digitalResource');

        switch (true) {
            case isset($attachment->filename):
                $messageDir = dirname($message->path);
                $filepath = $messageDir.DIRECTORY_SEPARATOR.$attachment->filename;
                $handler = fopen($filepath, 'r');
                $resource = $resourceController->createFromStream($handler, $attachment->filename);
                break;

            case isset($attachment->uri):
                $handler = fopen($filepath, 'r');
                $resource = $resourceController->createFromStream($handler);
                break;

            case isset($attachment->value):
                $contents = base64_decode($attachment->value);
                $resource = $resourceController->createFromContents($contents);
                break;

            default:
                return false;
        }

        return $resource;
    }

    protected function useArchivalAgreement($archivalAgreementReference)
    {
        if (!isset($this->archivalAgreements[$archivalAgreementReference])) {
            $this->currentArchivalAgreement = $this->archivalAgreementController->getByReference($archivalAgreementReference);

            $this->archivalAgreements[$archivalAgreementReference] = $this->currentArchivalAgreement;
        } else {
            $this->currentArchivalAgreement = $this->archivalAgreements[$archivalAgreementReference];
        }

        return $this->currentArchivalAgreement;
    }

    /**
     * Get the message schema
     * @param medona/message $message The message object
     *
     * @return string The bundle
     */
    protected function getMessageSchema($message)
    {
        $messageNamespace = $message->xml->documentElement->namespaceURI;
        if (!$messageSchema = \laabs::resolveXmlNamespace($messageNamespace)) {
            throw \laabs::newException(
                'medona/invalidMessageException',
                "Unknown message namespace'.$messageNamespace",
                400
            );
        }
        $message->schema = $messageSchema;

        return $messageSchema;
    }

    /**
     * Get the message type and schema specific parser
     * @param medona/message $message The message object
     * @param string         $format  The implementation format
     *
     * @return The parser
     */
    protected function getMessageTypeParser($message, $format = "xml")
    {
        if (!isset($message->type)) {
            $message->type = $message->xml->documentElement->nodeName;
        }

        if (!isset($message->schema)) {
            $messageNamespace = $message->xml->documentElement->namespaceURI;
            if (!$messageSchema = \laabs::resolveXmlNamespace($messageNamespace)) {
                throw new \Exception('Unknown message namespace '.$messageNamespace, 400);
            }
            $message->schema = $messageSchema;
        }

        $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
        $this->messageTypeParser = \laabs::newParser($namespace.LAABS_URI_SEPARATOR.$message->type, $format);

        return $this->messageTypeParser;
    }

    /**
     * Get the message type and schema specific serializer
     * @param medona/message $message The message object
     * @param string         $format  The implementation format
     *
     * @return The parser
     */
    protected function getMessageTypeSerializer($message, $format = "xml")
    {
        if (!isset($message->type)) {
            $message->type = $message->xml->documentElement->nodeName;
        }

        if (!isset($message->schema)) {
            $messageNamespace = $message->xml->documentElement->namespaceURI;
            if (!$messageSchema = \laabs::resolveXmlNamespace($messageNamespace)) {
                throw new \Exception('Unknown message namespace'.$messageNamespace, 400);
            }
            $message->schema = $messageSchema;
        }
        $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
        $this->messageTypeSerializer = \laabs::newSerializer(
            $namespace. LAABS_URI_SEPARATOR. $message->type,
            $format
        );

        return $this->messageTypeSerializer;
    }

    /**
     * Change status of message
     * @param string $messageId The message identifier
     * @param string $status    The new status for message
     * @param string $comment   A comment
     *
     * @return boolean The result of the operation
     */
    protected function changeStatus($messageId, $status, $comment = null)
    {
        $messageStatus = $this->sdoFactory->read('medona/messageStatus', $messageId);
        
        $messageStatus->status = strtolower($status);

        if ($comment) {
            if (!empty($messageStatus->comment) && is_array($messageStatus->comment)) {
                $messageStatus->comment = json_decode($messageStatus->comment);
            } else {
                $messageStatus->comment = [];
            }

            $messageStatus->comment[] = $comment;
            $messageStatus->comment = json_encode($messageStatus->comment);
        }

        $this->sdoFactory->update($messageStatus, 'medona/message');

        return true;
    }

    /**
     * Archive messages
     *
     * @return array
     */
    public function archiveMessages()
    {
        $messages = $this->sdoFactory->find('medona/message', "archived=false");

        $archivedMessageIds = [];

        if (count($messages)) {
            foreach ($messages as $message) {
                if ($this->archiveMessage($message)) {
                    $archivedMessageIds[] = (string) $message->messageId;
                }
            }
        }

        return $archivedMessageIds;
    }

    /**
     * Archive a message
     * @param mixed $messageId The message identifier OR the message itself
     *
     * @return bool
     */
    public function archiveMessage($messageId)
    {
        if (is_object($messageId)) {
            $message = $messageId;
            $messageId = (string) $message->messageId;
        }

        $filename = $this->messageDirectory.DIRECTORY_SEPARATOR.
            (string) $messageId.DIRECTORY_SEPARATOR.
            (string) $messageId.
            '.xml';
        if (!is_file($filename)) {
            return false;
        }

        if (!is_object($messageId)) {
            $message = $this->read($messageId);
        }

        $this->readOrgs($message);

        // Create archive
        $archive = $this->archiveController->newArchive();

        $archive->archiveId = $message->messageId;
        $archive->accessRuleDuration = 'P0D';
        $archive->retentionDuration = 'P0D';
        $archive->finalDisposition = 'preservation';
        $archive->descriptionId = $messageId;
        $archive->descriptionClass = 'medona/message';

        // Create resource
        $messageResource = $this->digitalResourceController->createFromFile($filename);
        $messageResource->archiveId = $archive->archiveId;
        $messageResource->puid = "fmt/101";
        $messageResource->mimetype = "text/xml";
        $this->digitalResourceController->getHash($messageResource, "SHA256");

        $archive->digitalResources[] = $messageResource;

        if ($message->type == 'ArchiveTransferReply'
            || $message->type == 'ArchiveDeliveryRequestReply'
            || $message->type == 'ArchiveModificationNotification'
            || $message->type == 'ArchiveDestructionNotification'
        ) {
            $archive->archiverOrgRegNumber = $archive->depositorOrgRegNumber = (string) $message->senderOrgRegNumber;
            $archive->originatorOrgRegNumber = (string) $message->recipientOrgRegNumber;
            $archive->originatorOwnerOrgId = (string) $message->recipientOrg->orgId;
        } else {
            $archive->archiverOrgRegNumber = $archive->depositorOrgRegNumber = (string) $message->recipientOrgRegNumber;
            $archive->originatorOrgRegNumber = (string) $message->senderOrgRegNumber;
            $archive->originatorOwnerOrgId = (string) $message->senderOrg->orgId;
        }

        $archive->serviceLevelReference = $this->archiveController->useServiceLevel("deposit")->reference;

        $originatorOrgs = [];
        if (!isset($originatorOrgs[$archive->originatorOrgRegNumber])) {
            $originatorOrg = $this->orgController->getOrgByRegNumber($archive->originatorOrgRegNumber);
            $originatorOrgs[$archive->originatorOrgRegNumber] = $originatorOrg;
        } else {
            $originatorOrg = $originatorOrgs[$archive->originatorOrgRegNumber];
        }

        $archive->originatorOwnerOrgId = $originatorOrg->ownerOrgId;

        $context = array_merge(get_object_vars($message), get_object_vars($archive));
        $filePlanPosition = $this->archiveController->resolveStoragePath($context);
        $archived = $this->archiveController->deposit($archive, $filePlanPosition);

        if ($archived) {
            $archivedMessage = new \StdClass();
            $archivedMessage->messageId = $messageId;
            $archivedMessage->archived = true;

            $this->sdoFactory->update($archivedMessage, 'medona/message');

            return $archived;
        }
    }

    /**
     * Get a message with the reference
     * @param string $reference
     *
     * @return mixte Return a message or null if not exist
     */
    public function getByReference($reference)
    {
        //$queryParts = $this->searchMessage(null, $reference);
        $queryParams = array();
        $queryString = $this->searchMessage(
            null,
            $reference,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            $queryParams
        );

        $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        $message = $this->sdoFactory->find('medona/message', $queryString, $queryParams, false, false, $maxResults);

        if (!$message) {
            return null;
        }

        $messages = $this->read($message[0]->messageId);
        if (!$messages) {
            throw new \core\Exception\NotFoundException("The message does not exist");
        }
        
        return $messages;
    }

    /**
     * Find medona message
     * @param mixed  $type   String or array of medona message type
     * @param mixed  $status String or array of medona message status
     * @param string $delay  The interval in past
     *
     * @return array Array of medona/message object
     */
    public function find($type = null, $status = null, $delay = null)
    {
        $queryString = [];

        if ($type) {
            $type = is_array($type) ? $type : array($type);
            $queryString[] = "type=['".\laabs\implode("','", $type)."']";
        }
        if ($status) {
            $status = is_array($status) ? $status : array($status);
            $queryString[] = "status=['".\laabs\implode("','", $status)."']";
        }
        if ($delay) {
            $date = \laabs::newTimestamp()->sub(\laabs::newDuration($delay));
            $queryString[] = "date < '".$date->format("Y-m-d H:i:s")."'";
        }

        $messages = $this->sdoFactory->find("medona/message", \laabs\implode(" AND ", $queryString));

        return $messages;
    }

     /**
     * Apply config task to remove medona messages directories
     *
     * @return array Array of medona message directory removed
     */
    public function messageDirectoryPurge()
    {
        $res = [];

        if (!isset($this->removeMessageTask) || empty($this->removeMessageTask)) {
            return;
        }

        foreach ($this->removeMessageTask as $removeTask) {
            $deletedMessages = $this->removeFromMessageDirectory($removeTask['type'], $removeTask['status'], $removeTask['delay']);
            $res = array_merge($res, $deletedMessages);
        }

        return $res;
    }

    /**
     * Find medona message
     * @param mixte $type   String or array of medona message type
     * @param mixte $status String or array of medona message status
     * @param type  $delai  Delay since creation
     *
     * @return array Array of medona message directory removed
     */
    public function removeFromMessageDirectory($type = null, $status = null, $delai = null)
    {
        $messages = $this->find($type, $status, $delai);

        $removeMessageId = [];

        foreach ($messages as $message) {
            $uri = $this->messageDirectory."/".(string) $message->messageId;

            if (is_dir($uri)) {
                \laabs\rmdir($uri, true);
                $removeMessageId[] = $message->messageId;
            }

            $filenames = glob($uri.'.*');
            foreach ($filenames as $filename) {
                if (is_file($filename)) {
                    unlink($filename);
                }
            }
        }

        return $removeMessageId;
    }

    /**
     * Count messages for an organization
     * @param string $orgRegNumber The organization registration number
     *
     * @return int The number of messages with this organization
     */
    public function countByOrg($orgRegNumber)
    {
        $queryString = [];
        $queryString[] = "senderOrgRegNumber='$orgRegNumber'";
        $queryString[] = "recipientOrgRegNumber='$orgRegNumber'";

        $count = $this->sdoFactory->count("medona/message", \laabs\implode(" OR ", $queryString));

        return $count;
    }

    protected function sendError($code, $message = false, $variable = null)
    {
        if ($message) {
            array_push($this->errors, new \core\Error($message, $variable, $code));
        } else {
            array_push($this->errors, new \core\Error($this->getReplyMessage($code), $variable, $code));
        }

        if ($this->replyCode == null) {
            $this->replyCode = $code;
        }
    }

    protected function getReplyMessage($code)
    {
        switch ((string) $code) {
            case "000":
                $name = "OK";
                break;
            case "001":
                $name = "OK (consulter les commentaires pour information)";
                break;
            case "002":
                $name = "OK (Demande aux autorités de contrôle effectuée)";
                break;

            case "101":
                $name = "Message mal formé.";
                break;
            case "102":
                $name = "Système momentanément indisponible.";
                break;
            case "103":
                $name = "Message déjà reçu.";
                break;
            case "104":
                $name = "Message en erreur.";
                break;

            case "200":
                $name = "Service producteur non reconnu.";
                break;
            case "201":
                $name = "Service d'archive non reconnu.";
                break;
            case "202":
                $name = "Service versant non reconnu.";
                break;
            case "203":
                $name = "Dépôt non conforme au profil de données.";
                break;
            case "204":
                $name = "Format de document non géré.";
                break;
            case "205":
                $name = "Format de document non conforme au format déclaré.";
                break;
            case "206":
                $name = "Signature du message invalide.";
                break;
            case "207":
                $name = "Empreinte(s) invalide(s).";
                break;
            case "208":
                $name = "Archive indisponible. Délai de communication non écoulé.";
                break;
            case "209":
                $name = "Archive absente (élimination, restitution, transfert)";
                break;
            case "210":
                $name = "Archive inconnue";
                break;
            case "211":
                $name = "Pièce attachée absente.";
                break;
            case "212":
                $name = "Dérogation refusée.";
                break;
            case "213":
                $name = "Identifiant de document incorrect";
                break;

            case "300":
                $name = "Convention invalide.";
                break;
            case "301":
                $name = "Dépôt non conforme à la convention. Quota des versements dépassé.";
                break;
            case "302":
                $name = "Dépôt non conforme à la convention. Identifiant du producteur non conforme.";
                break;
            case "303":
                $name = "Dépôt non conforme à la convention. Identifiant du service versant non conforme.";
                break;
            case "304":
                $name = "Dépôt non conforme à la convention. Identifiant du service d'archives non conforme.";
                break;
            case "305":
                $name = "Dépôt non conforme à la convention. Signature(s) de document(s) absente(s).";
                break;
            case "306":
                $name = "Dépôt non conforme à la convention. Volume non conforme.";
                break;
            case "307":
                $name = "Dépôt non conforme à la convention. Format non conforme.";
                break;
            case "308":
                $name = "Dépôt non conforme à la convention. Empreinte(s) non transmise(s).";
                break;
            case "309":
                $name = "Dépôt non conforme à la convention. Absence de signature du message.";
                break;
            case "310":
                $name = "Dépôt non conforme à la convention. Signature(s) de document(s) non valide(s).";
                break;
            case "311":
                $name = "Dépôt non conforme à la convention. Signature(s) de document(s) non vérifiée(s).";
                break;
            case "312":
                $name = "Dépôt non conforme à la convention. Dates de début ou de fin non respectées.";
                break;

            case "400":
                $name = "Demande rejetée.";
                break;
            case "404":
                $name = "Message non trouvé.";
                break;

            default:
                $name = null;
        }

        return $name;
    }
}
