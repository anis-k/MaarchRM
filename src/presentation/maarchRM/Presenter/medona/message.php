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

namespace presentation\maarchRM\Presenter\medona;

/**
 * Serializer html retentionRule
 *
 * @package RecordsManagement
 * @author  Maarch Prosper DE LAURE <prosper.delaure@maarch.com>
 */
class message
{

    use archiveDeliveryTrait,
        archiveDestructionTrait,
        archiveTransferTrait,
        archiveOutgoingTransferTrait,
        archiveAuthorizationTrait,
        archiveNotificationTrait,
        archiveRestitutionTrait,
        \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    public $sdoFactory;
    protected $json;
    protected $translator;

    protected $dashboardPresenter;
    protected $menu;
    protected $packageSchemas = [];

    /**
     * Constuctor of message html serializer
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\sdo\Factory                      $sdoFactory The SDO dependency
     * @param \dependency\json\JsonObject                  $json       The JSON dependency
     * @param \dependency\localisation\TranslatorInterface $translator The localisation dependency
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\sdo\Factory $sdoFactory,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator
    ) {
        $this->view = $view;
        $this->sdoFactory = $sdoFactory;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('medona/messages');
        $this->menu = \laabs::configuration()['medona']['menu'];

        $this->dashboardPresenter = \laabs::newService('presentation/dashboard');

        if (isset(\laabs::configuration('medona')['packageSchemas'])) {
            $this->packageSchemas = \laabs::configuration('medona')['packageSchemas'];
        }
    }

    /**
     * Dashobord for messages
     */
    public function index()
    {
        $currentService = \laabs::getToken("ORGANIZATION");
        if (!$currentService) {
            $this->view->addContentFile("recordsManagement/welcome/noWorkingOrg.html");
            $this->view->translate();


            return $this->view->saveHtml();
        }
        $this->view->addContentFile("medona/message/menu.html");

        $menu = $this->dashboardPresenter->filterMenuAuth($this->menu);

        $this->view->translate();

        $this->view->setSource('medonaMenu', $menu);

        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * View message description
     * @param medona/message $message The message object
     *
     * @return string
     */
    public function read($message)
    {
        $this->view->addContentFile("medona/message/message.table.html");
        $this->view->translate();

        $this->view->setSource("message", $message);

        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Initialise the history form
     */
    public function initHistoryForm()
    {
        $archivalAgreements = \laabs::callService('medona/archivalAgreement/readIndex');
        $currentService = \laabs::getToken("ORGANIZATION");
        $ownerOriginatorOrgs = [];
        $ownerDepositorOrgs = [];
        $ownerArchiverOrgs = [];

        if ($currentService) {
            $originators = \laabs::callService('organization/organization/readByrole_role_', 'originator');
            $depositors = \laabs::callService('organization/organization/readByrole_role_', 'depositor');
            $archivers = \laabs::callService('organization/organization/readByrole_role_', 'archiver');


            $owner = is_array($currentService->orgRoleCodes) && in_array('owner', $currentService->orgRoleCodes);

            foreach ($originators as $originator) {
                if (!isset($ownerOriginatorOrgs[(string) $originator->ownerOrgId])) {
                    $orgObject = \laabs::callService('organization/organization/read_orgId_', (string) $originator->ownerOrgId);

                    $ownerOriginatorOrgs[(string) $orgObject->orgId] = new \stdClass();
                    $ownerOriginatorOrgs[(string) $orgObject->orgId]->displayName = $orgObject->displayName;
                    $ownerOriginatorOrgs[(string) $orgObject->orgId]->originators = [];
                }

                $ownerOriginatorOrgs[(string) $orgObject->orgId]->originators[] = $originator;
            }
            foreach ($depositors as $depositor) {
                if (!isset($ownerDepositorOrgs[(string) $depositor->ownerOrgId])) {
                    $orgObject = \laabs::callService('organization/organization/read_orgId_', (string) $depositor->ownerOrgId);
                    $ownerDepositorOrgs[(string) $orgObject->orgId] = new \stdClass();
                    $ownerDepositorOrgs[(string) $orgObject->orgId]->displayName = $orgObject->displayName;
                    $ownerDepositorOrgs[(string) $orgObject->orgId]->depositors = [];
                }

                $ownerDepositorOrgs[(string) $orgObject->orgId]->depositors[] = $depositor ;
            }
            foreach ($archivers as $archiver) {
                if (!isset($ownerArchiverOrgs[(string) $archiver->ownerOrgId])) {
                    $orgObject = \laabs::callService('organization/organization/read_orgId_', (string) $archiver->ownerOrgId);
                    $ownerArchiverOrgs[(string) $orgObject->orgId] = new \stdClass();
                    $ownerArchiverOrgs[(string) $orgObject->orgId]->displayName = $orgObject->displayName;
                    $ownerArchiverOrgs[(string) $orgObject->orgId]->archivers = [];
                }

                $ownerArchiverOrgs[(string) $orgObject->orgId]->archivers[] = $archiver ;
            }
        }

        $this->view->setSource('archivalAgreements', $archivalAgreements);
        $this->view->setSource('ownerOriginatorOrgs', $ownerOriginatorOrgs);
        $this->view->setSource('ownerDepositorOrgs', $ownerDepositorOrgs);
        $this->view->setSource('ownerArchiverOrgs', $ownerArchiverOrgs);
    }

    /**
     * get a message's information for history view
     * @param medona/message $message The message objects
     *
     * @return string
     */
    public function displayInHistory($message)
    {
        return $this->display($message, $withActionsButtons = false);
    }

    /**
     * get a message's information
     * @param medona/message $message            The message objects
     * @param boolean        $withActionsButtons Set the action buttons up on screen
     *
     * @return string
     */
    public function display($message, $withActionsButtons = true)
    {
        if (isset(\laabs::configuration('medona')['parentsDerogation']) && \laabs::configuration('medona')['parentsDerogation']) {
            $registrationNumber = \laabs::callService('organization/userPosition/readDescendantservices');
        } else {
            $organization = \laabs::getToken("ORGANIZATION");
            $registrationNumber = array($organization->registrationNumber);
        }

        $unitIdentifiers = [];

        $messages = array($message);
        $baseMessage = reset($messages);
        if (isset($message->acknowledgement)) {
            $messages = array_merge($messages, [$message->acknowledgement]);
        }

        if (isset($message->authorizationMessages)) {
            $messages = array_merge($messages, $message->authorizationMessages);
        }

        if (isset($message->replyMessage)) {
            $messages[] = $message->replyMessage;
        }

        $this->view->addContentFile("medona/message/messageModal.html");

        if ($this->view->getElementsByClass("dataTable")->item(1)) {
            $dataTable = $this->view->getElementsByClass("dataTable")->item(1)->plugin['dataTable'];
            $dataTable->setPaginationType("full_numbers");
            $dataTable->setSorting(array(array(0, 'desc')));
        }

        foreach ($messages as $message) {
            $message->size = number_format($message->size, 0, '', ' ');

            $user = \laabs::callService('auth/userAccount/read_userAccountId_', $message->accountId);
            $message->accountDisplayName = $user->displayName;
            $message->accountName = $user->accountName;

            // Set messages action buttons
            if ($withActionsButtons) {
                $this->setMessageActions($message, $messages, $registrationNumber);
            }

            if (in_array($message->status, ['error', 'processError', 'validationError'])) {
                $message->retryButton = "/medona/message/". $message->messageId . "/retry";
            }

            if (is_array($message->unitIdentifier)) {
                foreach ($message->unitIdentifier as $unitIdentifier) {
                    $unitIdentifiers[(string) $unitIdentifier->objectId] = $unitIdentifier;
                }
            }

            if (isset($this->packageSchemas[$baseMessage->schema])) {
                $schemaConf = $this->packageSchemas[$baseMessage->schema];
                if (isset($schemaConf['label'])) {
                    $message->labelSchema = $schemaConf['label'];
                }
            } else {
                $message->labelSchema = $message->schema;
            }

            if ($baseMessage->schema == 'recordsManagement') {
                if (!isset($descriptionField)) {
                    $descriptionFields = \laabs::callService('recordsManagement/descriptionField/readIndex');
                }

                $message->sender = $message->senderOrg;
                $message->receiver = $message->recipientOrg;

                if (isset($message->binaryDataObject)) {
                    $message->binaryDataObject = (array) $message->binaryDataObject;
                }

                if (isset($message->descriptiveMetadata)) {
                    $message->descriptiveMetadata = (array) $message->descriptiveMetadata;

                    foreach ($message->descriptiveMetadata as $descriptiveMetadata) {
                        if (isset($descriptiveMetadata->descriptionObject)) {
                            $descriptionObjects = $descriptiveMetadata->descriptionObject;
                            $descriptiveMetadata->descriptionObject = [];

                            foreach($descriptionObjects as $key => $value) {
                                $object = new \stdClass();
                                $object->key = $key;

                                foreach ($descriptionFields as $descriptionField) {
                                    if ($descriptionField->name === $key) {
                                        $object->label = $descriptionField->label;
                                    }
                                }

                                $object->value = $value;

                                $descriptiveMetadata->descriptionObject[] = $object;
                            }
                        }
                    }
                }
            }
        }
        end($messages)->last = true;
        
        $messageViewer = $this->view->getElementById('messageViewer');

        $schemaConf = $presenter = null;
        if (isset($this->packageSchemas[$baseMessage->schema])) {
            $schemaConf = $this->packageSchemas[$baseMessage->schema];
            if (isset($schemaConf['presenter'])) {
                $presenter = \laabs::newPresenter($schemaConf['presenter']);
                $messageView = $presenter->read($messages);
                $this->view->addContent($messageView, $messageViewer);
            }
        }
        
        /*if ($baseMessage->schema == 'medona') {
            $this->view->addContentFile('medona/message/message.html', $messageViewer);

            // Append description specific form
            $descriptionMetadataDiv = $this->view->getElementById('descriptiveMetadata');
            if (isset($messages[0]->dataObjectPackage)) {
                $this->view->addContentFile($messages[0]->dataObjectPackage->descriptiveMetadataClass.'.html', $descriptionMetadataDiv);
                if ($messages[0]->dataObjectPackage->descriptiveMetadataClass == 'medona/archivePackage') {
                    foreach ($messages[0]->dataObjectPackage->descriptiveMetadata->archive as $archive) {
                        if (isset($archive->descriptionObject)) {
                            $archive->descriptionObject->json = json_encode($archive->descriptionObject);
                        }
                    }
                }
            }
        }*/

        
        $this->view->setSource('messages', $messages);
        $this->view->setSource('unitIdentifiers', $unitIdentifiers);
        $this->view->merge();

        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * Get a message as a certificate
     * @param medona/message $message The message objects
     *
     * @return string
     */
    public function getCertificate($message)
    {
        $message->last = true;
        $messages = array($message);

        $this->view->addContentFile("medona/message/messageModal.html");
        $this->view->setSource('messages', $messages);
        $this->view->merge();

        $messageSerializer = $this->getMessageTypeSerializer($message);
        $html = $messageSerializer->display($message);
        $html = utf8_decode($html);
        if ($html) {
            $messageContent = $this->view->createDocumentFragment();
            $messageContent->appendHTML($html);

            $messageDiv = $this->view->getElementById($message->messageId);
            $messageDiv->appendChild($messageContent);
        }
        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * Get a form to search resource
     * @param recordsManagement/digitalResource $digitalResource The digital resouce
     *
     * @return string
     */
    public function getDataObjectAttachment($digitalResource)
    {
        $mimetype = $digitalResource->mimetype;
        \laabs::setResponseType($mimetype);

        switch ($mimetype) {
            case 'application/xml':
            case 'text/xml':
                $contents = $digitalResource->getContents();
                $dom = new \DOMDocument();
                $dom->formatOutput = true;

                $dom->loadXml($contents);

                $contents = "<pre>".htmlentities(preg_replace('#\>\n(\s*\n)*#', ">\n", $dom->saveXml()))."</pre>";
                \laabs::setResponseType("text/html");

                return $contents;

            default:
                return $digitalResource->getHandler();
        }
    }

    /**
     * Get a form to search resource
     * @param string $mimetype The mime type
     *
     * @return string
     */
    public function showDataObjectAttachmentsContent($mimetype)
    {
        $this->view->addContentFile("recordsManagement/archive/modalViewDocument.html");
        $this->view->translate();

        //$this->view->setSource('url', $url);
        $this->view->setSource('mimetype', $mimetype);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * List archive conversion request
     *
     * @param array $archivesConversionRequest Array of medona/message object
     *
     * @return string
     */
    public function listConversionRequest($archivesConversionRequest)
    {
        $this->view->addContentFile("medona/message/conversionRequestList.html");

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(3);

        $this->view->translate();

        $this->view->setSource('archivesConversionRequest', $archivesConversionRequest);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Get the message import form
     * @param string $messageZip The message zip
     *
     * @return string
     */
    public function messageExport($messageZip)
    {
        \laabs::setResponseType("application/zip");

        $response = \laabs::kernel()->response;
        $response->setHeader('Content-Disposition', 'attachment; filename="'.func_get_args()[1] . '.zip"');

        return $messageZip;
    }

    //JSON

    /**
     * Get messages numbers
     * @param array $messagesCount Array of number for each message type
     *
     * @return array
     */
    public function countMessages($messagesCount)
    {
        return json_encode($this->count($messagesCount));
    }

    /**
     * Serializer JSON for acceptArchiveTransfer method
     *
     * @return type
     */
    public function acceptArchiveTransfer()
    {
        $this->json->message = $this->translator->getText("Message accepted");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectArchiveTransfer method
     *
     * @return type
     */
    public function rejectArchiveTransfer()
    {
        $this->json->message = $this->translator->getText("Message rejected");

        return $this->json->save();
    }

    /**
     * Serializer JSON for validateArchiveTransfer method
     *
     * @return type
     */
    public function validateArchiveTransfer()
    {
        $this->json->message = $this->translator->getText("Message validated");

        return $this->json->save();
    }

    /**
     * Serializer JSON for processBash method
     *
     * @return type
     */
    public function processArchiveTransfer()
    {
        $this->json->message = $this->translator->getText("Message processed");

        return $this->json->save();
    }

    /**
     * Serializer JSON for derogationDeliveryRequest method
     *
     * @return type
     */
    public function derogationDeliveryRequest()
    {
        $this->json->message = $this->translator->getText("Message sent to derogation");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectArchiveTransfer method
     *
     * @return type
     */
    public function rejectDeliveryRequest()
    {
        $this->json->message = $this->translator->getText("Message rejected");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectArchiveTransfer method
     *
     * @return type
     */
    public function rejectArchiveDestructionRequest()
    {
        $this->json->message = $this->translator->getText("Message rejected");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectArchiveTransfer method
     *
     * @return type
     */
    public function validateArchiveDestructionRequest()
    {
        $this->json->message = $this->translator->getText("Message accepted");

        return $this->json->save();
    }


    /**
     * Serializer JSON for sendAuthorizationOriginatingAgencyRequest method
     *
     * @return type
     */
    public function sendAuthorizationOriginatingAgencyRequest()
    {
        $this->json->message = $this->translator->getText("Authorization request send to originator agency");

        return $this->json->save();
    }

    /**
     * Serializer JSON for sendAuthorizationControlAuthorityRequest method
     *
     * @return type
     */
    public function sendAuthorizationControlAuthorityRequest()
    {
        $this->json->message = $this->translator->getText("Authorization request send to control authority");

        return $this->json->save();
    }

    /**
     * Serializer JSON for acceptAuthorizationOriginatingAgencyRequest method
     *
     * @return type
     */
    public function acceptAuthorizationOriginatingAgencyRequest()
    {
        $this->json->message = $this->translator->getText("Authorization accepted");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectAuthorizationOriginatingAgencyRequest method
     *
     * @return type
     */
    public function rejectAuthorizationOriginatingAgencyRequest()
    {
        $this->json->message = $this->translator->getText("Authorization rejected");

        return $this->json->save();
    }

    /**
     * Serializer JSON for acceptAuthorizationControlAuthorityRequest method
     *
     * @return type
     */
    public function acceptAuthorizationControlAuthorityRequest()
    {
        $this->json->message = $this->translator->getText("Authorization accepted");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectAuthorizationControlAuthorityRequest method
     *
     * @return type
     */
    public function rejectAuthorizationControlAuthorityRequest()
    {
        $this->json->message = $this->translator->getText("Authorization rejected");

        return $this->json->save();
    }

    /**
     * Serializer JSON for acceptArchiveTransfer method
     *
     * @return type
     */
    public function retry()
    {
        $this->json->message = $this->translator->getText("Message restarted");

        return $this->json->save();
    }

    /**
     * Serializer JSON for invalid message exception
     * @param Exception $exception The exception
     *
     * @return object JSON object with a status
     */
    public function invalidMessageException($exception)
    {
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getFormat());
        $this->json->message = vsprintf($this->json->message, $exception->getVariables());

        if (count($exception->errors)) {
            foreach ($exception->errors as $error) {
                if (method_exists($error,'getFormat')) {
                    $format = $this->translator->getText($error->getFormat());
                    $error->setMessage(vsprintf($format, $error->getVariables()));
                }
            }
        }
        $this->json->errors = $exception->errors;

        return $this->json->save();
    }

    /**
     * Serializer JSON for invalid status exception
     * @param Exception $exception The exception
     *
     * @return object JSON object with a status
     */
    public function invalidStatusException(\Exception $exception)
    {
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getMessage());

        return $this->json->save();
    }

    /**
     * Serializer JSON for receive method
     * @param medona/message $ack The ack message object
     *
     * @return object JSON object with a status
     */
    public function receive($ack)
    {
        $this->json->status = true;
        $this->json->message = $this->translator->getText("Message received");
        $this->json->messageId = (string) $ack->receivedMessageId;
        if (isset($ack->xml)) {
            $this->json->acknowledgement = $ack->xml->saveXML();
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for validate method
     *
     * @return object JSON object with a status
     */
    public function validate()
    {
        $this->json->status = true;
        $this->json->message = $this->translator->getText("Message received");

        return $this->json->save();
    }

    /**
     * Load organization name
     * @param object $message            The message
     * @param object $messages           The message list
     * @param string $registrationNumber The current user registration number
     */
    protected function setMessageActions($message, $messages, $registrationNumber)
    {
        if (in_array($message->recipientOrgRegNumber, $registrationNumber)) {
            $messageId = (string) $message->messageId;

            switch ($message->type) {
                case 'ArchiveTransfer':
                    if ($message->status == "received" || $message->status == "modified") {
                        if (!$message->isIncoming) {
                            $message->acknowledgeButton = "/restitution/".$messageId."/Acknowledge";
                        }
                        $message->validateButton = "/transferValidate/" . $messageId;
                        $message->exportButton = "/medona/message/".$messageId."/Export";
                    } elseif ($message->status == "valid") {
                        $message->acceptButton = "/transferAcceptance/".$messageId;
                        $message->rejectButton = "/transferRejection/".$messageId;
                        $message->exportButton = "/medona/message/".$messageId."/Export";
                    } elseif ($message->status == "toBeModified") {
                        $message->rejectButton = "/transferRejection/".$messageId;
                        $message->retryButton = "/medona/message/". $message->messageId . "/retry";
                        if ($message->schema === 'seda') {
                            $message->modifyButton = "/transferModify/" . $message->messageId;
                        }
                    } elseif ($message->status == "accepted") {
                        $message->processButton = "/transferProcess/".$messageId;
                        $message->exportButton = "/medona/message/".$messageId."/Export";
                    } elseif ($message->status == "sent") {
                        $message->exportButton = "/outgoingTransfer/".$messageId."/Export";
                    } elseif ($message->status == "downloaded") {
                        $message->acknowledgeButton = "/outgoingTransfer/".$messageId."/Acknowledge";
                        $message->rejectButton = "/outgoingTransfer/".$messageId."/Reject";
                        $message->exportButton = "/medona/message/".$messageId."/Export";
                    } elseif ($message->status == "processing") {
                        $message->retryButton = "/medona/message/". $message->messageId . "/retry";
                    }
                    break;

                case 'Acknowledgement':
                    $message->exportButton = "/medona/message/".$messageId."/Export";
                    break;

                case 'ArchiveDeliveryRequest':
                    if (count($messages) == 1) {
                        if ($message->status === "sent") {
                            $message->rejectButton = "/delivery/".$messageId."/Reject";
                            $message->derogationButton = "/delivery/".$messageId."/Derogation";
                        }
                    }
                    if ($message->status == "accepted") {
                        $message->processButton = "/delivery/".$messageId."/process";
                    }
                    break;

                case 'ArchiveDeliveryRequestReply':
                    if (count($messages) == 1) {
                        if ($message->status == "sent") {
                            $message->exportButton = "/delivery/".$messageId."/Export";
                        }
                    }
                    break;

                case 'ArchiveRestitutionRequest':
                    if (count($messages) == 1) {
                        if ($message->status == "sent") {
                            $message->rejectButton = "/restitutionRequest/".$messageId."/Reject";
                            $message->acceptWarningButton = "/restitutionRequest/".$messageId."/Accept";
                        }
                    } elseif ($message->status == "accepted") {
                        $message->processButton = "/restitutionRequest/".$messageId."/process";
                    }
                    break;

                case 'ArchiveModificationRequest':
                    if ($message->status == "received") {
                        $message->rejectButton = "/modificationRequest/".$messageId."/Reject";
                        $message->acceptWithCommentButton = "/modificationRequest/".$messageId."/accept";
                    }
                    break;

                case 'ArchiveRestitution':
                    if ($message->status == "sent") {
                        $message->exportButton = "/restitution/".$messageId."/Export";
                    } elseif ($message->status == "received") {
                        $message->rejectButton = "/restitution/".$messageId."/Reject";
                        $message->acknowledgeButton = "/restitution/".$messageId."/Acknowledge";
                    } elseif ($message->status == "acknowledge") {
                        $message->validationButton = "/restitution/".$messageId."/process";
                        $message->exportButton = "/medona/message/".$messageId."/Export";
                    }
                    break;

                case 'ArchiveDestructionRequest':
                    if ($message->status == "accepted") {
                        $message->validationButton = "/destructionRequest/".$messageId."/Accept";
                        //$message->rejectButton = "/destructionRequest/".(string) $message->messageId."/Reject";
                    }
                    break;

                case 'AuthorizationOriginatingAgencyRequest':
                    $message->acceptButton = "/authorizationOriginatingAgencyRequest/".(string) $message->messageId."/Accept";
                    $message->rejectButton = "/authorizationOriginatingAgencyRequest/".(string) $message->messageId."/Reject";
                    break;

                case 'AuthorizationOriginatingAgencyRequestReply':
                    if (($messages[0]->status != "originator_authorization_wait" && $messages[0]->status != "control_authorization_wait") || ($message != end($messages))) {
                        break;
                    }

                    if (strtoupper($message->replyCode) == "000") {
                        if ($messages[0]->type == "ArchiveDeliveryRequest") {
                            $message->acceptButton = "/deliveryRequest/".(string) $messages[0]->messageId."/Accept";
                            $message->sendRequestAuth = "/authorizationControlAuthorityRequest/".(string) $messages[0]->messageId;
                        }

                        if ($message[0]->type == "ArchiveDestructionRequest") {
                            $message->acceptButton = "/destructionRequest/".(string) $messages[0]->messageId."/Accept";
                            $message->sendRequestAuth = "/authorizationControlAuthorityRequest/".(string) $messages[0]->messageId;
                        }
                    } else {
                        if ($messages[0]->type == "ArchiveDeliveryRequest") {
                            $message->rejectButton = "/deliveryRequest/".$messageId."/Reject";
                        }

                        if ($messages[0]->type == "ArchiveDestructionRequest") {
                            $message->rejectButton = "/destructionRequest/".(string) $messages[0]->messageId."/Reject";
                        }
                    }
                    break;

                case 'AuthorizationControlAuthorityRequest':
                    $message->acceptButton = "/authorizationControlAuthorityRequest/".(string) $message->messageId."/Accept";
                    $message->rejectButton = "/authorizationControlAuthorityRequest/".(string) $message->messageId."/Reject";
                    break;

                case 'AuthorizationControlAuthorityRequestReply':
                    if (strtoupper($message->replyCode) == "000") {
                        if ($messages[0]->type == "ArchiveDeliveryRequest") {
                            $message->acceptButton = "/deliveryRequest/".(string) $messages[0]->messageId."/Accept";
                        }
                    } else {
                        if ($messages[0]->type == "ArchiveDeliveryRequest") {
                            $message->rejectButton = "/deliveryRequest/".(string) $messages[0]->messageId."/Reject";
                        }
                    }
                    break;
                /*
                */
            }
        } elseif (in_array($message->senderOrgRegNumber, $registrationNumber)) {
            $messageId = (string) $message->messageId;

            switch ($message->type) {
                case 'ArchiveRestitution':
                    if ($message->status == "acknowledge") {
                        $message->validationButton = "/restitution/".$messageId."/process";
                    }
                    break;
                case 'ArchiveTransfer':
                    if ($message->status == "acknowledge") {
                        $message->validationButton = "/outgoingTransfer/".$messageId."/process";
                    }
                    break;
            }
        }
    }

    /**
     * Load organization name
     * @param object         $object
     * @param medona/message $message
     */
    protected function loadOrganizationName($object, $message)
    {
        $organization = null;

        if (!isset($object->identifier) && !isset($object->identification->value)) {
            return;
        }

        if (isset($object->identifier)) {
            $name = 'identifier';
        } else {
            $name = 'identification';
        }

        if ($object->{$name}->value == $message->recipientOrg->registrationNumber) {
            $organization = $message->recipientOrg;
        } elseif ($object->{$name}->value == $message->senderOrg->registrationNumber) {
            $organization = $message->senderOrg;
        } else {
            try {
                $organization = \laabs::callService('organization/organization/readByregnumber', $object->{$name}->value)->orgName;
            } catch (\Exception $e) {
                $organization = "Organisation inconnue (" . $object->{$name}->value . ")";
            }
        }

        return $organization;
    }

    /**
     * Get the message type and schema specific serializer
     * @param medona/message $message The message object
     * @param string         $format  The implementation format
     *
     * @return The parser
     */
    protected function getMessageTypeSerializer($message, $format = "html")
    {
        if (!isset($message->type)) {
            $message->type = $message->xml->documentElement->nodeName;
        }

        if (!isset($message->schema)) {
            $messageNamespace = $message->xml->documentElement->namespaceURI;
            if (!$messageSchema = \laabs::resolveXmlNamespace($messageNamespace)) {
                throw new \Exception('Unknown message namespace'.$messageNamespace);
            }
            $message->schema = $messageSchema;
        }

        $namespace = \laabs::configuration("medona")["packageSchemas"][$message->schema]["phpNamespace"];
        $this->messageTypeSerializer = \laabs::newSerializer($namespace.LAABS_URI_SEPARATOR.$message->type, $format);

        return $this->messageTypeSerializer;
    }

    /**
     * Show message list
     * @param array     $messages Array of message object
     * @param boolean   $history  Type of table (true => history and false => other)
     *
     * @return string The view
     */
    protected function prepareMesageList($messages, $history = null)
    {

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");

        if ($history) {
            $dataTable->setUnsortableColumns(6);
            $dataTable->setSorting(array(array(4, 'desc')));
        } else {
            $dataTable->setUnsortableColumns(5);
            $dataTable->setSorting(array(array(3, 'desc')));
        }

        $this->view->translate();

        foreach ($messages as $message) {
            $message->status = $this->view->translator->getText($message->status, false, "medona/messages");
            $message->typeTranslate = $this->view->translator->getText($message->type, false, "medona/messages");
        }

        $this->loadSendingModal(false, false);
        $this->view->setSource("messages", $messages);
    }

    /**
     * load sending modal
     * @param type $showOrganization
     * @param type $showReference
     */
    protected function loadSendingModal($showOrganization = true, $showReference = true)
    {
        if ($showOrganization) {
            $orgController = \laabs::newController('organization/organization');

            $organizations = $orgController->index();
            $userOrg = $orgController->listMyOrg();
            $currentOrg = \laabs::getToken("ORGANIZATION");
            if ($currentOrg) {
                foreach ($userOrg as $key => $org) {
                    if ($currentOrg->orgId == $org->orgId) {
                        unset($userOrg[$key]);
                        break;
                    }
                }
            }
            if (count($userOrg) < 2) {
                $userOrg = null;
            }
            $this->view->setSource('organizations', $organizations);
            $this->view->setSource('currentOrg', $currentOrg);
            $this->view->setSource('userOrg', $userOrg);
        }

        $this->view->setSource('reference', $showReference);
    }

    /**
     * Get numbers of messages
     */
    protected function count($messagesCount)
    {
        foreach ($messagesCount as $key => $tab) {
            foreach ($tab as $list => $number) {
                if ($number == "0") {
                    $number = "";
                    $messagesCount[$key][$list] = "";
                }
            }
        }

        return $messagesCount;
    }


    /**
     * Wrong archive infos exceptions
     * @param object $exception The exception
     *
     * @return string String serialized in JSON
     */
    public function notDisposableArchiveException($exception)
    {
        $this->translator->setCatalog('recordsManagement/messages');

        // Manage errors
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getMessage());

        return $this->json->save();
    }
}
