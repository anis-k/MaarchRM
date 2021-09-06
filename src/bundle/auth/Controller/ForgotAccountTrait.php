<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle auth.
 *
 * Bundle auth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle auth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle auth.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\auth\Controller;
use bundle\digitalResource\Exception\resourceUnavailableException;

/**
 * Forgot account trait
 *
 * @package Auth
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
trait ForgotAccountTrait
{
    /**
     * Generate reset token
     * @param string $username The username
     * @param string $email    The email of the user
     *
     * @throws \Exception
     *
     * @return boolean The result of the request
     */
    public function forgotAccount($username, $email)
    {
        $result = null;

        try {
            if (!$this->sdoFactory->exists("auth/account", array("accountName" => $username))) {
                 throw \laabs::newException('auth/authenticationException', 'Invalid username or email.', 401);
            }

            $userAccount = $this->sdoFactory->read("auth/account", array("accountName" => $username));

            if ($userAccount->enabled != true) {
                throw \laabs::newException('auth/authenticationException', 'User %1$s is disabled', 403, null, array($username));
            }

            if ($email != $userAccount->emailAddress) {
                throw \laabs::newException('auth/authenticationException', 'Invalid username or email.', 401);
            }

            $data = new \stdClass();
            $data->accountId = $userAccount->accountId;
            $data->tokenDate = \laabs::newTimestamp();

            $userAccount->tokenDate = $data->tokenDate;
            $this->sdoFactory->update($userAccount, "auth/account");

            $token = $this->generateEncodedToken($data);

            $message = $this->getForgotAccountMessage($token);

            $notificationDependency = \laabs::newService("dependency/notification/Notification");

            $title = "Maarch RM - user information";
            $result = $notificationDependency->send($title, $message, array($userAccount->emailAddress));
        } catch (\bundle\auth\Exception\authenticationException $e) {
            \laabs::notify(LAABS_BUSINESS_EXCEPTION, $e);
        }

        return $result;
    }

    /**
     * Reset a user password
     * @param $newPassword string The new password
     * @param $token       string
     *
     * @throws \core\Exception\ForbiddenException
     *
     * @return boolean True if the password has been reset
     */
    public function resetPassword($newPassword, $token)
    {
        $token = $this->decodeToken($token);

        if (empty($token)) {
            throw new \core\Exception\ForbiddenException("Invalid link");
        }

        $token->data->tokenDate = \laabs::newTimestamp($token->data->tokenDate);

        if (!$this->sdoFactory->exists("auth/account", array("accountId" => $token->data->accountId))) {
            throw new \core\Exception\ForbiddenException("Invalid link");
        }

        $userAccount = $this->sdoFactory->read("auth/account", $token->data->accountId);

        $this->checkOldToken($userAccount, $token);

        $this->checkPasswordAlreadyChanged($userAccount, $token);

        $this->checkTokenDelay($token);

        $this->updatePassword($userAccount, $newPassword);

        return true;
    }

    /**
     * Generate an encoded token
     * @param $data Data of the token
     *
     * @return string The encoded token
     */
    private function generateEncodedToken($data)
    {
        $token = new \core\token($data, 3600);
        $jsonToken = \json_encode($token);
        $cryptedToken = \laabs::encrypt($jsonToken, \laabs::getCryptKey());
        $base64Token = base64_encode($cryptedToken);
        $urlEncodeToken = urlencode($base64Token);

        return $urlEncodeToken;
    }

    private function decodeToken($encodedToken)
    {
        $cryptedToken = base64_decode($encodedToken);
        $jsonToken = \laabs::decrypt($cryptedToken, \laabs::getCryptKey());
        $jsonToken = trim($jsonToken);
        $token = json_decode($jsonToken);

        return $token;
    }

    /**
     * Get message of the forgot account notification
     * @param string $encodedToken The encoded token
     *
     * @return string The message
     */
    private function getForgotAccountMessage($encodedToken)
    {
        $host = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $link = $host . "/user/changePassword?token=" . $encodedToken;

        $message = 'A password reset request has been sent. To reset your password, please to click on this link %1$s';

        if (!empty($this->securityPolicy["newPasswordValidity"]) && $this->securityPolicy["newPasswordValidity"] != 0) {
            $message .= '  and you have %2$d hour to change it';
            $message = sprintf($message, $link, $this->securityPolicy["newPasswordValidity"]);
        } else {
            $message = sprintf($message, $link);
        }

        return $message;
    }

    /**
     * Check if the token isn't the last
     * @param auth/userAccount $userAccount
     * @param object $token The token
     *
     * @throws \core\Exception\ForbiddenException
     */
    private function checkOldToken($userAccount, $token)
    {
        if (empty($userAccount->tokenDate) || $userAccount->tokenDate->getTimestamp() != $token->data->tokenDate->getTimestamp()) {
            throw new \core\Exception\ForbiddenException("Expired link");
        }
    }

    /**
     * Check if the password was already changed
     * @param auth/userAccount $userAccount
     * @param object $token The token
     *
     * @throws \core\Exception\ForbiddenException
     */
    private function checkPasswordAlreadyChanged($userAccount, $token)
    {
        if (empty($userAccount->passwordLastChange)) {
            return;
        }

        $diffWithLastChange = $userAccount->passwordLastChange->getTimestamp() - $token->data->tokenDate->getTimestamp();

        if ($diffWithLastChange > 0) {
            throw new \core\Exception\ForbiddenException("Invalid link");
        }
    }

    /**
     * Check if the security policy to changed the password is exceeded
     * @param object $token The token
     *
     * @throws \core\Exception\ForbiddenException
     */
    private function checkTokenDelay($token)
    {
        if (empty($this->securityPolicy["newPasswordValidity"]) || $this->securityPolicy["newPasswordValidity"] <= 0) {
            return;
        }

        $diffWithSecurityPolicy = ($token->data->tokenDate->getTimestamp() + $this->securityPolicy["newPasswordValidity"] * 3600) - \laabs::newTimestamp()->getTimestamp();

        if ($diffWithSecurityPolicy <= 0) {
            throw new \core\Exception\ForbiddenException("Expired link");
        }
    }
}
