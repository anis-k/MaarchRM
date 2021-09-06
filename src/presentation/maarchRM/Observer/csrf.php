<?php
/*
 * Copyright (C) 2018 Maarch
 *
 * This file is part of bundle Auth.
 *
 * Bundle Auth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle Auth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle Auth.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Observer;

/**
 * Service for Cross Site Request Forgery protection
 *
 * @package Auth
 * @author Maarch Alexis Ragot <alexis.ragot@maarch.org>
 */
class csrf
{
    protected $sdoFactory;
    protected $config;
    protected $whiteList;

    protected $accountId;
    protected $account;
    protected $accountAuth;

    protected $requestToken;
    protected $requestTokenTime;

    protected $responseToken;
    protected $responseTokenTime;

    /**
     * Construct the observer
     * @param object $sdoFactory The user model
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->config = \laabs::configuration("auth")['csrfConfig'];
        $this->whiteList = \laabs::configuration("auth")['csrfWhiteList'];
    }

    /**
     * Observer for the CSRF protection
     * @param \core\Reflection\Command $userCommand
     * @param array                    $args
     *
     * @subject LAABS_USER_COMMAND
     */
    public function check(&$userCommand, array &$args = null)
    {
        // Do not process base uri or whitelisted URIs
        if (empty(\laabs::kernel()->request->uri) || in_array(\laabs::kernel()->request->uri, $this->whiteList)) {
            return;
        }

        // Do not process if no user account to retrieve or store csrf tokens
        if (!$this->getAccountId()) {
            return;
        }

        // Get account
        $this->getAccount();

        // Get auth object from json, init data structures if necessary
        $this->getAccountAuth();

        // Remove expired csrf tokens from security object
        $this->discardExpiredTokens();

        if (in_array($userCommand->method, ["create", "update", "delete"])) {
            $this->checkRequestToken();
        }

        // Save auth information to user account
        $this->updateAccount();

        return true;
    }

    /**
     * Observer for the CSRF protection
     * @param \core\Response\HttpResponse
     *
     * @subject LAABS_RESPONSE
     */
    public function setResponseToken(&$response)
    {
        $this->getAccountId();

        // Do not process base uri or whitelisted URIs
        if ((
            empty(\laabs::kernel()->request->uri)
            || in_array(\laabs::kernel()->request->uri, $this->whiteList)
            )
            && (empty($this->accountId))
        ) {
            return;
        }

        // Do not process if no account was loaded
        if (empty($this->accountId)) {
            return;
        }

        // Get account
        $this->getAccount();

        // Get auth object from json, init data structures if necessary
        $this->getAccountAuth();

        // If a token was received, discard it and all the previous tokens
        if (isset($this->requestTokenTime)) {
            $this->discardUsedTokens();
        }

        if (empty($this->accountAuth->csrf)) {
            // Generate a new one for next write operations
            $responseToken = $this->addToken();
        } else {
            // Select the latest unused token
            $responseToken = $this->getLastToken();
        }

        // Save auth information to user account
        $this->updateAccount();

        \laabs::setToken(strtoupper($this->config["cookieName"]), $responseToken, null, false);
    }

    /**
     * Retrieves the current account identifier
     * from AUTH or TEMP-AUTH token
     * @return string
     */
    private function getAccountId()
    {
        $authToken = \laabs::getToken('AUTH');

        if (is_null($authToken)) {
            $authToken = \laabs::getToken('TEMP-AUTH');

            if (is_null($authToken)) {
                return false;
            }
        }

        return $this->accountId = $authToken->accountId;
    }

    /**
     * Retrieves the current user accout
     */
    private function getAccount()
    {
        $this->account = $this->sdoFactory->read('auth/account', $this->accountId, $lock = true);
    }

    /**
     * Retrieves the current account auth object
     * containing the csrf tokens
     */
    private function getAccountAuth()
    {
        // Decode authentication object from JSON
        $this->accountAuth = json_decode($this->account->authentication);

        // Create authentication object if not set
        if (empty($this->accountAuth)) {
            $this->accountAuth = new \stdClass();
            $this->accountAuth->csrf = [];

            return;
        }

        // Create CSRF token list if not set
        if (!isset($this->accountAuth->csrf)) {
            $this->accountAuth->csrf = [];

            return;
        }

        // Convert object to array of timestamp => token
        $this->accountAuth->csrf = (array) $this->accountAuth->csrf;
    }

    /**
     * Remove tokens which date is expired
     */
    private function discardExpiredTokens()
    {
        // Get lifetime from config, defaults 1h
        $lifetime = '3600';
        if (isset($this->config['lifetime'])) {
            $lifetime = $this->config['lifetime'];
        }
        $duration = \laabs::newDuration('PT'.$lifetime.'S');

        // Current timestamp
        $now = \laabs::newTimestamp();

        // Loop and discard expired tokens
        foreach ($this->accountAuth->csrf as $time => $token) {
            $timestamp = \laabs::newTimestamp($time);
            $expiration = $timestamp->add($duration);

            if ($now->diff($expiration)->invert == 1) {
                unset($this->accountAuth->csrf[$time]);
            }
        }
    }

    /**
     * Adds a token to the current array of tokens
     * @return string The generated token
     */
    private function addToken()
    {
        $tokenLength = 32;

        if (!empty($this->config["cookieName"])) {
            $tokenLength = $this->config["tokenLength"];
        }

        if (function_exists("openssl_random_pseudo_bytes")) {
            $token = bin2hex(openssl_random_pseudo_bytes($tokenLength));
        } elseif (function_exists("random_bytes")) {
            $token = bin2hex(random_bytes($tokenLength));
        } else {
            $token = \laabs::newId();
        }

        $time = (string) \laabs::newTimestamp();

        return $this->accountAuth->csrf[$time] = $token;
    }

    /**
     * Returns the latest token on the auth crsf list
     * @return string
     */
    private function getLastToken()
    {
        ksort($this->accountAuth->csrf);

        return end($this->accountAuth->csrf);
    }

    /**
     * Checks that a token has been sent with request
     * and that it can be found on account auth object
     *
     * @throws Exception If no token or not found or token limit reached
     */
    private function checkRequestToken()
    {
        // getToken's param must be in Camelcase
        $this->requestToken = \laabs::getToken($this->config["cookieName"], LAABS_IN_HEADER);

        if (empty($this->requestToken)) {
            throw new \core\Exception('Attempt to access without a valid token', 412);
        }

        $this->requestTokenTime = array_search($this->requestToken, $this->accountAuth->csrf);

        if (empty($this->requestTokenTime)) {
            throw new \core\Exception('Attempt to access without a valid token', 412);
        }
    }

    /**
     * Removes the used token AND the older tokens
     * from auth object
     */
    private function discardUsedTokens()
    {
        foreach ($this->accountAuth->csrf as $time => $token) {
            if ($time <= $this->requestTokenTime) {
                unset($this->accountAuth->csrf[$time]);
            }
        }
    }

    /**
     * Persists modifications on account auth object
     */
    private function updateAccount()
    {
        $this->account->authentication = json_encode($this->accountAuth);

        $this->sdoFactory->update($this->account, "auth/account");
    }
}
