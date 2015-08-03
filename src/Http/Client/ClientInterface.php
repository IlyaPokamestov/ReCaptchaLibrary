<?php
/*
 * This file is part of the ReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha\Http\Client;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * ReCaptcha library, interface for implement custom drivers, for example CurlClient, etc.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * Interface ClientInterface
 * @package DS\Library\ReCaptcha\Http\Client
 */
interface ClientInterface
{
    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function send(RequestInterface $request);
}
