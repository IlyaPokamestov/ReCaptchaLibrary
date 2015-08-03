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

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * ReCaptcha library, validation exception.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class ValidationException extends \Exception
{
    /** @var array */
    protected $errors = array();
    /** @var  RequestInterface */
    protected $request;
    /** @var  ResponseInterface */
    protected $response;

    public function __construct(
        array $errors,
        ResponseInterface $response = null,
        RequestInterface $request = null,
        $message = '',
        $code = 0,
        \Exception $previous = null
    ) {
        $this->errors = $errors;
        $this->request = $request;
        $this->response = $response;

        if ('' === $message) {
            $message = sprintf('Verification failed with errors: %s', implode(';', $errors));
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
