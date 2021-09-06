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
 * Trait html archiveDestruction
 *
 * @package medona
 * @author  Maarch Alexis Ragot <alexis.ragot@maarch.com>
 */
trait archiveDestructionTrait
{
    /**
     * Show incoming destruction message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function destructionIncomingList($messages)
    {
        $this->view->addContentFile('medona/archiveDestruction/destructionIncomingList.html');

        $this->prepareMesageList($messages);

        $title = $this->translator->getText("Request validation");
        $this->view->setSource("sender", true);
        $this->view->setSource("title",$title );
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show incoming destruction message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function destructionAuthorizationIncomingList($messages)
    {
        $this->view->addContentFile('medona/archiveDestruction/destructionIncomingList.html');

        $this->prepareMesageList($messages);

        $title = $this->translator->getText("Authorization request");
        $this->view->setSource("sender", true);
        $this->view->setSource("title",$title );
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show incoming transfer message list
     * @param array $messages Array of message object
     *
     * @return string The view
     */
    public function destructionHistory($messages)
    {
        $this->view->addContentFile('medona/archiveDestruction/destructionHistory.html');
        $this->prepareMesageList($messages, true);
        $this->initHistoryForm();

        $statuses = [
            'sent' => $this->translator->getText('sent'),
            'validated' => $this->translator->getText('validated'),
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
     * Serializer JSON for delete method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function dispose($result)
    {
        $echec = 0;
        $success = count($result['success']);
        if (array_key_exists('error', $result)) {
            $echec = count($result['error']);
        }
        $this->translator->setCatalog('recordsManagement/messages');

        $this->json->message = '%1$s / %2$s archive(s) flagged for destruction.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success,($echec+$success));

        return $this->json->save();
    }

    /**
     * notDisposableArchiveException presenter
     *
     * @param \bundle\recordsManagement\Exception\notDisposableArchiveException $exception The exception
     * @return string JSON result with status and message parameters
     */
    public function notDisposableArchiveException($exception)
    {
        $this->translator->setCatalog('recordsManagement/messages');
        $this->json->message = $this->translator->getText($exception->getMessage());
        $this->json->status = true;

        return $this->json->save();
    }

    /**
     * Get a destruction request form
     *
     * @return string
     */
    public function destructionRequest()
    {
        $this->view->addContentFile('medona/message/requestForm.html');

        $this->view->setSource('type', "destruction");
        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }
}
