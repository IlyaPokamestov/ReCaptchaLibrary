<?php
/*
 * This file is part of the ReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha\Tests\Http\Client;

use DS\Library\ReCaptcha\Client;
use DS\Library\ReCaptcha\Http\Client\SimpleClient;
use DS\Library\ReCaptcha\Http\Request;
use DS\Library\ReCaptcha\Http\Uri;

/**
 * ReCaptcha library, simple http file_get_content driver.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class SimpleClientTest extends \PHPUnit_Framework_TestCase
{
    public static $testUri = 'https://gist.githubusercontent.com/DarioSwain/e7e3d5653b96bdf15f30/raw/6fcd37fde96c0be03d39969b881c8789ed101bc2/test.json';

    public function testSend()
    {
        $client = new Client('test', new SimpleClient());
        $this->assertInstanceOf('DS\Library\ReCaptcha\Client', $client);
        $response = $client->send(new Request('GET', new Uri(self::$testUri)), false);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
        $this->assertEquals('{"success": "true"}', $response->getBody()->getContents());
    }
}
