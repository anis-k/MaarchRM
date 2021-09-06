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
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>
 */
namespace presentation\maarchRM\Presenter\medona;

/**
 * trait html archiveAuthorizationTrait
 *
 * @package medona
 * @author  Maarch Alexis Ragot <alexis.ragot@maarch.com>
 */

trait archiveAuthorizationTrait
{
    /**
     * Show the list of received archiveAuthorization request messages
     * @param medona/message[] $messages The list of message
     *
     * @return string
     */
    public function listArchiveAuthorizationReception($messages)
    {
        $this->view->addContentFile('medona/message/archiveAuthorizationReception.html');

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(4);
        $dataTable->setSorting(array(array(2, 'desc')));

        $this->view->translate();

        foreach ($messages as $message) {
            $message->status = $this->view->translator->getText($message->status, false, "medona/messages");
        }

        $this->view->setSource("messages", $messages);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show the list of sending archiveAuthorization request messages
     * @param medona/message[] $messages The list of message
     *
     * @return string
     */
    public function listArchiveAuthorizationSending($messages)
    {
        $this->view->addContentFile('medona/message/archiveAuthorizationSending.html');

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(4);
        $dataTable->setSorting(array(array(2, 'desc')));

        $this->view->translate();

        foreach ($messages as $message) {
            $message->status = $this->view->translator->getText($message->status, false, "medona/messages");
        }

        $this->view->setSource("messages", $messages);
        $this->view->merge();

        return $this->view->saveHtml();
    }
}
