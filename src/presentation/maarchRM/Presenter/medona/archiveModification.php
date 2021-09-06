<?php

/*
 * Copyright (C) 2015 Maarch
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

namespace presentation\maarchRM\Presenter\medona;

/**
 * archive html serializer
 *
 * @package RecordsManagement
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class archiveModification
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    protected $json;
    protected $translator;
    protected $archivalProfileController;

    /**
     * Constuctor
     * @param \dependency\html\Document                    $view
     * @param \dependency\json\JsonObject                  $json
     * @param \dependency\localisation\TranslatorInterface $translator
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator
    ) {
        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('recordsManagement/messages');

        $this->archivalProfileController = \laabs::newController("recordsManagement/archivalProfile");
    }

    /**
     * Serializer JSON for restitution method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function setForRestitution($result)
    {
        if (array_key_exists("message", $result)) {
            $this->json->message = $this->translator->getText("Restitution message created");
        } else {
            $success = count($result['success']);
            $echec = count($result['error']);
            $this->json->message='';

            if ($success > 0) {
                $this->json->message = '%1$s archive(s) flagged for restitution.';
                $this->json->message = $this->translator->getText($this->json->message);
                $this->json->message = sprintf($this->json->message, $success);
            }

            if ($echec > 0) {
                if ($success > 0) {
                    $this->json->message .= '<br>';
                }
                $message = '%1$s archive(s) can not be flagged because an action is already in progress on this(these) archive(s) (or on one of its/their objects).';
                $message = $this->translator->getText($message);
                $message = sprintf($message, $echec);

                $this->json->message .= $message;
            }
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for conversion method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function convert($result)
    {
        $this->json->message = '%1$s document(s) converted.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, count($result));

        return $this->json->save();
    }

    /**
     * Serializer JSON for conversion method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function conversion($result)
    {
        $this->json->message = '%1$s archive(s) flagged for conversion.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, count($result));

        return $this->json->save();
    }

    /**
     * Serializer JSON for modification method
     * @param recordsManagement/archiveRetentionRule $result The new retention rule
     *
     * @return object JSON object with a status and message parameters
     */
    public function modifyRetentionRule($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s archive(s) modified.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($echec > 0) {
            $message = '%1$s archive(s) can not be modified.';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for modification method
     * @param recordsManagement/archiveAccessRule $result The new retention rule
     *
     * @return object JSON object with a status and message parameters
     */
    public function modifyAccessRule($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s archive(s) modified.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($echec > 0) {
            $message = '%1$s archive(s) can not be modified.';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for freeze method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function freeze($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s archive(s) freezed.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($success == 0) {
            $this->json->status = false;
        }

        if ($echec > 0) {
            $message = '%1$s archive(s) can not be freezed.';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for unfreeze method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function unfreeze($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s archive(s) unfreezed.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($success == 0) {
            $this->json->status = false;
        }

        if ($echec > 0) {
            $message = '%1$s archive(s) can not be unfreezed.';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for addRelationship method
     *
     * @return object JSON object with a status and message parameters
     */
    public function addRelationship()
    {
        $this->translator->setCatalog('recordsManagement/archiveRelationship');
        $this->json->message = $this->translator->getText("The archive relationship has been created");

        return $this->json->save();
    }

    /**
     * Serializer JSON for deleteRelationship method
     *
     * @return object JSON object with a status and message parameters
     */
    public function deleteRelationship()
    {
        $this->translator->setCatalog('recordsManagement/archiveRelationship');
        $this->json->message = $this->translator->getText("The archive relationship has been remove");

        return $this->json->save();
    }

    /**
     * Serializer JSON for validateRestitution method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function validateRestitution($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s restitution(s) validated.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($echec > 0) {
            $message = '%1$s restitution(s) can not be validate(s).';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for cancelRestitution method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function cancelRestitution($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s restitution(s) canceled.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($echec > 0) {
            $message = '%1$s restitution(s) can not be canceled.';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for cancelDestruction method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function cancelDestruction($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s destruction(s) canceled.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($echec > 0) {
            $message = '%1$s destruction(s) can not be canceled.';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * Archive delivery
     * @param medona/message $message
     *
     * @return string
     */
    public function deliver($message)
    {
        $this->json->message = "The communication request has been send, check your requests list to follow the treatment";

        if (isset($message->replyMessage) && $message->replyMessage->status == "derogation") {
            $this->json->message = "The communication request is in derogation, check your requests list to follow the treatment";
        }

        if (isset($message->replyMessage) && $message->replyMessage->status == "accepted") {
            $this->json->message = "The communication request has been accepted, check your requests list to get the archived package";
        }

        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }


    public function transferSending($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s transfer(s) validated.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($echec > 0) {
            $message = '%1$s transfer(s) can not be validate(s).';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    public function modificationRequestSent($message)
    {
        $this->json->message = 'Modification request sent';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $message);

        return $this->json->save();
    }

    public function modificationRequestAccepted()
    {
        $this->translator->setCatalog('medona/messages');
        $this->json->message = 'Modification request accepted';
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    public function modificationRequestRejected()
    {
        $this->translator->setCatalog('medona/messages');
        $this->json->message = 'Modification request rejected';
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }
}
