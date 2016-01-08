<?php
/*
 * This file is part of the ReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha\Http\Client\Guzzle;

use DS\Library\ReCaptcha\Http\Client\ClientInterface;
use DS\Library\ReCaptcha\Http\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * ReCaptcha library, Guzzle client wrapper for Guzzle versions 4.*, 5.* and 6.*.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * Class GuzzleClient
 * @package DS\Library\ReCaptcha\Http\Client\Guzzle
 */
class GuzzleClient implements ClientInterface
{
    const GUZZLE_THREE_CLIENT_CLASS = 'Guzzle\Http\Client';
    const GUZZLE_CLIENT_CLASS = 'GuzzleHttp\Client';

    /** @var string|null */
    protected $guzzleVersion = null;
    /** @var \GuzzleHttp\Client|\Guzzle\Http\Client */
    protected $guzzle;
    /** @var RequestBuilder */
    protected $requestBuilder;

    /**
     * @param \GuzzleHttp\Client|\Guzzle\Http\Client $guzzleClient
     * @throw BadMethodCallException
     */
    public function __construct($guzzleClient = null)
    {
        if (null === $guzzleClient) {
            $guzzleClientClass = self::GUZZLE_CLIENT_CLASS;
            if ($this->checkClassExists(self::GUZZLE_THREE_CLIENT_CLASS, false)) {
                $guzzleClientClass = self::GUZZLE_THREE_CLIENT_CLASS;
            } elseif ($this->checkClassExists(self::GUZZLE_CLIENT_CLASS)) {
                $guzzleClientClass = self::GUZZLE_CLIENT_CLASS;
            }

            $guzzleClient = new $guzzleClientClass();
        }

        $this->guzzle = $guzzleClient;
        $this->requestBuilder = new RequestBuilder($this);
    }

    /**
     * @return \Guzzle\Http\Client|\GuzzleHttp\Client
     */
    public function getGuzzleClient()
    {
        return $this->guzzle;
    }

    /**
     * Check if class available
     *
     * @param string $className
     * @param bool $throwException
     * @throw BadMethodCallException
     * @return bool
     */
    protected function checkClassExists($className, $throwException = true)
    {
        if (class_exists($className)) {
            return true;
        }

        if ($throwException) {
            throw new \BadMethodCallException(
                sprintf('%s not found. Please check your dependencies in composer.json', $className)
            );
        }

        return false;
    }

    /**
     * Set Guzzle client version
     *
     * @param $version string
     */
    public function setVersion($version)
    {
        $this->guzzleVersion = $version;
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function send(RequestInterface $request)
    {
        $guzzleRequest = $this->requestBuilder->buildGuzzleRequest($request);
        $guzzleResponse = $this->guzzle->send($guzzleRequest);

        if ($this->guzzleVersion === Version::VERSION_THREE) {
            $response = new Response(
                $guzzleResponse->getStatusCode(),
                $guzzleResponse->getHeaders()->toArray(),
                $guzzleResponse->getBody(true)
            );
        } else {
            $response = new Response(
                $guzzleResponse->getStatusCode(),
                $guzzleResponse->getHeaders(),
                $guzzleResponse->getBody()->getContents()
            );
        }

        return $response;
    }
}
