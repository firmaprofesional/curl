<?php

namespace FP\CURL\Infrastructure\Service\Curl;

class CurlConfig
{
    /**
     * @var string
     */
    private $curlUrl;
    /**
     * @var string
     */
    private $accessCACert;
    /**
     * @var string
     */
    private $accessCert;
    /**
     * @var string
     */
    private $accessKey;
    /**
     * @var string
     */
    private $accessCertPassword;
    /**
     * @var array
     */
    private $httpHeader;
    /**
     * @var string
     */
    private $method;
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $userPassword;
    /**
     * @var string
     */
    private $data;

    /**
     * @var int
     */
    private $timeout = 10;

    /**
     * @var bool
     */
    private $verbose = false;

    /**
     * @return string
     */
    public function curlUrl()
    {
        return $this->curlUrl;
    }

    /**
     * @param string $curlUrl
     *
     * @return CurlConfig
     */
    public function setCurlUrl($curlUrl)
    {
        $this->curlUrl = $curlUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function accessCACert()
    {
        return $this->accessCACert;
    }

    /**
     * @param string $accessCACert
     *
     * @return CurlConfig
     */
    public function setAccessCACert($accessCACert)
    {
        $this->accessCACert = $accessCACert;

        return $this;
    }

    /**
     * @return string
     */
    public function accessCert()
    {
        return $this->accessCert;
    }

    /**
     * @param string $accessCert
     *
     * @return CurlConfig
     */
    public function setAccessCert($accessCert)
    {
        $this->accessCert = $accessCert;

        return $this;
    }

    /**
     * @return string
     */
    public function accessKey()
    {
        return $this->accessKey;
    }

    /**
     * @param string $accessKey
     *
     * @return CurlConfig
     */
    public function setAccessKey($accessKey)
    {
        $this->accessKey = $accessKey;

        return $this;
    }

    /**
     * @return string
     */
    public function accessCertPassword()
    {
        return $this->accessCertPassword;
    }

    /**
     * @param string $accessCertPassword
     *
     * @return CurlConfig
     */
    public function setAccessCertPassword($accessCertPassword)
    {
        $this->accessCertPassword = $accessCertPassword;

        return $this;
    }

    /**
     * @return array
     */
    public function httpHeader()
    {
        return $this->httpHeader;
    }

    /**
     * @param array $httpHeader
     *
     * @return CurlConfig
     */
    public function setHttpHeader($httpHeader)
    {
        $this->httpHeader = $httpHeader;

        return $this;
    }

    /**
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * @return CurlConfig
     */
    public function setMethodPUT()
    {
        $this->method = CURLOPT_PUT;

        return $this;
    }

    /**
     * @return CurlConfig
     */
    public function setMethodPOST()
    {
        $this->method = CURLOPT_POST;

        return $this;
    }
    
    /**
     * @return CurlConfig
     */
    public function setMethodDELETE() {
        $this->method = 'DELETE';
        return $this;
    }

    /**
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return CurlConfig
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function userPassword()
    {
        return $this->userPassword;
    }

    /**
     * @param string $userPassword
     *
     * @return CurlConfig
     */
    public function setUserPassword($userPassword)
    {
        $this->userPassword = $userPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function data()
    {
        return $this->data;
    }

    /**
     * @param string|array $data
     *
     * @return CurlConfig
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param bool $verbose
     * @codeCoverageIgnore
     */
    public function setVerbose($verbose)
    {
        $this->verbose = $verbose;
    }

    /**
     * @return bool
     * @codeCoverageIgnore
     */
    public function verbose()
    {
        return $this->verbose;
    }

    /**
     * @return int
     */
    public function timeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     * @return CurlConfig
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }
}
