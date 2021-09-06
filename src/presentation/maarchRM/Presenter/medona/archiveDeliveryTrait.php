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
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\medona;

/**
 * Trait html archiveDelivery
 *
 * @package medona
 * @author  Maarch Alexis Ragot <alexis.ragot@maarch.com>
 */
trait archiveDeliveryTrait
{

    /**
     * Show incoming delivery message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function deliveryRequestReplyList($messages)
    {
        $this->view->addContentFile('medona/archiveDelivery/deliveryIncomingList.html');
        $this->prepareMesageList($messages);
        $this->view->setSource("sender", true);
        $title = $this->translator->getText("Communication to recover");
        $this->view->setSource("title",$title );
        $this->view->merge();

        return $this->view->saveHtml();
    }
    /**
     * Show incoming delivery message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function deliveryRequestList($messages)
    {
        $this->view->addContentFile('medona/archiveDelivery/deliveryIncomingList.html');
        $this->prepareMesageList($messages);
        $title = $this->translator->getText("Request communication");
        $this->view->setSource("sender", true);
        $this->view->setSource("title",$title );
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show incoming delivery message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function deliveryAuthorizationList($messages)
    {
        $this->view->addContentFile('medona/archiveDelivery/deliveryIncomingList.html');
        $this->prepareMesageList($messages);
        $title = $this->translator->getText("Delivery authorization request");
        $this->view->setSource("sender", true);
        $this->view->setSource("title",$title );
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show delivery message history
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function deliveryHistory($messages)
    {
        $this->view->addContentFile('medona/archiveDelivery/deliveryHistory.html');
        $this->prepareMesageList($messages, true);
        $this->initHistoryForm();

        $statuses = [
            'new' => $this->translator->getText('new'),
            'sent' => $this->translator->getText('sent'),
            'accepted' => $this->translator->getText('accepted'),
            'rejected' => $this->translator->getText('rejected'),
            'processing' => $this->translator->getText('processing'),
            'processed' => $this->translator->getText('processed'),
            'invalid' => $this->translator->getText('invalid'),
            'error' => $this->translator->getText('error')
        ];

        $this->view->setSource('statuses', $statuses);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Get a delivery request form
     *
     * @return string
     */
    public function deliveryRequest()
    {
        $this->view->addContentFile('medona/message/requestForm.html');

        $this->view->setSource('type', "delivery");
        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show delivery message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function deliveryProcessList($messages)
    {
        $this->view->addContentFile('medona/archiveDelivery/deliveryProcessList.html');

        $this->prepareMesageList($messages);

        $this->view->setSource("recipient", true);
        $this->view->merge();

        return $this->view->saveHtml();
    }
}
