<?php
/*
 * This file is part of the DSReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha;

use DS\Library\ReCaptcha\Http\Driver\DriverInterface;
use DS\Library\ReCaptcha\Http\Request;

/**
 * DsReCaptcha library, main instance.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class ReCaptcha
{
    public static $signupUrl = 'https://www.google.com/recaptcha/admin';
    public static $siteVerifyUrl = 'https://www.google.com/recaptcha/api/siteverify';

    /** @var string */
    protected $secret;
    /** @var string */
    protected $version = 'php_1.0';
    /** @var string */
    protected $gReCaptchaResponse;
    /** @var string */
    protected $clientIp;
    /** @var Request */
    protected $request;

    /**
     * @param string $secret
     * @param string $clientIp
     * @param string $gReCaptchaResponse
     * @throws \Exception
     */
    public function __construct($secret, $clientIp = null, $gReCaptchaResponse = null)
    {
        if (null === $secret) {
            throw new \Exception(
                sprintf(
                    'To use reCAPTCHA you must get an API key from <a href="%s">%s</a>',
                    self::$signupUrl,
                    self::$signupUrl
                )
            );
        }

        $this->secret = $secret;
        $this->clientIp = $clientIp;
        $this->gReCaptchaResponse = $gReCaptchaResponse;
    }

    /**
     * @param string $clientIp
     * @return $this
     */
    public function setClientIp($clientIp)
    {
        $this->clientIp = $clientIp;

        return $this;
    }

    /**
     * @param string $gReCaptchaResponse
     * @return $this
     */
    public function setGReCaptchaResponse($gReCaptchaResponse)
    {
        $this->gReCaptchaResponse = $gReCaptchaResponse;

        return $this;
    }

    /** @return string */
    public function getClientIp()
    {
        return $this->clientIp;
    }

    /** @return string */
    public function getGReCaptchaResponse()
    {
        return $this->gReCaptchaResponse;
    }

    /**
     * @param DriverInterface $driver
     * @return Request
     * @throws \Exception
     */
    public function buildRequest(DriverInterface $driver = null)
    {
        if (null === $this->clientIp) {
            throw new \Exception('Client IP is required.');
        }

        if (null === $this->gReCaptchaResponse) {
            throw new \Exception('G reCaptcha response token is required.');
        }

        $this->request = new Request(self::$siteVerifyUrl, $driver);
        return $this->request->setParameters(
            array(
                'secret' => $this->secret,
                'remoteip' => $this->clientIp,
                'v' => $this->version,
                'response' => $this->gReCaptchaResponse
            )
        );
    }
}
