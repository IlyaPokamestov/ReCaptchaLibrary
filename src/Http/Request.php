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

use DS\Library\ReCaptcha\Http\Driver\DriverInterface;
use DS\Library\ReCaptcha\Http\Driver\SimpleDriver;

/**
 * DsReCaptcha library, request to google reCAPTCHA API.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class Request
{
    /** @var DriverInterface */
    protected $driver;
    /** @var  string */
    protected $url;
    /** @var array */
    protected $parameters;

    /**
     * @param string $url
     * @param DriverInterface $driver
     */
    public function __construct($url, DriverInterface $driver = null)
    {
        $this->url = $url;
        $this->parameters = array();


        if(null === $driver)
        {
            $driver = new SimpleDriver();
        }

        $this->driver = $driver;
    }

    /**
     * @param DriverInterface $driver
     * @return $this
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    /**
     * @return DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return Response
     */
    public function send()
    {
        $response = $this->driver->get($this->getUrl(), $this->getParameters());

        if(false === $response)
        {
            return new Response(false, array('Connection error'));
        }

        $response = json_decode($response, true);

        if ($response['success'] === true)
        {
            return new Response(true);
        }
        else
        {
            return new Response(false, array_key_exists('error-codes', $response) ? $response['error-codes'] : array());
        }
    }
}