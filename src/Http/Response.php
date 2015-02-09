<?php
/*
 * This file is part of the DSReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha\Http;

/**
 * DsReCaptcha library, response from google reCAPTCHA API.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class Response
{
    /** @var bool */
    protected $success;
    /** @var array */
    protected $errorCodes;

    /**
     * @param bool $success
     * @param array $errors
     */
    public function __construct($success, array $errors = array())
    {
        $this->success = $success;
        $this->errorCodes = $errors;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errorCodes;
    }
}