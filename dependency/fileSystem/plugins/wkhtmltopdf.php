<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency fileSystem.
 *
 * Dependency fileSystem is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency fileSystem is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency fileSystem.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace dependency\fileSystem\plugins;

/**
 * Class for wkhtmltopdf functions
 *
 * @package Dependency/fileSystem
 */
class wkhtmltopdf
{
    protected $wkhtmltopdfExecutable;

    /**
     * wkhtmltopdf constructor.
     * @param $wkhtmltopdfExecutable
     */
    public function __construct($wkhtmltopdfExecutable = null)
    {
        if ($wkhtmltopdfExecutable) {
            $this->wkhtmltopdfExecutable = $wkhtmltopdfExecutable;
        } else {
            $this->wkhtmltopdfExecutable = "wkhtmltopdf";
        }
    }

    /**
     *
     * @param $path
     * @return string
     * @throws \dependency\fileSystem\Exception
     */
    public function send($pathToHTML, $pathToPdf)
    {
        $command = $this->wkhtmltopdfExecutable
            ." ". $pathToHTML
            ." ". $pathToPdf;

        $output = array();
        $return = null;

        exec($command, $output, $return);

        if (file_exists($pathToPdf)) {
            return $pathToPdf;
        }

        throw new \dependency\fileSystem\Exception("error during transformation to pdf $command", $return, null, $output);
    }
}