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
 * trait html archiveDelivery
 *
 * @package medona
 * @author  Maarch Alexis Ragot <alexis.ragot@maarch.com>
 */
trait archiveTransferTrait
{
    /**
     * Get the message import form
     *
     * @return string
     */
    public function messageImport()
    {
        $packageSchemas = [];
        if (isset(\laabs::configuration('medona')['packageSchemas'])) {
            $packageSchemas = \laabs::configuration('medona')['packageSchemas'];
        }

        $packageConnectors = [];
        if (isset(\laabs::configuration('medona')['packageConnectors'])) {
            $packageConnectors = \laabs::configuration('medona')['packageConnectors'];
        }
        $this->view->addContentFile("medona/archiveTransfer/messageImport.html");

        $this->view->setSource("packageSchemas", $packageSchemas);
        $this->view->setSource("packageConnectors", $packageConnectors);
        $this->view->merge();
        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * Get the source inputs form
     *
     * @param string $schema The source schema
     * @param string $source The name of the source
     *
     * @return string The view
     */
    public function getSourceInputs($schema, $source)
    {
        $inputs = [];
        $notTextTypes = ['file', 'boolean', 'number', 'enum'];

        if (isset(\laabs::configuration('medona')['packageConnectors'][$source]['params'])) {
            $sourceInputs = \laabs::configuration('medona')['packageConnectors'][$source]['params'];
            foreach ($sourceInputs as $key => $input) {
                if (isset($input["source"]) && $input["source"] == 'input') {
                    $input['name'] = $key;
                    if (!isset($input['type'])) {
                        $input['type'] = 'text';
                    }
                    $input['typeAccepted'] = false;
                    if (in_array($input['type'], $notTextTypes)) {
                        $input['typeAccepted'] = true;
                    }
                    $inputs[] = $input;
                }
            }
        }

        $this->view->addContentFile("medona/archiveTransfer/sourceInputsForm.html");
        $this->view->setSource("inputs", $inputs);
        $this->view->merge();
        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * Show incoming transfer message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function transferIncomingList($messages)
    {
        $this->view->addContentFile('medona/archiveTransfer/transferIncomingList.html');

        $this->prepareMesageList($messages);

        $this->view->setSource("sender", true);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show outgoing transfer message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function transferOutgoingList($messages)
    {
        $this->view->addContentFile('medona/archiveTransfer/transferOutgoingList.html');

        $this->prepareMesageList($messages);

        $this->view->setSource("recipient", true);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show incoming transfer message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function transferHistory($messages)
    {
        $this->view->addContentFile('medona/archiveTransfer/transferHistory.html');
        $this->prepareMesageList($messages, true);
        $this->initHistoryForm();

        $statuses = [
            'template' => $this->translator->getText('template'),
            'draft' => $this->translator->getText('draft'),
            'sent' => $this->translator->getText('sent'),
            'received' => $this->translator->getText('received'),
            'valid' => $this->translator->getText('valid'),
            'accepted' => $this->translator->getText('accepted'),
            'processing' => $this->translator->getText('processing'),
            'processed' => $this->translator->getText('processed'),
            'toBeModified' => $this->translator->getText('toBeModified'),
            'modified' => $this->translator->getText('modified'),
            'rejected' => $this->translator->getText('rejected'),
            'invalid' => $this->translator->getText('invalid'),
            'error' => $this->translator->getText('error')
        ];

        $this->view->setSource('statuses', $statuses);
        $this->view->merge();

        return $this->view->saveHtml();
    }
}
