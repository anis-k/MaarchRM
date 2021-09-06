<?php

/*
 * Copyright (C) 2018 Maarch
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
 * archive description html serializer
 *
 * @package RecordsManagement
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class archiveDescription
{
    public $view;
    protected $json;
    protected $translator;

    /**
     * Constuctor
     * @param \dependency\html\Document                    $view
     * @param \dependency\json\JsonObject                  $json
     * @param \dependency\localisation\TranslatorInterface $translator
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator
    )
    {
        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('recordsManagement/messages');
    }

    public function read($descriptionObject, $archivalProfile = null, $descriptionClass = null)
    {        
        if (!is_null($archivalProfile) && is_null($descriptionClass)) {
            $descriptionClass = $archivalProfile->descriptionClass;
        }

        $descriptionFields = $archivalProfileFields = [];

        // Retrieve scheme properties (description fields)
        $descriptionFields = \laabs::callService('recordsManagement/descriptionScheme/read_name_Descriptionfields', $descriptionClass);
        
        if ($archivalProfile && !empty($archivalProfile->archiveDescription)) {
            // Index profile fields and set immutale fields as readonly
            $archivalProfileFieldNames = [];
            foreach ($archivalProfile->archiveDescription as $archiveDescriptionField) {
                if ($archiveDescriptionField->isImmutable) {
                    $descriptionFields[$archiveDescriptionField->fieldName]->readonly = true;
                }
                $archivalProfileFieldNames[] = $archiveDescriptionField->fieldName;
            }
        } else {
            $archivalProfileFieldNames = [];
        }

        // Sort description fields as in profile
        $descriptionFields = $this->sortFields($descriptionFields, $archivalProfileFieldNames);

        $table = $this->getObjectTable($descriptionObject, $descriptionFields);

        return $this->view->saveHtml($table);
    }


    protected function getObjectTable($object, $descriptionFields)
    {
        $table = $this->view->createElement('table');
        $table->setAttribute('class', "table table-condensed table-striped");

        $object = (object) $this->sortFields(get_object_vars($object), array_keys($descriptionFields));
        foreach ($object as $name => $value) {
            $descriptionField = $this->getDescriptionField($name, $value, $descriptionFields);
            if (!$descriptionField) {
                continue;
            }

            if (isset($descriptionField->internal)) {
                continue;
            }

            if (in_array($descriptionField->type, ['object', 'array'])) {
                $descriptionField->readonly = true;
            }

            if ($tr = $this->getTableRow($name, $value, $descriptionField)) {
                $table->appendChild($tr);
            }
        }

        return $table;
    }

    protected function getTableRow($name, $value, $descriptionField)
    {
        $tr = $this->view->createElement('tr');

        if (!empty($name)) {
            $th = $this->getTableHeader($descriptionField);
            $tr->appendChild($th);
        }

        $td = $this->getTableData($value, $descriptionField);
        $tr->appendChild($td);

        return $tr;
    }

    protected function getDescriptionField($name, $value, $descriptionFields)
    {
        if (isset($descriptionFields[$name])) {
            return $descriptionFields[$name];
        } else {
            $actualDatatype = \gettype($value);
            if ($actualDatatype == 'object' || $actualDatatype == 'array') {
                return;
            }

            return $this->getDummyDescriptionField($name, 'text');
        }
    }

    protected function getDummyDescriptionField($name, $type)
    {
        $descriptionField = new \stdClass();
        $descriptionField->name = $name;
        $descriptionField->label = $name;
        $descriptionField->type = $type;
        $descriptionField->additionnal = true;

        return $descriptionField;
    }

    protected function getTableHeader($descriptionField)
    {
        $th = $this->view->createElement('th', $descriptionField->label);
        $th->setAttribute('name', $descriptionField->name);
        if (is_string($descriptionField->type)) {
            $th->setAttribute('data-type', $descriptionField->type);
        }
        if (isset($descriptionField->readonly)) {
            $th->setAttribute('data-readonly', 'readonly');
        }
        if (isset($descriptionField->additionnal)) {
            $th->setAttribute('data-additionnal', 'additionnal');
        }

        return $th;
    }

    protected function getTableData($value, $descriptionField)
    {
        // if I have an external ref in my decription Field, I load it and attributes it its first value
        if (isset($descriptionField->ref) && $descriptionField->ref === true) {
            $value = \laabs::callService('recordsManagement/descriptionRef/read_name__key_', $descriptionField->name, $value)[1];
        }

        $td = $this->view->createElement('td');
        $td->setAttribute('style', 'padding: 0 5px 0 5px');
        $td->setAttribute('data-value', json_encode($value));
        if (!is_null($value)) {
            $valueNode = $this->getValueNode($value, $descriptionField);
            $td->appendChild($valueNode);
        }

        return $td;
    }

    protected function getValueNode($value, $descriptionField)
    {
        switch ($descriptionField->type) {
            case 'boolean':
                return $this->getBooleanChecker($value);

            case 'array':
                return $this->getArrayTable($value, $descriptionField);

            case 'object':
                return $this->getObjectTable($value, $descriptionField->properties);

            case 'date':
                return $this->getDateString($value, $descriptionField);

            default:
                if (is_string($descriptionField->type) && $descriptionField->type[0] == '#') {
                    // Object type is given by ref, assume complex type/object
                    $itemTypeName = substr($descriptionField->itemType, 1);
                    $properties = \laabs::callService('recordsManagement/descriptionScheme/read_name_Descriptionfields', $itemTypeName);

                    return $this->getObjectTable($value, $properties);
                } else {
                    if (!empty($descriptionField->enumeration) && !empty($descriptionField->enumNames)) {
                        $index = array_search($value, $descriptionField->enumeration);
                        if ($index!== false && isset($descriptionField->enumNames[$index])) {
                            $value = $descriptionField->enumNames[$index];
                        }
                    }

                    return $this->view->createTextNode($value);
                }
        }
    }

    protected function getBooleanChecker($boolean)
    {
        $checker = $this->view->createElement('i');
        if (is_null($boolean)) {
            $checker->setAttribute('data-value', '');
        } else {
            if ($boolean) {
                $checker->setAttribute('class', "fa fa-check");
                $checker->setAttribute('data-value', '1');
            } else {
                $checker->setAttribute('class', "fa fa-times");
                $checker->setAttribute('data-value', '0');
            }
        }

        return $checker;
    }

    protected function getArrayTable($array, $descriptionField)
    {
        $table = $this->view->createElement('table');
        if (is_string($descriptionField->itemType) && $descriptionField->itemType[0] == '#') {
            $itemTypeName = substr($descriptionField->itemType, 1);
            $properties = \laabs::callService('recordsManagement/descriptionScheme/read_name_Descriptionfields', $itemTypeName);
            $descriptionField = $this->getDummyDescriptionField('dummy', 'object');
            $descriptionField->properties = $properties;
        } elseif (is_object($descriptionField->itemType)) {
            $descriptionField = $descriptionField->itemType;
        } else {
            $descriptionField = $this->getDummyDescriptionField('dummy', $descriptionField->itemType);
        }

        if (is_scalar($array)) {
            $array = [$array];
        }
        
        foreach ($array as $item) {
            $tr = $this->getTableRow($name = null, $item, $descriptionField, true);
            $table->appendChild($tr);
        }

        return $table;
    }

    protected function getDateString($value, $descriptionField)
    {
        if (is_string($value)) {
            $dateFormat = 'Y-m-d';
            $dateTimeFormat = 'Y-m-d H:i:s';
            $timezone = "";

            $localizationConfig = \laabs::configuration()['dependency.localisation'];

            if (isset($localizationConfig['dateFormat'])) {
                $dateFormat = $localizationConfig['dateFormat'];
            }

            if (isset($localizationConfig['dateTimeFormat'])) {
                $dateTimeFormat = $localizationConfig['dateTimeFormat'];
            }

            if (isset($localizationConfig['timezone'])) {
                $timezone = $localizationConfig['timezone'];
            }

            $parts = date_parse_from_format('Y-m-d\TH:i:s.uP', $value);
            
            if (empty($parts['hour'])) {
                $value = \laabs::newDate($value)->format($dateFormat);
            } else {
                $value = \laabs::newDatetime($value)->format($dateTimeFormat);
            }
        }

        return $this->view->createTextNode($value);
    }

    protected function sortFields($fields, $names)
    {
        $sortedFields = [];

        foreach ($names as $name) {
            if (isset($fields[$name])) {
                $sortedFields[$name] = $fields[$name];
                unset($fields[$name]);
            }
        }

        if (!empty($fields)) {
            foreach ($fields as $name => $value) {
                $sortedFields[$name] = $value;
            }
        }

        return $sortedFields;
    }
}
