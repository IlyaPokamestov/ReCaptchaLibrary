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

use Psr\Http\Message\StreamInterface;

/**
 * Describes a PSR7 data stream.
 *
 * Typically, an instance will wrap a PHP stream; this interface provides
 * a wrapper around the most common operations, including serialization of
 * the entire stream to a string.
 *
 * Class Stream
 * @package DS\Library\ReCaptcha\Http
 */
class Stream implements StreamInterface
{
    /** @var resource */
    protected $stream;
    /** @var array|mixed|null */
    protected $seekable;
    /** @var bool */
    protected $readable;
    /** @var bool */
    protected $writable;
    /** @var int */
    protected $size;
    /** @var array */
    protected $meta;

    /**
     * Create new instance of Stream from string, array or callable.
     *
     * Stream constructor.
     * @param $resource array|string|callable
     */
    public function __construct($resource)
    {
        if (is_array($resource)) {
            $resource = http_build_query($resource);
        }

        if (is_callable($resource)) {
            $resource = call_user_func($resource);
            if (!is_string($resource)) {
                throw new \RuntimeException('Callable must return string value.');
            }
        }

        if (!is_string($resource)) {
            throw new \InvalidArgumentException('$resource type is invalid, support array and string only');
        }

        $stream = fopen('php://temp', 'r+');
        if ($resource !== '') {
            fwrite($stream, $resource);
            fseek($stream, 0);
        }

        $this->stream = $stream;
        $this->seekable = $this->getMetadata('seekable');
        //Readable and writable is true for r+ mode
        $this->readable = true;
        $this->writable = true;
    }

    /**
     * Set stream resource.
     *
     * @param $stream resource
     */
    public function setStream($stream)
    {
        $this->stream = $stream;
    }

    /**
     * Closes the stream when the destructed
     */
    public function __destruct()
    {
        $this->close();
    }

    /** @inheritdoc */
    public function __toString()
    {
        try {
            $this->seek(0);
            return (string)stream_get_contents($this->stream);
        } catch (\Exception $e) {
            return '';
        }
    }

    /** @inheritdoc */
    public function getContents()
    {
        if (!is_resource($this->stream)) {
            throw new \RuntimeException('Unable to read stream contents');
        }

        $contents = stream_get_contents($this->stream);

        return $contents;
    }

    /** @inheritdoc */
    public function close()
    {
        if (isset($this->stream)) {
            if (is_resource($this->stream)) {
                fclose($this->stream);
            }
            $this->detach();
        }
    }

    /** @inheritdoc */
    public function detach()
    {
        if (!isset($this->stream)) {
            return null;
        }

        $result = $this->stream;
        unset($this->stream);
        $this->readable = $this->writable = $this->seekable = false;

        return $result;
    }

    /** @inheritdoc */
    public function getSize()
    {
        if (!isset($this->stream)) {
            return null;
        }

        if ($this->size !== null) {
            return $this->size;
        }

        $stats = fstat($this->stream);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];
            return $this->size;
        }

        return null;
    }

    /** @inheritdoc */
    public function isReadable()
    {
        return $this->readable;
    }

    /** @inheritdoc */
    public function isWritable()
    {
        return $this->writable;
    }

    /** @inheritdoc */
    public function isSeekable()
    {
        return $this->seekable;
    }

    /** @inheritdoc */
    public function eof()
    {
        return !$this->stream || feof($this->stream);
    }

    /** @inheritdoc */
    public function tell()
    {
        if (!is_resource($this->stream)) {
            throw new \RuntimeException('Unable to read stream contents');
        }

        $result = ftell($this->stream);

        return $result;
    }

    /** @inheritdoc */
    public function rewind()
    {
        $this->seek(0);
    }

    /** @inheritdoc */
    public function seek($offset, $whence = SEEK_SET)
    {
        if (!$this->seekable) {
            throw new \RuntimeException('Stream is not seekable');
        } elseif (fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException(
                sprintf('Unable to seek to stream position %s with whence %s', $offset, var_export($whence, true))
            );
        }
    }

    /** @inheritdoc */
    public function read($length)
    {
        if (!$this->readable) {
            throw new \RuntimeException('Cannot read from non-readable stream');
        }

        return fread($this->stream, $length);
    }

    /** @inheritdoc */
    public function write($string)
    {
        if (!$this->writable) {
            throw new \RuntimeException('Cannot write to a non-writable stream');
        }

        $this->size = null;
        $result = fwrite($this->stream, $string);

        return $result;
    }

    /** @inheritdoc */
    public function getMetadata($key = null)
    {
        if (!isset($this->stream)) {
            return $this->meta = $key ? null : array();
        } elseif (isset($this->meta[$key])) {
            return $this->meta[$key];
        }
        $this->meta = stream_get_meta_data($this->stream);

        return isset($this->meta[$key]) ? $this->meta[$key] : null;
    }
}
