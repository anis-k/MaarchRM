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
 * Serializer html adminArchivalAgreement
 *
 * @package medona
 * @author  Maarch Prosper DE LAURE <prosper.delaure@maarch.com>
 */
class archivalAgreement
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;

    protected $json;

    protected $translator;

    /**
     * Constuctor of archival Agreement html serializer
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The view
     * @param \dependency\localisation\TranslatorInterface $translator The view
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json, \dependency\localisation\TranslatorInterface $translator)
    {
        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('medona/archivalAgreement');
    }

    /**
     * Get archival Agreements
     * @param array $archivalAgreements Array of archival Agreements
     *
     * @return string
     */
    public function index(array $archivalAgreements)
    {
        $this->view->addContentFile('medona/archivalAgreement/index.html');

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(2);

        $this->view->translate();

        $this->view->setSource("archivalAgreements", $archivalAgreements);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * The view to create or edit a archival Agreement
     * @param medona/archivalAgreement $archivalAgreement The archival Agreement object
     *
     * @return string
     */
    public function edit($archivalAgreement)
    {
        $this->view->addContentFile('medona/archivalAgreement/edit.html');

        if (!empty($archivalAgreement->allowedFormats)) {
            $formatsList = explode(' ', trim($archivalAgreement->allowedFormats));
            $archivalAgreement->allowedFormats = array();

            $formatController = \laabs::newController('digitalResource/format');

            foreach ($formatsList as $format) {
                if ($format) {
                    array_push($archivalAgreement->allowedFormats, $formatController->get($format));
                }
            }
        }

        $archivalProfiles = \laabs::callService('recordsManagement/archivalProfile/readIndex');
        $serviceLevels = \laabs::callService('recordsManagement/serviceLevel/readIndex');
        $depositors = \laabs::callService('organization/organization/readByrole_role_', "depositor");
        $originators = \laabs::callService('organization/organization/readByrole_role_', "originator");
        $archivers = \laabs::callService('organization/organization/readByrole_role_', "archiver");

        $ownerOrgs = array();
        foreach ($originators as $originator) {
            if (!isset($ownerOrgs[(string) $originator->ownerOrgId])) {
                $ownerOrgs[(string) $originator->ownerOrgId] = \laabs::callService('organization/organization/read_orgId_', (string) $originator->ownerOrgId);
            }
            $ownerOrgs[(string) $originator->ownerOrgId]->originators[] = $originator;
        }

        $depositorOrgs = array();
        foreach ($depositors as $depositor) {
            if (!isset($depositorOrgs[(string) $depositor->ownerOrgId])) {
                $depositorOrgs[(string) $depositor->ownerOrgId] = \laabs::callService('organization/organization/read_orgId_', (string) $depositor->ownerOrgId);
            }
            $depositorOrgs[(string) $depositor->ownerOrgId]->depositors[] = $depositor;
        }

        $archiversOrgs = array();

        foreach ($archivers as $archiver) {
            if (!isset($archiversOrgs[(string) $archiver->ownerOrgId])) {
                $archiversOrgs[(string) $archiver->ownerOrgId] = \laabs::callService('organization/organization/read_orgId_', (string) $archiver->ownerOrgId);
            }
            $archiversOrgs[(string) $archiver->ownerOrgId]->archivers[] = $archiver;
        }

        foreach ($ownerOrgs as $ownerOrg) {
            $allChecked = 0;
            foreach ($ownerOrg->originators as $originator) {
                $checked = false;
                if($archivalAgreement->originatorOrgIds){
                    foreach ($archivalAgreement->originatorOrgIds as $originatorOrgId) {
                        if($originatorOrgId == $originator->orgId) {
                            $checked = true;
                            $allChecked++;
                        }
                        $originator->serviceChecked = $checked;
                    }
                }
            }

            if($allChecked == count($ownerOrg->originators)) {
                $ownerOrg->allChecked = true;
            }
            else if($allChecked == 0){
                $ownerOrg->allUnChecked = true;
            }
        }

        $this->view->translate();
        $this->view->setSource("archivalProfiles", $archivalProfiles);
        $this->view->setSource("serviceLevels", $serviceLevels);
        $this->view->setSource("depositors", $depositorOrgs);
        $this->view->setSource("originators", $ownerOrgs);
        $this->view->setSource("archivers", $archivers);
        $this->view->merge($this->view->getElementById("archivalProfileReference"));
        $this->view->merge($this->view->getElementById("serviceLevelReference"));
        $this->view->merge($this->view->getElementById("depositorOrgRegNumber"));
        $this->view->merge($this->view->getElementById("allOriginator"));
        $this->view->merge($this->view->getElementById("archiverOrgRegNumber"));

        $this->view->setSource("archivalAgreement", $archivalAgreement);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    //JSON
    /**
     * Serializer JSON for create method
     *
     * @return object JSON object with a status and message parameters
     */
    public function create()
    {
        $this->json->message = "Archival agreement created";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for update method
     *
     * @return object JSON object with a status and message parameters
     */
    public function update()
    {
        $this->json->message = "Archival agreement updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for delete method
     *
     * @return object JSON object with a status and message parameters
     */
    public function delete()
    {
        $this->json->message = "Archival agreement deleted";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    protected function listProperties($class, &$properties, &$dateProperties, $containerClass = '')
    {
        foreach ($class->getProperties() as $property) {
            $type = $property->getType();
            if ($type == "date" || $type == "timestamp") {
                array_push($dateProperties, $containerClass.$property->name);
            }
            array_push($properties, $containerClass.$property->name);
            if (!$property->isSimple()) {
                $this->listProperties($property, $properties, $dataProperties, $containerClass.$property->name.'/');
            }
        }
    }
}
