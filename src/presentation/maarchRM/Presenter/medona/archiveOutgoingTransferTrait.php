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
 * trait html archive outgoing transfer
 *
 * @package medona
 * @author  Maarch Alexandre Morin <alexandre.morin@maarch.com>
 */
trait archiveOutgoingTransferTrait
{

    /**
     * Show incoming transfer message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function outgoingTransferList($messages)
    {
        $this->view->addContentFile('medona/archiveTransfer/outgoingTransferList.html');

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
    public function outgoingTransferProcessList($messages)
    {
        $this->view->addContentFile('medona/archiveTransfer/outgoingTransferProcessList.html');

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
    public function outgoingTransferHistory($messages)
    {
        $this->view->addContentFile('medona/archiveTransfer/outgoingTransferHistory.html');
        $this->prepareMesageList($messages, true);
        $this->initHistoryForm();

        $statuses = [
            'sent' => $this->translator->getText('sent'),
            'transferable' => $this->translator->getText('transferable'),
            'downloaded' => $this->translator->getText('downloaded'),
            'validating' => $this->translator->getText('validating'),
            'validated' => $this->translator->getText('validated'),
            'accepted' => $this->translator->getText('accepted'),
            'rejected' => $this->translator->getText('rejected'),
            'acknowledge' => $this->translator->getText('acknowledge'),
            'processing' => $this->translator->getText('processing'),
            'processed' => $this->translator->getText('processed'),
            'invalid' => $this->translator->getText('invalid'),
            'error' => $this->translator->getText('error')
        ];

        $this->view->setSource('statuses', $statuses);
        $this->view->merge();

        return $this->view->saveHtml();
    }
}
