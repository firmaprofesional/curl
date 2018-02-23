<?php

namespace FP\CURL\Tests\Infrastructure\Curl;

use FP\CURL\Infrastructure\Service\Curl\CurlConfig;

class CurlConfigTest extends \PHPUnit_Framework_TestCase
{
    private $accessCACert = 'cacert';
    private $accessCert = 'accesscert';
    private $accessCertPassword = 'accesscertpassword';
    private $accessKey = 'accesskey';
    private $curlUrl = 'curlurl';
    private $data = 'data';
    private $httpHeader = array('http' => 'header');
    private $username = 'username';
    private $userPassword = 'userpassword';

    public function testSetters()
    {
        $curlConfig = new CurlConfig();
        $curlConfig->setAccessCACert($this->accessCACert);
        $curlConfig->setAccessCert($this->accessCert);
        $curlConfig->setAccessCertPassword($this->accessCertPassword);
        $curlConfig->setAccessKey($this->accessKey);
        $curlConfig->setCurlUrl($this->curlUrl);
        $curlConfig->setData($this->data);
        $curlConfig->setHttpHeader($this->httpHeader);
        $curlConfig->setUsername($this->username);
        $curlConfig->setUserPassword($this->userPassword);
        
        $this->assertEquals($curlConfig->accessCACert(), $this->accessCACert);
        $this->assertEquals($curlConfig->accessCert(), $this->accessCert);
        $this->assertEquals($curlConfig->accessCertPassword(), $this->accessCertPassword);
        $this->assertEquals($curlConfig->accessKey(), $this->accessKey);
        $this->assertEquals($curlConfig->curlUrl(), $this->curlUrl);
        $this->assertEquals($curlConfig->data(), $this->data);
        $this->assertEquals($curlConfig->httpHeader(), $this->httpHeader);
        $this->assertEquals($curlConfig->username(), $this->username);
        $this->assertEquals($curlConfig->userPassword(), $this->userPassword);
    }

    public function testMethod()
    {
        $curlConfig = new CurlConfig();

        $curlConfig->setMethodPOST();
        $this->assertEquals($curlConfig->method(), CURLOPT_POST);

        $curlConfig->setMethodPUT();
        $this->assertEquals($curlConfig->method(), CURLOPT_PUT);
    }
}
