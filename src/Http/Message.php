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

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * ReCaptcha library, PSR7 message implementation.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * Class Message
 * @package DS\Library\ReCaptcha\Http
 */
class Message implements MessageInterface
{
    /** @var array Cached HTTP header collection with lowercase key to values */
    protected $headers = array();
    /** @var string */
    protected $protocol = '1.1';
    /** @var StreamInterface */
    protected $stream;

    /** {@inheritdoc} */
    public function getProtocolVersion()
    {
        return $this->protocol;
    }

    /** {@inheritdoc} */
    public function withProtocolVersion($version)
    {
        $this->protocol = $version;

        return $this;
    }

    /** {@inheritdoc} */
    public function getHeaders()
    {
        return $this->headers;
    }

    /** {@inheritdoc} */
    public function hasHeader($header)
    {
        return isset($this->headers[strtolower($header)]);
    }

    /** {@inheritdoc} */
    public function getHeader($header)
    {
        $name = strtolower($header);

        return isset($this->headers[$name]) ? $this->headers[$name] : array();
    }

    /** {@inheritdoc} */
    public function getHeaderLine($header)
    {
        return implode(', ', $this->getHeader($header));
    }

    /** {@inheritdoc} */
    public function withHeader($header, $value)
    {
        $header = trim($header);
        $name = strtolower($header);
        if (!is_array($value)) {
            $this->headers[$name] = array(trim($value));
        } else {
            $this->headers[$name] = array_map('trim', $value);
        }

        return $this;
    }

    /** {@inheritdoc} */
    public function withAddedHeader($header, $value)
    {
        if (!$this->hasHeader($header)) {
            return $this->withHeader($header, $value);
        }
        $this->headers[strtolower($header)][] = $value;

        return $this;
    }

    /** {@inheritdoc} */
    public function withoutHeader($header)
    {
        if (!$this->hasHeader($header)) {
            return $this;
        }
        $name = strtolower($header);
        unset($this->headers[$name]);

        return $this;
    }

    /** {@inheritdoc} */
    public function getBody()
    {
        if (!$this->stream) {
            $this->stream = new Stream('');
        }

        return $this->stream;
    }

    /** {@inheritdoc} */
    public function withBody(StreamInterface $body)
    {
        $this->stream = $body;

        return $this;
    }
}
