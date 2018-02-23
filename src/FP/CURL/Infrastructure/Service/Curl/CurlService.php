<?php

namespace FP\CURL\Infrastructure\Service\Curl;

use FP\CURL\Domain\Exception\CurlException;
use FP\CURL\Domain\Exception\ServerException;

class CurlService
{
    /**
     * @var CurlConfig
     */
    private $curlConfig;

    /**
     * @param CurlConfig $curlConfig
     */
    public function configure(CurlConfig $curlConfig)
    {
        $this->curlConfig = $curlConfig;
    }

    /**
     * @throws CurlException
     *
     * @codeCoverageIgnore
     *
     * @throws \FP\CURL\Domain\Exception\ServerException
     *
     * @return mixed
     */
    public function send()
    {
        $logger = \Logger::getLogger(__METHOD__);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->curlConfig->curlUrl());

        if (null !== $this->curlConfig->method()) {
            $logger->debug('cURL: set method');
            curl_setopt($ch, $this->curlConfig->method(), true);
        }

        if ($this->curlConfig->method() === CURLOPT_PUT) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PUT, 1);
            curl_setopt($ch, CURLOPT_INFILE, fopen($this->curlConfig->data(), 'r'));
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($this->curlConfig->data()));
        }

        if ($this->curlConfig->data()) {
            $logger->debug('cURL: set data');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->curlConfig->data());
        }

        //------------- CERTIFICADOS
        if ($this->curlConfig->accessCert()) {
            $logger->debug('cURL: set certificate');
            curl_setopt($ch, CURLOPT_SSLCERT, $this->curlConfig->accessCert());
        }
        if ($this->curlConfig->accessKey()) {
            $logger->debug('cURL: set key');
            curl_setopt($ch, CURLOPT_SSLKEY, $this->curlConfig->accessKey());
        }
        if ($this->curlConfig->accessCertPassword()) {
            $logger->debug('cURL: set certpassword');
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->curlConfig->accessCertPassword());
        }
        if ($this->curlConfig->accessCACert()) {
            $logger->debug('cURL: set cacert');
            curl_setopt($ch, CURLOPT_CAINFO, $this->curlConfig->accessCACert());
        }
        //--------------------------

        if ($this->curlConfig->username()) {
            $logger->debug('cURL: set password');
            curl_setopt($ch, CURLOPT_USERPWD, $this->curlConfig->username() . ':' . $this->curlConfig->userPassword());
        }

        if ($this->curlConfig->httpHeader()) {
            $logger->debug('cURL: set header');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->curlConfig->httpHeader());
        }

        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->curlConfig->timeout());
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_ENCODING, '');

        if ($this->curlConfig->verbose()) {
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            $verbose = fopen(sys_get_temp_dir().DIRECTORY_SEPARATOR.'curlLog', 'w+');
            curl_setopt($ch, CURLOPT_STDERR, $verbose);
        }

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            throw new CurlException('ERROR cURL (' . curl_error($ch) . ')', $httpCode);
        }

        if ($httpCode >= 400) {
            throw new ServerException($result, $httpCode);
        }

        return $result;
    }
}
