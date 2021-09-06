<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\recordsManagement;

/**
 * Serializer html storageRule
 *
 * @package RecordsManagement
 * @author  Maarch Dylan CORREIA LOPES <dylan.correia@maarch.com>
 */
class storageRule
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;

    protected $json;
    protected $translator;

    /**
     * Constuctor of archival Agreement html serializer
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The json service
     * @param \dependency\localisation\TranslatorInterface $translator The translator service
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
        $this->translator->setCatalog('recordsManagement/storageRule');
    }

    /**
     * Show storage rules list
     * @param array $retentionRule Array of storage rule object
     *
     * @return string
     */
    public function index($storageRule)
    {
        $this->view->addContentFile('recordsManagement/storageRule/index.html');
        $this->view->translate();

        $dataTable = $this->view->getElementById("rulesTable")->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(4);

        foreach ($storageRule as $rule) {
            $rule->durationUnit = substr($rule->duration, -1);
            $rule->duration = substr($rule->duration, 1, -1);

            if($rule->duration >= 9999 && $rule->durationUnit == 'Y' ){
                $rule->durationText = $this->view->translator->getText('Unlimited', null, "recordsManagement/storageRule");
            } else {
                $rule->durationUnit = $this->view->translator->getText($rule->durationUnit, "duration", "recordsManagement/storageRule");
                $rule->durationText =  $rule->duration.' '.  $rule->durationUnit;
            }
        }


        $this->view->setSource('storageRule', $storageRule);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show storage rules list
     * @param array $storageRule Array of retention rule object
     *
     * @return string
     */
    public function listRules($storageRule)
    {
        $this->view->addContentFile('recordsManagement/storageRule/index.html');
        $this->view->translate();

        $dataTable = $this->view->getElementById("rulesTable")->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(array(4));

        foreach ($storageRule as $rule) {
            $rule->durationUnit = substr($rule->duration, -1);
            $rule->duration = substr($rule->duration, 1, -1);

            if($rule->duration >= 9999 && $rule->durationUnit == 'Y' ){
                $rule->durationText = $this->view->translator->getText('Unlimited', null, "recordsManagement/retentionRule");
            } else {

                $rule->durationUnit = $this->view->translator->getText($rule->durationUnit, "duration", "recordsManagement/retentionRule");
                $rule->durationText =  $rule->duration.' '.  $rule->durationUnit;
            }
        }

        $this->view->setSource('storageRule', $storageRule);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    //JSON
    /**
     * Serializer JSON for edit method
     * @param recordsManagement/storageRule $storageRule The storage rule object
     *
     * @return object JSON object
     */
    public function edit($storageRule)
    {

        $storageRule->durationUnit = substr($storageRule->duration, -1);
        $storageRule->duration = substr($storageRule->duration, 1, -1);

        return json_encode($storageRule);
    }

    /**
     * Serializer JSON for create method
     *
     * @return object JSON object with a status and message parameters
     */
    public function create()
    {
        $this->json->message = "Storage rule created.";
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
        $this->json->message = "Storage rule updated.";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for delete method
     * @param bool $response The result of the operation
     *
     * @return object JSON object with a status and message parameters
     */
    public function delete($response)
    {
        $this->json->status = $response;

        if ($response) {
            $this->json->message = "Storage rule deleted.";
        } else {
            $this->json->message = "The operation could not be completed because the storage rule doesn't exist.";
        }
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for SDO exception
     *
     * @return type
     */
    public function sdoException()
    {
        $this->json->status = false;
        $this->json->message = "Error with the Data system, so the operation could not be completed.";
        $this->translator->setCatalog('recordsManagement/exception');
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Exception
     * @param recordsManagement/Exception/storageRuleException $storageRuleException
     *
     * @return string
     */
    public function retentionRuleException($storageRuleException)
    {
        $this->json->status = false;
        $this->json->message = $this->translator->getText($storageRuleException->getMessage());

        return $this->json->save();
    }
}
