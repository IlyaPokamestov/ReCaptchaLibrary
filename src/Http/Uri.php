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

use Psr\Http\Message\UriInterface;

/**
 * PSR7 Uri representation.
 *
 * Class Uri
 * @package DS\Library\ReCaptcha\Http
 */
class Uri implements UriInterface
{
    /** @var string Uri scheme. */
    protected $scheme = '';
    /** @var string Uri user info. */
    protected $userInfo = '';
    /** @var string Uri host. */
    protected $host = '';
    /** @var int|null Uri port. */
    protected $port = null;
    /** @var string Uri path. */
    protected $path = '';
    /** @var string Uri query string. */
    protected $query = '';
    /** @var string Uri fragment. */
    protected $fragment = '';

    /**
     * @param string $uri URI to parse and wrap.
     */
    public function __construct($uri = '')
    {
        if ('' !== $uri) {
            $parts = parse_url($uri);
            //Schema and host should be defined
            if ($parts === false || count($parts) < 2) {
                throw new \InvalidArgumentException("Unable to parse URI: $uri");
            }
            $this->buildFromParts($parts);
        }
    }

    /** {@inheritdoc} */
    public function __toString()
    {
        $path = '';
        if ('' !== $this->getPath()) {
            $path = sprintf('/%s', ltrim($this->getPath(), '/'));
        }

        $query = '';
        if ('' !== $this->getQuery()) {
            $query = sprintf('?%s', $this->getQuery());
        }

        $fragment = '';
        if ('' !== $this->getFragment()) {
            $fragment = sprintf('#%s', $this->getFragment());
        }

        $uri = sprintf(
            '%s://%s%s%s%s',
            $this->getScheme(),
            $this->getAuthority(),
            $path,
            $query,
            $fragment
        );

        return $uri;
    }

    /** {@inheritdoc} */
    public function getScheme()
    {
        return $this->scheme;
    }

    /** {@inheritdoc} */
    public function getAuthority()
    {
        if ('' === $this->host) {
            return '';
        }

        $authority = $this->host;
        if ('' !== $this->userInfo) {
            $authority = sprintf('%s@%s', $this->userInfo, $authority);
        }

        if (null !== $this->port) {
            $authority = sprintf('%s:%d', $authority, $this->port);
        }

        return $authority;
    }

    /** {@inheritdoc} */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /** {@inheritdoc} */
    public function getHost()
    {
        return $this->host;
    }

    /** {@inheritdoc} */
    public function getPort()
    {
        return $this->port;
    }

    /** {@inheritdoc} */
    public function getPath()
    {
        return $this->path;
    }

    /** {@inheritdoc} */
    public function getQuery()
    {
        return $this->query;
    }

    /** {@inheritdoc} */
    public function getFragment()
    {
        return $this->fragment;
    }

    /** {@inheritdoc} */
    public function withScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /** {@inheritdoc} */
    public function withUserInfo($user, $password = null)
    {
        $info = $user;
        if ($password) {
            $info = sprintf('%s:%s', $info, $password);
        }
        $this->userInfo = $info;

        return $this;
    }

    /** {@inheritdoc} */
    public function withHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /** {@inheritdoc} */
    public function withPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /** {@inheritdoc} */
    public function withPath($path)
    {
        if (!is_string($path)) {
            throw new \InvalidArgumentException(
                'Invalid path provided; must be a string'
            );
        }
        $this->path = $path;

        return $this;
    }

    /** {@inheritdoc} */
    public function withQuery($query)
    {
        if (!is_string($query) && !is_array($query)) {
            throw new \InvalidArgumentException(
                'Query string must be a string or array'
            );
        }

        if (is_array($query)) {
            $query = http_build_query($query);
        }

        $this->query = ltrim($query, '?');

        return $this;
    }

    public function withFragment($fragment)
    {
        $this->fragment = ltrim($fragment, '#');

        return $this;
    }

    /**
     * Apply parse_url parts to a URI.
     *
     * @param $parts array of parse_url parts.
     */
    private function buildFromParts(array $parts)
    {
        $this->withScheme(isset($parts['scheme']) ? $parts['scheme'] : '')
            ->withUserInfo(
                isset($parts['user']) ? $parts['user'] : '',
                isset($parts['pass']) ? ':'.$parts['pass'] : ''
            )
            ->withHost(isset($parts['host']) ? $parts['host'] : '')
            ->withPort(!empty($parts['port']) ? $parts['port'] : null)
            ->withPath(isset($parts['path']) ? $parts['path'] : '')
            ->withQuery(isset($parts['query']) ? $parts['query'] : '')
            ->withFragment(isset($parts['fragment']) ? $parts['fragment'] : '');
    }
}
