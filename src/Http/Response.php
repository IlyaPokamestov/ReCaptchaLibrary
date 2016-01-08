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

use Psr\Http\Message\ResponseInterface;

/**
 * ReCaptcha library, PSR7 response representation.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * Class Response
 * @package DS\Library\ReCaptcha\Http
 */
class Response extends Message implements ResponseInterface
{
    /** @var null|string */
    protected $reasonPhrase = '';
    /** @var int */
    protected $statusCode = 200;

    /**
     * @param int    $status  Status code for the response, if any.
     * @param array  $headers Headers for the response, if any.
     * @param mixed  $body    Stream body.
     */
    public function __construct($status = 200, array $headers = array(), $body = null)
    {
        $this->statusCode = (int)$status;
        if ($body) {
            $this->stream = new Stream($body);
        }

        foreach ($headers as $headerName => $header) {
            //Some HTTP clients can provide header value in array format.
            if (is_array($header)) {
                $name = $headerName;
                $value = current($header);
            } else {
                list($name, $value) = explode(':', $header);
            }

            $values = explode(';', trim($value));
            foreach ($values as $headerValue) {
                $this->withAddedHeader($name, trim($headerValue));
            }
        }

        if (isset(Protocol::$phrases[$this->statusCode])) {
            $this->reasonPhrase = Protocol::$phrases[$this->statusCode];
        }
    }

    /** {@inheritdoc} */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /** {@inheritdoc} */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    /** {@inheritdoc} */
    public function withStatus($code, $reasonPhrase = '')
    {
        $this->statusCode = (int)$code;
        if (!$reasonPhrase && isset(Protocol::$phrases[$this->statusCode])) {
            $reasonPhrase = Protocol::$phrases[$this->statusCode];
        }
        $this->reasonPhrase = $reasonPhrase;

        return $this;
    }
}
