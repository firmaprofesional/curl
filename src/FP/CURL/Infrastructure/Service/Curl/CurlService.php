<?php

namespace FP\CURL\Infrastructure\Service\Curl;

use FP\CURL\Domain\Exception\CurlException;
use FP\CURL\Domain\Exception\ServerException;
use FP\UTILS\Infrastructure\Monolog\Service\TraceLogIdGenerator;

class CurlService
{
    /**
     * @var CurlConfig
     */
    private $curlConfig;

    private $traceLogIdGenerator;

    public function __construct(?TraceLogIdGenerator $traceLogIdGenerator = null)
    {
        $this->traceLogIdGenerator = $traceLogIdGenerator;
    }

    /**
     * @param CurlConfig $curlConfig
     */
    public function configure(CurlConfig $curlConfig): void
    {
        $this->curlConfig = $curlConfig;
    }

    /**
     * @codeCoverageIgnore
     *
     * @return bool|string
     * @throws CurlException
     * @throws ServerException
     */
    public function send()
    {
        if ($this->traceLogIdGenerator instanceof TraceLogIdGenerator && $this->curlConfig->sendTraceLogId()) {
            $this->curlConfig->addHttpHeader(['traceLogId: ' . $this->traceLogIdGenerator->getTraceLogId()]);
        }

        $ch =  $this->prepareCurlHandler(curl_init());

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        curl_close($ch);

        if ($errno) {
            throw new CurlException('ERROR cURL (' . $errno . ')', $httpCode);
        }

        if ($httpCode >= 400) {
            throw new ServerException($result, $httpCode);
        }

        return $result;
    }

    /**
     * @codeCoverageIgnore
     *
     * @param CurlConfig[] $curlConfigs
     * @return array|string|null
     * @throws CurlException
     * @throws ServerException
     */
    public function multipleParallelSend(array $curlConfigs)
    {
        $mh = curl_multi_init();
        $chList = [];
        foreach ($curlConfigs as $i => $curlConfig) {
            $this->configure($curlConfig);
            $chList[$i] = $this->prepareCurlHandler(curl_init());
            curl_multi_add_handle($mh, $chList[$i]);
        }
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
        } while ($mrc === CURLM_CALL_MULTI_PERFORM);

        while ($active && $mrc === CURLM_OK) {
            if (curl_multi_select($mh) !== -1) {
                do {
                    $mrc = curl_multi_exec($mh, $active);
                } while ($mrc === CURLM_CALL_MULTI_PERFORM);
            }
        }
        $result = [];
        $errno = curl_multi_errno($mh);
        $httpCode = null;
        $hasServerError = false;
        foreach ($chList as $ch) {
            $result[] = curl_multi_getcontent($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode >= 400) {
                $result = curl_multi_getcontent($ch);
                $hasServerError = true;
                break;
            }
            curl_multi_remove_handle($mh, $ch);
        }
        curl_multi_close($mh);

        if ($errno) {
            throw new CurlException('ERROR cURL (' . $errno . ')', $httpCode);
        }

        if ($hasServerError) {
            throw new ServerException($result, $httpCode);
        }

        return $result;
    }

    /**
     * @param $ch
     * @return mixed
     */
    private function prepareCurlHandler($ch)
    {
        if (null !== $this->curlConfig->method()) {
            switch ($this->curlConfig->method()) {
                case 'DELETE':
                case 'PUT':
                case 'GET':
                case 'PATCH':
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->curlConfig->method());
                    break;
                default:
                    curl_setopt($ch, $this->curlConfig->method(), true);
                    break;
            }
        }

        if ($this->curlConfig->method() === 'PATCH') {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->curlConfig->data());
        }

        if ($this->curlConfig->method() === 'PUT') {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            if (is_array($this->curlConfig->data())) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($this->curlConfig->data()));
            } else if (file_exists($this->curlConfig->data())) {
                curl_setopt($ch, CURLOPT_PUT, 1);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
                curl_setopt($ch, CURLOPT_INFILE, fopen($this->curlConfig->data(), 'r'));
                curl_setopt($ch, CURLOPT_INFILESIZE, filesize($this->curlConfig->data()));
            } else {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->curlConfig->data());
            }
        }

        if ($this->curlConfig->method() !== 'PUT' && $this->curlConfig->data()) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->curlConfig->data());
        }

        //------------- CERTIFICATES
        if ($this->curlConfig->accessCert()) {
            curl_setopt($ch, CURLOPT_SSLCERT, $this->curlConfig->accessCert());
        }
        if ($this->curlConfig->accessKey()) {
            curl_setopt($ch, CURLOPT_SSLKEY, $this->curlConfig->accessKey());
        }
        if ($this->curlConfig->accessCertPassword()) {
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->curlConfig->accessCertPassword());
        }
        if ($this->curlConfig->accessCACert()) {
            curl_setopt($ch, CURLOPT_CAINFO, $this->curlConfig->accessCACert());
        }
        //--------------------------

        if ($this->curlConfig->username()) {
            curl_setopt($ch, CURLOPT_USERPWD, $this->curlConfig->username() . ':' . $this->curlConfig->userPassword());
        }

        if ($this->curlConfig->httpHeader()) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->curlConfig->httpHeader());
        }

        curl_setopt($ch, CURLOPT_URL, $this->curlConfig->curlUrl());
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

        return $ch;
    }
}
