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

namespace bundle\recordsManagement\Controller;

/**
 * Managemet of the data dictionnary description field
 *
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class descriptionField
{

    protected $sdoFactory;
    protected $csv;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     * @param \dependency\csv\Csv     $csv        Csv
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, \dependency\csv\Csv $csv = null)
    {
        $this->sdoFactory = $sdoFactory;
        $this->csv = $csv;
    }

    /**
     * List
     *
     * @param integer $limit Max limit of results to return
     *
     * @return recordsManagement/descriptionField[] The list of retention rules
     */
    public function index($limit = null)
    {
        $descriptionFields = $this->sdoFactory->find('recordsManagement/descriptionField', null, null, null, null, $limit);

        foreach ($descriptionFields as $i => $descriptionField) {
            $descriptionFields[$descriptionField->name] = $descriptionField;
            $this->serializeFacets($descriptionField);

            if (!empty($descriptionField->enumeration)) {
                $descriptionField->enumeration = json_decode($descriptionField->enumeration);
            }

            unset($descriptionFields[$i]);
        }

        return $descriptionFields;
    }

    /**
     * Create
     * @param recordsManagement/descriptionField $descriptionField The description field
     *
     * @throws \Exception
     *
     * @return boolean The request result
     */
    public function create($descriptionField)
    {
        if ($this->sdoFactory->exists("recordsManagement/descriptionField", $descriptionField->name)) {
            throw new \core\Exception\ConflictException("The description field already exists.");
        }

        $model = \laabs::bundle('recordsManagement')->getClass('descriptionField');
        $differences = array_diff_key(get_object_vars($descriptionField), $model->getProperties());

        $facets = new \stdClass();
        foreach ($differences as $property => $value) {
            if (!is_null($value)) {
                $facets->{$property} = $value;
            }
        }

        $descriptionField->facets = null;
        // cast in array to check if empty
        if (!empty((array) $facets)) {
            $descriptionField->facets = json_encode($facets);
        }

        if (!empty($descriptionField->enumNames)) {
            //throw exception if number of enumNames is different from enumeration
            // array_filter without callback function, it removes all entries equals to FALSE (cf. https://www.php.net/manual/en/function.array-filter.php)
            if (count(array_filter($descriptionField->enumNames)) != count(array_filter($descriptionField->enumeration))) {
                throw new \core\Exception\BadRequestException("All label Description fields must be filled");
            }
        }

        if (!empty($descriptionField->enumeration)) {
            $descriptionField->enumeration = json_encode($descriptionField->enumeration);
        }

        try {
            return $this->sdoFactory->create($descriptionField, 'recordsManagement/descriptionField');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Read a field
     * @param string $name The field name
     *
     * @return recordsManagement/descriptionField The description field
     */
    public function read($name)
    {
        try {
            $descriptionField = $this->sdoFactory->read('recordsManagement/descriptionField', $name);
            $this->serializeFacets($descriptionField);

            if (!empty($descriptionField->enumeration)) {
                $descriptionField->enumeration = json_decode($descriptionField->enumeration);
            }

            return $descriptionField;
        } catch (\Exception $e) {

        }
    }

    /**
     * Format facets object into descriptionField
     *
     * @param  recordsManagement/descriptionField $descriptionField DatabaseObject
     *
     * @return descriptionField                   $descriptionField object serialised
     */
    public function serializeFacets($descriptionField)
    {
        if (isset($descriptionField->facets) && !is_null($descriptionField->facets)) {
            $facets = $descriptionField->facets->getValue();
            foreach ($facets as $key => $value) {
                $descriptionField->$key = $value;
            }

            unset($descriptionField->facets);
        }
    }

    /**
     * Update a description field
     * @param recordsManagement/descriptionField $descriptionField The description field
     *
     * @throws \Exception
     *
     * @return boolean The request result
     */
    public function update($descriptionField)
    {
        if (!empty($descriptionField->enumNames)) {
            //throw exception if number of enumNames is different from enumeration
            // array_filter without callback function, it removes all entries equals to FALSE (cf. https://www.php.net/manual/en/function.array-filter.php)
            if (count(array_filter($descriptionField->enumNames)) != count(array_filter($descriptionField->enumeration))) {
                throw new \core\Exception\BadRequestException("All label Description fields must be filled");
            }
        }

        $model = \laabs::bundle('recordsManagement')->getClass('descriptionField');
        $differences = array_diff_key(get_object_vars($descriptionField), $model->getProperties());

        $facets = new \stdClass();
        if (isset($descriptionField->facets)) {
            $facets = json_decode($descriptionField->facets);
        }

        foreach ($differences as $property => $value) {
            if (!is_null($value)) {
                $facets->{$property} = $value;
            }
        }

        $descriptionField->facets = null;
        // cast as array to check if empty
        if (!empty((array) $facets)) {
            $descriptionField->facets = json_encode($facets);
        }


        if (!empty($descriptionField->enumeration)) {
            $descriptionField->enumeration = json_encode($descriptionField->enumeration);
        }

        try {
            $res = $this->sdoFactory->update($descriptionField, 'recordsManagement/descriptionField');
        } catch (\core\Exception $e) {
            throw $e;
        }

        return $res;
    }

    /**
     * Delete a description field
     * @param string $name The description field name
     *
     * @throws \Exception
     *
     * @return boolean The request result
     */
    public function delete($name)
    {
        $descriptionField = $this->sdoFactory->read('recordsManagement/descriptionField', $name);

        if (!$descriptionField) {
            return false;
        }

        if ($this->isUsed($descriptionField)) {
            throw new \core\Exception\ForbiddenException("The description field %s is currently used in an archival profile. It can't be deleted as long as it is used in any archival profile.", 403, null, [$descriptionField->name]);
        }

        try {
            $this->sdoFactory->delete($descriptionField);
        } catch (\core\Exception $e) {
            throw new \bundle\recordsManagement\Exception\descriptionFieldException("Description field not deleted.");
        }
        return true;
    }

    /**
     * Check descriptionField usage
     * @param recordsManagement/descriptionField $descriptionField The descriptionField
     *
     * @return bool The result of the operation
     */
    public function isUsed($descriptionField)
    {
        return (bool) $this->sdoFactory->count('recordsManagement/archiveDescription', "fieldName='$descriptionField->name'");
    }

    public function exportCsv($limit = null)
    {
        $descriptionFields = $this->sdoFactory->find('recordsManagement/descriptionField', null, null, null, null, $limit);

        $handler = fopen('php://temp', 'w+');
        $this->csv->writeStream($handler, (array) $descriptionFields, 'recordsManagement/descriptionField', false);
        return $handler;
    }

    public function import($data, $isReset = false)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            if ($isReset) {
                $descriptionFields = $this->index();

                $archiveDescriptionSdoFactory = \laabs::dependency('sdo', 'recordsManagement')->getService('Factory')->newInstance();
                foreach ($descriptionFields as $descriptionField) {
                    $archiveDescriptions = $archiveDescriptionSdoFactory->find('recordsManagement/archiveDescription', "fieldName='$descriptionField->name'");

                    foreach ($archiveDescriptions as $archiveDescription) {
                        $archiveDescriptionSdoFactory->delete($archiveDescription, 'recordsManagement/archiveDescription');
                    }

                    $this->sdoFactory->delete($descriptionField, 'recordsManagement/descriptionField');
                }
            }

            $descriptionFields = $this->csv->readStream($data, 'recordsManagement/descriptionField', $messageType = false);
            foreach ($descriptionFields as $key => $descriptionField) {
                if (is_null($descriptionField->name)) {
                    throw new \core\Exception\BadRequestException("Name cannot be null");
                }

                if ($isReset
                    || !$this->sdoFactory->exists('recordsManagement/descriptionField', $descriptionField->name)
                ) {
                    $this->sdoFactory->create($descriptionField, 'recordsManagement/descriptionField');
                } else {
                    $this->sdoFactory->update($descriptionField, 'recordsManagement/descriptionField');
                }
            }
        } catch (\Exception $e) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }
            throw $e;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return true;
    }
}
