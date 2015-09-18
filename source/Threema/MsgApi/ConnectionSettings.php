<?php
/**
 * @author Threema GmbH
 * @copyright Copyright (c) 2015 Threema GmbH
 */


namespace Threema\MsgApi;

class ConnectionSettings
{
    /**
     * @var string
     */
    private $threemaId;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $host;

    /**
     * @var array
     */
    private $tlsOptions;

    /**
     * @param string $threemaId valid threema id (8chars)
     * @param string $secret secret
     * @param string $host server url
     */
    public function __construct($threemaId, $secret, $host = null, $tlsOptions = null)
    {
        $this->threemaId = $threemaId;
        $this->secret = $secret;
        if ($host === null) {
            $host = 'https://msgapi.threema.ch';
        }
        $this->host = $host;

        // TLS options
        if ($tlsOptions !== null && is_array($tlsOptions) && !empty($tlsOptions)) {
            if (!array_key_exists('forceHttps', $tlsOptions) || $tlsOptions['forceHttps'] === null) {
                $tlsOptions['forceHttps'] = false;
            }
            if (!array_key_exists('tslVersion', $tlsOptions)) {
                $tlsOptions['tslVersion'] = null;
            }
            if (!array_key_exists('tslCipher', $tlsOptions)) {
                $tlsOptions['tslCipher'] = null;
            }

            $this->tlsOptions = $tlsOptions;
        } else {
            $this->tlsOptions = null;
        }
    }

    /**
     * @return string
     */
    public function getThreemaId()
    {
        return $this->threemaId;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @return array
     */
    public function getTlsOptions()
    {
        return $this->tlsOptions;
    }

    /**
     * @return string
     */
    public function getTlsOption($option)
    {
        return $this->tlsOptions[$option];
    }
}
