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

use Psr\Http\Message\RequestInterface;

/**
 * ReCaptcha library, Guzzle request builder, build request object for any Guzzle versions.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * Class RequestBuilder
 * @package DS\Library\ReCaptcha\Http\Client\Guzzle
 */
class RequestBuilder
{
    /** @var string */
    protected $guzzlePrsRequestClass = '\GuzzleHttp\Psr7\Request';
    /** @var GuzzleClient */
    protected $guzzleReCaptchaClient;
    /** @var \Guzzle\Http\Client|\GuzzleHttp\Client */
    protected $guzzleClient;
    /** @var array */
    protected $callableChain = array();

    /**
     * @param GuzzleClient $guzzle
     */
    public function __construct(GuzzleClient $guzzle)
    {
        $this->guzzleReCaptchaClient = $guzzle;
        $this->guzzleClient = $guzzle->getGuzzleClient();
    }

    /**
     * Build guzzle request based on Guzzle client version.
     *
     * @param RequestInterface $request
     * @return mixed
     * @throws RequestException
     */
    public function buildGuzzleRequest(RequestInterface $request)
    {
        $this->addCallable(function () use ($request) {
            return $this->buildForVersionThree($request);
        });

        $this->addCallable(function () use ($request) {
            return $this->buildForVersionFourOrFive($request);
        });

        $this->addCallable(function () use ($request) {
            return $this->buildForVersionSix($request);
        });

        $guzzleRequest = $this->executeCallableChain();

        if (null === $guzzleRequest) {
            throw new RequestException('Unknown Guzzle client, request instance can not be created.');
        }

        return $guzzleRequest;
    }

    /**
     * Add request builder callable
     *
     * @param callable $callable
     */
    protected function addCallable($callable)
    {
        if (!is_callable($callable)) {
            throw new \InvalidArgumentException('Argument must be callable.');
        }

        $this->callableChain[] = $callable;
    }

    /**
     * Execute callable chain and return callable result.
     *
     * @return mixed
     */
    protected function executeCallableChain()
    {
        $callableResult = null;
        foreach ($this->callableChain as $callable) {
            $callableResult = call_user_func($callable);

            if (null !== $callableResult) {
                break;
            }
        }

        return $callableResult;
    }

    /**
     * Build request object for Guzzle 4.* or 5.*
     *
     * @param RequestInterface $request
     * @return mixed
     */
    protected function buildForVersionFourOrFive(RequestInterface $request)
    {
        if (method_exists($this->guzzleClient, 'createRequest')) {
            $this->guzzleReCaptchaClient->setVersion(Version::VERSION_FOUR_FIVE);
            return $this->guzzleClient->createRequest(
                $request->getMethod(),
                (string)$request->getUri(),
                array (
                    'headers' => $request->getHeaders(),
                    'body' => $request->getBody()->getContents(),
                )
            );
        }

        return null;
    }

    /**
     * Build request object for Guzzle 6.*
     *
     * @param RequestInterface $request
     * @return mixed
     */
    protected function buildForVersionSix(RequestInterface $request)
    {
        if (class_exists($this->guzzlePrsRequestClass)) {
            $this->guzzleReCaptchaClient->setVersion(Version::VERSION_SIX);
            return new $this->guzzlePrsRequestClass(
                $request->getMethod(),
                (string)$request->getUri(),
                $request->getHeaders(),
                $request->getBody()
            );
        }

        return null;
    }

    /**
     * Build request object for Guzzle 3.*
     *
     * @param RequestInterface $request
     * @return mixed
     */
    protected function buildForVersionThree(RequestInterface $request)
    {
        if (get_class($this->guzzleClient) === GuzzleClient::GUZZLE_THREE_CLIENT_CLASS
            && method_exists($this->guzzleClient, 'createRequest')) {
            $this->guzzleReCaptchaClient->setVersion(Version::VERSION_THREE);
            return $this->guzzleClient->createRequest(
                $request->getMethod(),
                (string)$request->getUri(),
                $request->getHeaders(),
                $request->getBody()->getContents()
            );
        }

        return null;
    }
}
