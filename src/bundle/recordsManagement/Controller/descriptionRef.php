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
class descriptionRef
{
    protected $dir;

    /**
     * Constructor
     * @param string $refDirectory The directory where refs are uploaded
     */
    public function __construct($refDirectory = null)
    {
        $this->dir = $refDirectory;

        if (!is_dir($this->dir)) {
            mkdir($this->dir, 775, true);
        }
    }

    /**
     * Upload a new version of the ref
     * @param string $name     The name of the ref
     * @param string $contents The datas of the ref, csv format
     */
    public function upload($name, $contents)
    {
        file_put_contents($this->dir.DIRECTORY_SEPARATOR.$name.'.csv', $contents);
    }

    /**
     * Download a csv file of the ref
     * @param string $name The name of the ref
     *
     * @return string
     */
    public function download($name)
    {
        return file_get_contents($this->dir.DIRECTORY_SEPARATOR.$name.'.csv');
    }

    /**
     * Search the ref
     * @param string $name  The name of the ref
     * @param string $query an optional searched text
     *
     * @return array object[]
     */
    public function search($name, $query = null)
    {
        $data = [];

        $handler = $this->open($name);

        if (!$handler) {
            return $data;
        }

        // $header = fgetcsv($handler, 1000, ",");
        while (($row = fgetcsv($handler, 1000, ",")) !== false) {
            if ($query && mb_strlen($query) > 2) {
                if (!preg_match('/'.preg_quote($query, '/').'/i', implode(' | ', $row))) {
                    continue;
                }
            }
            $item = [];
            // foreach ($header as $i => $name) {
            //     $item[$name] = $row[$i];
            // }
            $data[] = (object) $row;
        }

        fclose($handler);

        return $data;
    }

    /**
     * Retrieve the ref
     * @param string $name The name of the ref
     * @param string $key  The key value, first column
     *
     * @return object
     */
    public function get($name, $key)
    {
        $item = null;

        $handler = $this->open($name);

        if (!$handler) {
            return $item;
        }

        $item = [];
        // $header = fgetcsv($handler, 1000, ",");
        while (($row = fgetcsv($handler, 1000, ",")) !== false) {
            if ($row[0] == $key) {
                // foreach ($header as $i => $name) {
                //     $item[$name] = $row[$i];
                // }
                $item = $row;
            }
        }

        fclose($handler);

        return $item;
    }

    protected function open($name)
    {
        $filename = $this->dir.DIRECTORY_SEPARATOR.$name.'.csv';

        if (is_file($filename) && ($handle = fopen($filename, "r")) !== false) {
            return $handle;
        }
    }
}
