<?php
/*
 * This file is part of the ReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha\Http;

use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * ReCaptcha library, PSR7 request representation.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * Class Request
 * @package DS\Library\ReCaptcha\Http
 */
class Request extends Message implements RequestInterface
{
    /** @var string */
    protected $method;
    /** @var null|UriInterface */
    protected $uri;
    /** @var null|string */
    protected $requestTarget;

    /**
     * @param null|string $method HTTP method for the request.
     * @param null|string $uri URI for the request.
     *
     * @throws InvalidArgumentException for an invalid URI
     */
    public function __construct($method = null, $uri = null)
    {
        if (is_string($uri)) {
            $this->uri = new Uri($uri);
            $this->addHostHeader($this->uri);
        } elseif ($uri instanceof UriInterface) {
            $this->uri = $uri;
            $this->addHostHeader($this->uri);
        }

        if (is_string($method)) {
            $this->method = strtoupper($method);
        }
    }

    /**
     * Build host.
     *
     * @param UriInterface $uri
     */
    protected function addHostHeader(UriInterface $uri)
    {
        $host = $uri->getHost();
        if ($port = $uri->getPort()) {
            $host = sprintf('%s:%s', $host, $port);
        }

        $this->headers['host'] = array($host);
    }

    /** {@inheritdoc} */
    public function getRequestTarget()
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath();

        if ('' === $target) {
            $target = '/';
        }

        if ($this->uri->getQuery()) {
            $target .= '?' . $this->uri->getQuery();
        }

        return $target;
    }

    /** {@inheritdoc} */
    public function withRequestTarget($requestTarget)
    {
        if (preg_match('#\s#', $requestTarget)) {
            throw new InvalidArgumentException(
                'Invalid request target provided; cannot contain whitespace'
            );
        }
        $this->requestTarget = $requestTarget;

        return $this;
    }

    /** {@inheritdoc} */
    public function getMethod()
    {
        return $this->method;
    }

    /** {@inheritdoc} */
    public function withMethod($method)
    {
        $this->method = strtoupper($method);

        return $this;
    }

    /** {@inheritdoc} */
    public function getUri()
    {
        return $this->uri;
    }

    /** {@inheritdoc} */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $this->uri = $uri;

        if (!$preserveHost) {
            if ($uri->getHost()) {
                $this->addHostHeader($uri);
            }
        }

        return $this;
    }
}
