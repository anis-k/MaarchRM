<?php
/*
 * Copyright (C) 2019 Maarch
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
namespace bundle\recordsManagement\Controller;

/**
 * Control of the recordsManagement descriptionClass
 *
 * @package recordsManagement
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class descriptionScheme
{
    protected $descriptionSchemes = [];

    /**
     * Constructor
     * @param array $descriptionSchemes
     *
     * @return void
     */
    public function __construct($descriptionSchemes)
    {
        if (!is_null($descriptionSchemes)) {
            $this->descriptionSchemes = get_object_vars(json_decode(json_encode($descriptionSchemes)));
        }
    }

    /**
     * Get the description classes
     *
     * @return array The list of organization's roles
     */
    public function index()
    {
        $schemes = $this->descriptionSchemes;

        if (isset($schemes['extension'])) {
            unset($schemes['extension']);
        }

        return $schemes;
    }

    /**
     * Get the description class
     * @param string $name
     *
     * @return object
     */
    public function read($name)
    {
        if (isset($this->descriptionSchemes[$name])) {
            return $this->descriptionSchemes[$name];
        }
    }

    /**
     * Get the description class properties
     * @param string $name
     *
     * @return array
     */
    public function getDescriptionFields($name = false)
    {
        if (empty($name)) {
            $fields = \laabs::newController('recordsManagement/descriptionField')->index();
            if (isset($this->descriptionSchemes['extension'])) {
                $descriptionSchemeExtension = $this->descriptionSchemes['extension'];
            }
        } else {
            if (isset($this->descriptionSchemes[$name])) {
                $descriptionSchemeConfig = $this->descriptionSchemes[$name];
                if (isset($descriptionSchemeConfig->extension)) {
                    $descriptionSchemeExtension = $descriptionSchemeConfig->extension;
                }
            } elseif (strpos($name, '/') !== false) {
                $descriptionSchemeConfig = new \stdClass();
                $descriptionSchemeConfig->type = 'php';
                $descriptionSchemeConfig->uri = $name;
            }
            switch ($descriptionSchemeConfig->type) {
                case 'php':
                    $fields = $this->getDescriptionFieldsFromPhpClass($descriptionSchemeConfig->uri);
                    break;

                case 'json':
                    $fields = $this->getDescriptionFieldsFromJsonSchema($descriptionSchemeConfig->uri);
                    break;

                default:
                    $fields = [];
            }
        }

        if (isset($descriptionSchemeExtension)) {
            switch ($descriptionSchemeExtension->type) {
                case 'php':
                    $extendedFields = $this->getDescriptionFieldsFromPhpClass($descriptionSchemeExtension->uri);
                    break;

                case 'json':
                    $extendedFields = $this->getDescriptionFieldsFromJsonSchema($descriptionSchemeExtension->uri);
                    break;
            }

            $fields = array_merge($fields, $extendedFields);
        }
        
        return $fields;
    }

    protected function getDescriptionFieldsFromPhpClass($name)
    {
        $descriptionClass = \laabs::getClass($name);
        $fields = [];

        foreach ($descriptionClass->getProperties() as $descriptionProperty) {
            if (!$descriptionProperty->isPublic()) {
                continue;
            }
            $fields[$descriptionProperty->name] = $this->getDescriptionFieldFromPhpClass($descriptionProperty);
        }

        return $fields;
    }

    protected function getDescriptionFieldFromPhpClass($schemeProperty)
    {
        $descriptionField = \laabs::newInstance('recordsManagement/descriptionField');
        $descriptionField->name = $schemeProperty->name;

        if (!empty($schemeProperty->summary)) {
            $descriptionField->label = $schemeProperty->summary;
        } else {
            $descriptionField->label = $schemeProperty->name;
        }

        $type = $schemeProperty->getType();
        $descriptionField->default = $schemeProperty->getDefault();

        if (isset($schemeProperty->enumeration)) {
            $descriptionField->enumeration = $schemeProperty->enumeration;
            if (isset($schemeProperty->tags['enumNames'])) {
                @eval('$enumNames = '.trim($schemeProperty->tags['enumNames'][0]).';');
                if (isset($enumNames) && isset($descriptionField->enumeration) && count($enumNames) == count($descriptionField->enumeration)) {
                    $descriptionField->enumNames = $enumNames;
                }
            }
        }

        if (isset($schemeProperty->tags['ref'])) {
            $descriptionField->ref = $schemeProperty->tags['ref'][0];
        }

        if (isset($schemeProperty->tags['internal'])) {
            $descriptionField->internal = true;
        }

        if (isset($schemeProperty->tags['readonly'])) {
            $descriptionField->readonly = true;
        }

        if (isset($schemeProperty->tags['required'])) {
            $descriptionField->required = true;
        }

        switch (true) {
            case substr($type, -2) == '[]':
                $descriptionField->type = 'array';
                $itemType = substr($type, 0, -2);
                $descriptionField->itemType = $this->getPhpPropertyTypeName($itemType);
                break;

            case $type == 'string':
                if (isset($schemeProperty->tags['scheme'])
                    || isset($schemeProperty->tags['uses'])
                    || isset($schemeProperty->enumeration)
                    || isset($schemeProperty->index)
                    || isset($schemeProperty->tags['ref'])) {
                    $descriptionField->type = 'name';
                } else {
                    $descriptionField->type = 'text';
                }
                break;

            case strpos($type, '/') !== false:
                $descriptionField->type = 'object';
                $descriptionField->properties = $this->getDescriptionFieldsFromPhpClass($type);
                break;

            default:
                $descriptionField->type = $this->getPhpPropertyTypeName($type);
        }

        return $descriptionField;
    }

    protected function getPhpPropertyTypeName($type)
    {
        switch (true) {
            case $type == 'string':
            case $type == 'id':
                return 'text';

            case $type == 'int':
            case $type == 'integer':
            case $type == 'float':
            case $type == 'real':
            case $type == 'double':
                return 'number';

            case $type == 'bool':
            case $type == 'boolean':
                return 'boolean';

            case $type == 'date':
            case $type == 'timestamp':
            case $type == 'datetime':
                return 'date';

            case strpos($type, '/') !== false:
                return '#'.$type;

            default:
                return $type;
        }
    }

    protected function getDescriptionFieldsFromJsonSchema($uri)
    {
        $schema = json_decode(file_get_contents($uri));
        
        $fields = $this->getJsonObjectProperties($schema);

        return $fields;
    }

    protected function getJsonObjectProperties($schema)
    {
        $fields = [];

        foreach ($schema->properties as $name => $property) {
            $fields[$name] = $this->getDescriptionFieldFromJsonSchema($name, $property);
        }

        if (isset($schema->requiredProperties)) {
            foreach ($schema->requiredProperties as $requiredProperty) {
                if (isset($fields[$requiredProperty])) {
                    $fields[$requiredProperty]->required = true;
                }
            }
        }

        return $fields;
    }

    protected function getDescriptionFieldFromJsonSchema($name, $property)
    {
        $descriptionField = \laabs::newInstance('recordsManagement/descriptionField');
        $descriptionField->name = $name;

        if (isset($property->title)) {
            $descriptionField->label = $property->title;
        } else {
            $descriptionField->label = $name;
        }

        if (isset($property->default)) {
            $descriptionField->default = $property->default;
        }

        if (isset($property->enum)) {
            $descriptionField->enumeration = $property->enum;
            if (isset($property->enumNames)) {
                if (count($property->enumNames) == count($descriptionField->enumeration)) {
                    $descriptionField->enumNames = $property->enumNames;
                }
            }
        }

        if (isset($property->facets)) {
            $facets = $property->facets;
            foreach ($facets as $key => $value) {
                $descriptionField->$key = $value;
            }
        }

        if (isset($property->ref)) {
            $descriptionField->ref = $property->ref;
        }

        if (isset($property->readonly)) {
            $descriptionField->readonly = true;
        }

        switch ($property->type) {
            case 'array':
                $descriptionField->type = 'array';
                if (isset($property->items)) {
                    $descriptionField->itemType = $this->getJsonType($property->items);
                }

                break;

            case 'string':
                if (isset($property->enum)
                    || isset($property->ref)) {
                    $descriptionField->type = 'name';
                } else {
                    $descriptionField->type = 'text';
                }
                break;

            case 'object':
                $descriptionField->type = 'object';
                $descriptionField->properties = $this->getJsonObjectProperties($property);
                break;

            default:
                $descriptionField->type = $this->getJsonTypeName($property);
        }

        return $descriptionField;
    }

    protected function getJsonTypeName($type)
    {
        switch (true) {
            case isset($type->format) && ($type->format == 'date' || $type->format == 'dateTime'):
                return 'date';

            case $type->type == 'string':
                return 'text';

            case $type->type == 'integer':
            case $type->type == 'number':
                return 'number';

            default:
                return $type->type;
        }
    }

    protected function getJsonType($type)
    {
        switch (true) {
            case isset($type->format) && ($type->format == 'date' || $type->format == 'dateTime'):
                return 'date';

            case $type->type == 'string':
                return 'text';

            case $type->type == 'integer':
            case $type->type == 'number':
                return 'number';

            case $type->type == 'object':
                $descriptionField = \laabs::newInstance('recordsManagement/descriptionField');
                $descriptionField->type = 'object';
                $descriptionField->properties = $this->getJsonObjectProperties($type);

                return $descriptionField;

            default:
                return $type->type;
        }
    }
}
