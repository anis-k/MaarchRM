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
trait archiveRestitutionTrait
{
    /**
     * Show incoming restitution message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function restitutionIncomingList($messages)
    {
        $this->view->addContentFile('medona/archiveRestitution/restitutionIncomingList.html');
        $this->prepareMesageList($messages);
        $title = $this->translator->getText("Restitution process");
        $this->view->setSource("sender", true);
        $this->view->setSource("title",$title );
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show incoming restitution message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function restitutionValidationIncomingList($messages)
    {
        $this->view->addContentFile('medona/archiveRestitution/restitutionIncomingList.html');
        $this->prepareMesageList($messages);
        $title = $this->translator->getText("Restitution to recover");
        $this->view->setSource("sender", true);
        $this->view->setSource("title",$title );
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show incoming restitution message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function restitutionRequestIncomingList($messages)
    {
        $this->view->addContentFile('medona/archiveRestitution/restitutionIncomingList.html');
        $this->prepareMesageList($messages);
        $title = $this->translator->getText("Request validation");
        $this->view->setSource("title",$title );
        $this->view->setSource("sender", true);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show restitution message history
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function restitutionHistory($messages)
    {
        $this->view->addContentFile('medona/archiveRestitution/restitutionHistory.html');
        $this->prepareMesageList($messages, true);
        $this->initHistoryForm();

        $statuses = [
            'sent' => $this->translator->getText('sent'),
            'received' => $this->translator->getText('received'),
            'acknowledge' => $this->translator->getText('acknowledge'),
            'validated' => $this->translator->getText('valid'),
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
     * Get a restituion request form
     *
     * @return string
     */
    public function restitutionRequest()
    {
        $this->view->addContentFile('medona/message/requestForm.html');

        $this->view->setSource('type', "restitution");
        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Serializer JSON for acceptArchiveRestitution method
     *
     * @return string
     */
    public function acceptArchiveRestitution()
    {
        $this->json->message = $this->translator->getText("Message accepted");

        return $this->json->save();
    }

    /**
     * Serializer JSON for acceptArchiveRestitutionRequest method
     *
     * @return string
     */
    public function acceptArchiveRestitutionRequest()
    {
        $this->json->message = $this->translator->getText("Message accepted");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectArchiveRestitution method
     *
     * @return string
     */
    public function rejectArchiveRestitution()
    {
        $this->json->message = $this->translator->getText("Message rejected");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectArchiveRestitutionRequest method
     *
     * @return string
     */
    public function rejectArchiveRestitutionRequest()
    {
        $this->json->message = $this->translator->getText("Message rejected");

        return $this->json->save();
    }

    /**
     * Serializer JSON for processArchiveRestitution
     *
     * @return string
     */
    public function processArchiveRestitution()
    {
        $this->json->message = $this->translator->getText("Message processed");

        return $this->json->save();
    }

    /**
     * Serializer JSON for processArchiveRestitutionRequest
     *
     * @return string
     */
    public function processArchiveRestitutionRequest()
    {
        $this->json->message = $this->translator->getText("Message processed");

        return $this->json->save();
    }
}
