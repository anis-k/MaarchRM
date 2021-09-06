<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency timestamp.
 *
 * Dependency timestamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency timestamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency timestamp.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\timestamp\plugins;

/**
 * Timestamp service
 */
class TsaClient implements \dependency\timestamp\TimestampInterface
{
    /**
     * @var string The tsa url
     */
    protected $url;

    /**
     * @var string The username
     */
    protected $username;

    /**
     * @var string The password
     */
    protected $password;

    /**
     * @var string The path to OpenSSL
     */
    protected $pathToOpenSSL;

    /**
     * Constructs a new RFC3163 TSA client
     * @param string $url The URL of the TSA service
     */
    public function __construct(string $tsaUrl, string $username = null, string $password = null, $pathToOpenSSL = 'openssl')
    {
        $this->url = $tsaUrl;

        $this->username = $username;

        $this->password = $password;

        $this->pathToOpenSSL = $pathToOpenSSL;

        $cmd = '"'.$this->pathToOpenSSL.'" version';
        $output = [];
        exec($cmd." 2>&1", $output, $return);

        if ($return != 0) {
            throw new \core\Exception("Openssl is not installed: ".implode(", ", $output));
        }

        $openSSL = strtok($output[0], ' ');
        $version = strtok(' ');
        if (!version_compare($version, '1', '>=')) {
            throw new \core\Exception("Openssl v1 is required, ".$version." is installed");
        }
    }

    /**
     * Get a timestamp file for a journal
     * @param string $journalFile The journal file name
     *
     * @return string the timestamp file path
     */
    public function getTimestamp($journalFile)
    {
        $hash = hash_file('SHA1', $journalFile);

        $timestamp = $this->sign($hash);

        $timestampFile = $this->createTempFile($timestamp);

        return $timestampFile;
    }
  

    /**
     * Signs a timestamp request file at a TSA using CURL
     * @param string $hash The hash to sign
     *
     * @return string The response string
     */
    public function sign($hash)
    {
        $request = $this->createRequest($hash);

        $response = $this->sendRequest($request);

        return $response;
    }

    /**
     * Get info about the response
     * @param string $response
     *
     * @return array
     */
    public function getInfo($response)
    {
        $responseFile = $this->createTempFile($response);

        $cmd = '"'.$this->pathToOpenSSL.'" ts -reply -in '.escapeshellarg($responseFile)." -text";
        
        $output = [];
        exec($cmd." 2>&1", $output, $return);
        
        if ($return !== 0) {
            throw new \core\Exception("Openssl reply failed: ".implode(", ", $output));
        }

        return $output;
    }

    /**
     * Get the timestamp from the response string
     * @param string $response
     *
     * @return string
     */
    public function getTime($response)
    {
        $output = $this->getInfo($response);
        
        $matches = [];

        foreach ($output as $line) {
            $key = strtok($line, ':');
            if ($key == 'Time stamp' || $key == 'Timestamp') {
                $time = trim(strtok(''));
                if (preg_match('/^(.+)\.(\d{1,6})(.+)$/i', $time, $matches)) {
                    $time = $matches[1].$matches[3];
                    $ms = $matches[2];
                }

                $timestamp = strtotime($time);

                if (!$timestamp) {
                    throw new \core\Exception("The time stamp information could not be converted to Unix timestamp : '".$line."'");
                }

                return $timestamp;
            }
        }
        
        throw new \core\Exception("The time stamp information was not found on the reply");
    }

    /**
     *
     * @param string $hash        The has the data which should be checked
     * @param string $response    The response as returned by sign request
     * @param int    $timestamp   The response time, which should be checked
     * @param string $tsaCertFile The path to the TSAs certificate chain
     *
     * @return bool The result
     */
    public function validate(string $hash, string $response, int $timestamp, string $tsaCertFile)
    {
        if (!file_exists($tsaCertFile)) {
            throw new \core\Exception("The TSA Certificate chain file could not be found");
        }
        
        $responseFile = $this->createTempFile($response);
        $cmd = '"'.$this->pathToOpenSSL.'" ts -verify -digest '.escapeshellarg($hash)." -in ".escapeshellarg($responseFile)." -CAfile ".escapeshellarg($tsaCertFile);
        
        $output = [];
        exec($cmd." 2>&1", $output, $return);
        
        /*
         * Reply
         *  Valid : 
         *    return 0
         *    output[0] is "verification: OK"
         *  Invalid 
         *    return 1
         *    output contains "message imprint mismatch"
         * 
         * 
         *  Errors :
         *    certificate not found
         *    invalid
         *    openssl is not installed
         *    ts command not known
         */
        
        if ($return === 0 && strtolower(trim($output[0])) == "verification: ok") {
            if ($this->getTimestamp($response) != $timestamp) {
                throw new \core\Exception("The response time of the request differs from the provided time");
            }
            
            return true;
        }

        foreach ($output as $line) {
            if (stripos($line, "message imprint mismatch") !== false) {
                return false;
            }
        }

        throw new \core\Exception("OpenSSL verification failed: ".implode(", ", $output));
    }


    protected function createRequest($hash)
    {
        $outfilepath = $this->createTempFile();

        $cmd = '"'.$this->pathToOpenSSL.'" ts -query -digest '.escapeshellarg($hash)." -cert -out ".escapeshellarg($outfilepath);
        $output = [];
        exec($cmd." 2>&1", $output, $return);

        if ($return !== 0 || stripos($output[0], "openssl:Error") !== false) {
            throw new \core\Exception("OpenSSL failed to create the request: ".implode(", ", $output));
        }

        return file_get_contents($outfilepath);
    }

    protected function sendRequest($request)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/timestamp-query'));
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36");
        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status != 200 || !strlen($response)) {
            throw new \core\Exception("The call to the TSA service failed (".$status.') :'.$response);
        }

        return $response;
    }

    protected function createTempFile(string $str = null)
    {
        $tempFile = tempnam(sys_get_temp_dir(), rand());
        if (!file_exists($tempFile)) {
            throw new \core\Exception("Could not create a temporary file at path '".$tempFile."'");
        }
            
        if (!empty($str) && !file_put_contents($tempFile, $str)) {
            throw new \core\Exception("Could not write into temporary file at path '".$tempFile."'");
        }

        return $tempFile;
    }
}
