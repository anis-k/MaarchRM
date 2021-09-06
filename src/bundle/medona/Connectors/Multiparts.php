<?php
/*
 * Copyright (C) 2020 Maarch
 *
 * This file is part of bundle organization.
 *
 * Bundle organization is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle organization is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle organization.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\medona\Connectors;

/**
 * @author Benjamin ROUSSELIERE <benjamin.rousseliere@maarch.org>
 */
class Multiparts
{
    protected $messageDirectory;
    protected $attachments = [];

    /**
     *
     *
     * @param string $messageDirectory [description]
     */
    public function __construct($messageDirectory)
    {
        $this->messageDirectory = \laabs::configuration("medona")['messageDirectory'];
        if (!is_dir($this->messageDirectory)) {
            mkdir($this->messageDirectory, 0777, true);
        }
    }

    /**
     * Get archive transfer transformed by connector
     *
     * @param mixed $package The source of the message
     * @param array $params      Additional parameters
     *
     * @return array Array of medona/message object
     */
    public function receive($package, $params, $messageDirectory)
    {
        if (!isset($package->data) || empty($package->data)) {
            throw new \core\Exception\BadRequestException("Package data is mandatory", 400);
        }

        if (isset($messageDirectory) && !empty($messageDirectory)) {
            if (!is_dir($messageDirectory)) {
                throw new \core\Exception\BadRequestException("MessageDirectory is not a directory", 400);
            }
            $this->messageDirectory = $messageDirectory;
        }

        if (is_resource($package->data)) {
            $data = stream_get_contents(\core\Encoding\Base64::decode($package->data));
        } elseif (filter_var($package->data, FILTER_VALIDATE_URL)) {
            // TODO verify
        } elseif (preg_match('%^[a-zA-Z0-9\\\\/+]*={0,2}$%', $package->data)) {
            $data = \core\Encoding\Base64::decode($package->data);
        } elseif (is_file($package->data)) {
            $data = file_get_contents($package->data);
        }

        // to use to modify xml, replace namespace for seda2
        //
        // $modele->xml->xpath = \laabs::newService('dependency/xml/XPath', $modele->xml);
        // $modele->xml->xpath->registerNamespace('seda', $modele->xml->documentElement->namespaceURI);
        // foreach ($params as $paramKey => $paramValue) {
        //     if (!is_null($node = $modele->xml->xpath->query("//seda:" . $paramKey)) && $node->length > 0) {
        //         if (!is_null($node->item(0))) {
        //             $node->item(0)->nodeValue .= $paramValue;
        //         }
        //     }
        // }

        $modelName = $package->name ?? (string) \laabs::newId();
        file_put_contents($messageDirectory.DIRECTORY_SEPARATOR.$modelName, $data);

        if (!empty($params['attachments'])) {
            foreach ($params['attachments'] as $key => $attachment) {
                if (empty($attachment->name)) {
                    throw new \core\Exception\BadRequestException("Attachment name is mandatory", 400);
                }
                $attachmentFileName = $messageDirectory . DIRECTORY_SEPARATOR . $attachment->name;
                if ($attachment->encoding == 'base64') {
                    switch (true) {
                        case is_resource($attachment->data):
                        case is_string($attachment->data) &&
                            (
                                filter_var(substr($attachment->data, 0, 10), FILTER_VALIDATE_URL) ||
                                is_file($attachment->data)
                            ):
                            $handler = \core\Encoding\Base64::decode($attachment->data);
                            file_put_contents($attachmentFileName, stream_get_contents($handler));
                            break;
                        case is_string($attachment->data):
                            file_put_contents($attachmentFileName, \core\Encoding\Base64::decode($attachment->data));
                            break;
                        default:
                            throw new \core\Exception\BadRequestException("Data attachment format is not valid", 400);
                            break;
                    }
                } else {
                    switch (true) {
                        case is_resource($attachment->data):
                        case is_string($attachment->data) &&
                            (
                                filter_var(substr($attachment->data, 0, 10), FILTER_VALIDATE_URL) ||
                                is_file($attachment->data)
                            ):
                            file_put_contents($attachmentFileName, file_get_contents($attachment->data));
                            break;
                        case is_string($attachment->data):
                            file_put_contents($attachmentFileName, $attachment->data);
                            break;
                        default:
                            throw new \core\Exception\BadRequestException("Data attachment format is not valid", 400);
                            break;
                    }
                }

                $this->attachments[] = $attachmentFileName;
            }
        }

        return $this->messageDirectory.DIRECTORY_SEPARATOR.$modelName;
    }
}
