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
 * DsReCaptcha library, simple http file_get_content driver.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class SimpleDriver implements DriverInterface
{
    /**
     * {@inheritdoc}
     */
    public function get($url, array $parameters = array())
    {
        $url = sprintf('%s?%s', $url, http_build_query($parameters));
        return file_get_contents($url);
    }
}
