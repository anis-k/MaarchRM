<?php
/*
 * Copyright (C) 2019  Maarch
 *
 * This file is part of Maarch RM
 *
 * Maarch RM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Maarch RM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Maarch RM. If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\mades;
/**
 *
 * @author  Maarch Cyril Vazquez <cyril.vazquez@maarch.com>
 */
class message
{
    public $view;

    protected $json;
    protected $translator;

     /**
     * Constuctor of message html serializer
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The JSON dependency
     * @param \dependency\localisation\TranslatorInterface $translator The localisation dependency
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator
    ) {
        $this->view = $view;
        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('medona/messages');
    }

    /**
     * @param $messages
     * @return string
     * @throws \Exception
     */
    public function read($messages)
    {
        foreach ($messages as $message) {
            if (isset($message->object->archivalAgency)) {
                $message->object->archivalAgency->name = $this->loadOrganizationName(
                    $message->object->archivalAgency,
                    $message
                );
            }
            if (isset($message->object->transferringAgency)) {
                $message->object->transferringAgency->name = $this->loadOrganizationName(
                    $message->object->transferringAgency,
                    $message
                );
            }
            if (isset($message->object->controlAuthority)) {
                $message->object->controlAuthority->name = $this->loadOrganizationName(
                    $message->object->controlAuthority,
                    $message
                );
            }
            if (isset($message->object->originatingAgency)) {
                $message->object->originatingAgency->name = $this->loadOrganizationName(
                    $message->object->originatingAgency,
                    $message
                );
            }
            if (isset($message->object->requester)) {
                $message->object->requester->name = $this->loadOrganizationName(
                    $message->object->requester,
                    $message
                );
            }
            if (isset($message->object->sender)) {
                $message->object->sender->name = $this->loadOrganizationName(
                    $message->object->sender,
                    $message
                );
            }
            if (isset($message->object->receiver)) {
                $message->object->receiver->name = $this->loadOrganizationName(
                    $message->object->receiver,
                    $message
                );
            }

            if (isset($message->object->dataObjectPackage->descriptiveMetadata)) {
                $message->object->dataObjectPackage->descriptiveMetadata =
                    get_object_vars($message->object->dataObjectPackage->descriptiveMetadata);
                foreach ($message->object->dataObjectPackage->descriptiveMetadata as $key => $archiveUnit) {
                    if (isset($archiveUnit->management->appraisalRule)) {
                        $appraisalRule = \laabs::callService(
                            'recordsManagement/retentionRule/read_code_/',
                            $archiveUnit->management->appraisalRule->code
                        );

                        if ($appraisalRule) {
                            $dateInter = new \DateInterval($appraisalRule->duration);
                            $numberDuration = 0;
                            $toDisplay = '';

                            if ($dateInter->y != 0) {
                                if ($dateInter->y == 9999) {
                                    $numberDuration = null;
                                    $toDisplay = "Unlimited";
                                } else {
                                    $numberDuration = $dateInter->y;
                                    $toDisplay = "Year(s)";
                                }
                            } elseif ($dateInter->m != 0) {
                                $numberDuration = $dateInter->m;
                                $toDisplay = "Month(s)";
                            } elseif ($dateInter->d != 0) {
                                $numberDuration = $dateInter->d;
                                $toDisplay = "Day(s)";
                            } else {
                                $numberDuration = 0;
                                $toDisplay = "Year(s)";
                            }

                            $archiveUnit->management->appraisalRule->durationNumber = $numberDuration;
                            $archiveUnit->management->appraisalRule->durationToDisplay =
                                $this->translator->getText($toDisplay);
                        }
                    }
                    if (isset($archiveUnit->management->accessRule)) {
                        $accessRule = \laabs::callService(
                            'recordsManagement/accessRule/read_code_/',
                            $archiveUnit->management->accessRule->code
                        );

                        if ($accessRule) {
                            $dateInter = new \DateInterval($accessRule->duration);
                            $numberDuration = 0;
                            $toDisplay = '';

                            if ($dateInter->y != 0) {
                                if ($dateInter->y == 9999) {
                                    $toDisplay = "Unlimited";
                                    $numberDuration = null;
                                } else {
                                    $numberDuration = $dateInter->y;
                                    $toDisplay = "Year(s)";
                                }
                            } elseif ($dateInter->m != 0) {
                                $numberDuration = $dateInter->m;
                                $toDisplay = "Month(s)";
                            } elseif ($dateInter->d != 0) {
                                $numberDuration = $dateInter->d;
                                $toDisplay = "Day(s)";
                            } else {
                                $numberDuration = 0;
                                $toDisplay = "Year(s)";
                            }

                            $archiveUnit->management->accessRule->durationNumber = $numberDuration;
                            $archiveUnit->management->accessRule->durationToDisplay = $this->translator->getText($toDisplay);
                        }
                    }
                    $archivePresenter = \laabs::newPresenter('recordsManagement/archive');
                    $archivalProfile = $this->loadArchivalprofile($archiveUnit->profile);

                    if ($archivalProfile) {
                        $presenter = $archivePresenter->getDescriptionPresenter($archivalProfile->descriptionClass);
                        $descriptionHtml = $presenter->read(
                            $archiveUnit->description,
                            $archivalProfile
                        );

                        $fragment = $this->view->createDocumentFragment();
                        $fragment->appendHTML($descriptionHtml);
                        $archiveUnit->descriptionHTML = $fragment;
                    }

                    if (!isset($archiveUnit->management->serviceLevel)) {
                        $serviceLevel = \laabs::callService(
                            'recordsManagement/serviceLevel/read_Default/'
                        );

                        $archiveUnit->management->serviceLevel = $serviceLevel->reference;
                    }
                }
            } else {
                $message->object->dataObjectPackage = new \stdClass();
                $message->object->dataObjectPackage->descriptiveMetadata = [];
            }

            if (isset($message->object->dataObjectPackage->binaryDataObjects)) {
                $message->object->dataObjectPackage->binaryDataObjects =
                get_object_vars($message->object->dataObjectPackage->binaryDataObjects);
            }
        }
        $this->view->addContentFile("mades/message/message.html");

        if ($this->view->getElementsByClass("dataTable")->item(2)) {
            $dataTable = $this->view->getElementsByClass("dataTable")->item(2)->plugin['dataTable'];
            $dataTable->setPaginationType("full_numbers");
            $dataTable->setSorting(array(array(0, 'desc')));
        }

        $this->view->translate();

        $this->view->setSource("messages", $messages);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    protected function loadArchivalProfile($reference)
    {
        if (!isset($this->archivalProfiles[$reference])) {
            try {
                $this->archivalProfiles[$reference] = \laabs::callService(
                    'recordsManagement/archivalProfile/readProfiledescription_archivalProfileReference_',
                    $reference
                );
            } catch (\Exception $e) {
                return null;
            }
        }

        return $this->archivalProfiles[$reference];
    }

    /**
     * @param $object
     * @param $message
     * @return string|void|null
     */
    protected function loadOrganizationName($object, $message)
    {
        $organization = null;

        if (!isset($object->identifier)) {
            return;
        }

        if ($object->identifier == $message->recipientOrg->registrationNumber) {
            $organization = $message->recipientOrg;
        } elseif ($object->identifier == $message->senderOrg->registrationNumber) {
            $organization = $message->senderOrg;
        } else {
            try {
                $organization = \laabs::callService(
                    'organization/organization/readByregnumber',
                    $object->identifier
                )->orgName;
            } catch (\Exception $e) {
                $organization = "Organisation inconnue (" . $object->identifier . ")";
            }
        }

        return $organization;
    }
}