<?php
/*
 * This file is part of the DSReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha\Http\Driver;

/**
 * DsReCaptcha library, interface for implement custom drivers, for example CurlDriver, etc.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
interface DriverInterface
{
    /**
     * @param string $url
     * @param array $parameters
     * @return string|bool
     */
    public function get($url, array $parameters = array());
}
