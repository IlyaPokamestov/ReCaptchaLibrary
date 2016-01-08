<?php
/*
 * This file is part of the ReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha;

use DS\Library\ReCaptcha\Http\Client\ClientInterface;
use DS\Library\ReCaptcha\Http\Client\SimpleClient;
use DS\Library\ReCaptcha\Http\Request;
use DS\Library\ReCaptcha\Http\Stream;
use DS\Library\ReCaptcha\Http\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * ReCaptcha library, Google reCaptcha v2 client.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class Client
{
    /**
     * Google reCaptcha verify url.
     * @var string
     */
    public static $siteVerifyUri = 'https://www.google.com/recaptcha/api/siteverify';

    /** @var string */
    protected $secret;
    /** @var  ClientInterface */
    protected $httpClient;
    /** @var  UriInterface */
    protected $uri;
    /** @var RequestInterface */
    protected $request;
    /** @var  ResponseInterface */
    protected $response;
    /** @var string */
    protected $method = 'POST';

    /**
     * Client constructor.
     * @param string $secret
     * @param UriInterface|string|null $verifyUri
     * @param ClientInterface|null $httpClient
     * @param string $requestMethod
     */
    public function __construct($secret, $httpClient = null, $verifyUri = null, $requestMethod = 'POST')
    {
        $this->secret = $secret;

        if (is_string($verifyUri)) {
            $this->uri = new Uri($verifyUri);
        } elseif ($verifyUri instanceof UriInterface) {
            $this->uri = $verifyUri;
        } else {
            $this->uri = new Uri(self::$siteVerifyUri);
        }

        $this->httpClient = (null === $httpClient) ? new SimpleClient() : $httpClient;
        $this->method = $requestMethod;
    }

    /**
     * Get reCaptcha secret key.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param string $googleResponseToken
     * @param null|string $ip
     * @return bool
     */
    public function validate($googleResponseToken, $ip = null)
    {
        $this->request = new Request();
        $this->request->withUri($this->uri)
            ->withMethod($this->method)
            ->withBody($this->createBody($googleResponseToken, $ip))
            ->withHeader('Content-type', 'application/x-www-form-urlencoded');

        return $this->validateRequest($this->request);
    }

    /**
     * @param string $googleResponseToken
     * @param null|string $ip
     * @return StreamInterface
     */
    public function createBody($googleResponseToken, $ip = null)
    {
        $parameters = array(
            'secret' => $this->secret,
            'response' => $googleResponseToken,
        );

        if (null !== $ip) {
            $parameters['remoteip'] = $ip;
        }

        return new Stream($parameters);
    }

    /**
     * @param RequestInterface $request
     * @return bool
     */
    public function validateRequest(RequestInterface $request)
    {
        $response = $this->send($request);

        return $this->isValid($response);
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     * @throws ValidationException
     */
    public function isValid(ResponseInterface $response)
    {
        $responseArray = json_decode($response->getBody()->getContents(), true);

        if (isset($responseArray['error-codes'])) {
            throw new ValidationException($responseArray['error-codes'], $response, $this->request);
        }

        if (isset($responseArray['success']) && $responseArray['success'] === true) {
            return true;
        }

        return false;
    }

    /**
     * Send verification request
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function send(RequestInterface $request)
    {
        $this->request = $request;

        if ($this->httpClient instanceof ClientInterface || method_exists($this->httpClient, 'send')) {
            $this->response = $this->httpClient->send($request);
        } else {
            throw new \RuntimeException('HTTP Client must implement method send, and return ResponseInterface');
        }

        return $this->response;
    }
}
