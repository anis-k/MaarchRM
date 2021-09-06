<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of Laabs.
 *
 * Laabs is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Laabs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Laabs.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace core\Encoding;

/**
 * Base64 encoder/decoder
 *
 * @package Laabs
 * @author  Cyril Vazquez (Maarch) <cyril.vazquez@maarch.org>
 */
class Base64
{
    /**
     * Decode stream
     */
    public static function decode($value)
    {
        if (is_scalar($value)) {
            return base64_decode($value);
        }

        if (is_resource($value)) {
            $stream = fopen('php://temp', 'w+');
            while (!feof($value)) {
                // Chunk size 1024*1024*2 (2Mo binary)
                $chunk = fread($value, 2097152);
                fwrite($stream, base64_decode($chunk));
            }

            rewind($value);
            rewind($stream);

            return $stream;
        }
    }

    /**
     * Encode stream
     */
    public static function encode($value)
    {
        if (is_scalar($value)) {
            return base64_encode($value);
        }

        if (is_resource($value)) {
            $stream = fopen('php://temp', 'w+');
            while (!feof($value)) {
                // Chunk size 48*48*48/2 (2M of base64 6 bytes words)
                $chunk = fread($value, 2654208);
                fwrite($stream, base64_encode($chunk));
            }

            rewind($value);
            rewind($stream);

            return $stream;
        }
    }
}
