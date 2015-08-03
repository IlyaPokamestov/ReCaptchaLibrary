<?php
/*
 * This file is part of the ReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha\Tests\Http;

use DS\Library\ReCaptcha\Http\Response;

/**
 * ReCaptcha library, response from google reCAPTCHA API.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $response = new Response(400, array('test: one; two'), 'stream');
        $this->assertInstanceOf('Psr\Http\Message\MessageInterface', $response);
        $this->assertInstanceOf('Psr\Http\Message\ResponseInterface', $response);
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('one, two', $response->getHeaderLine('test'));
        $this->assertEquals('stream', (string)$response->getBody());
        $this->assertEquals('Bad Request', $response->getReasonPhrase());
    }

    public function testStatusCode()
    {
        $response = new Response();
        $this->assertEquals(200, $response->getStatusCode());
        $response->withStatus(666, 'strange');
        $this->assertEquals(666, $response->getStatusCode());
        $this->assertEquals('strange', $response->getReasonPhrase());
        $response->withStatus(404);
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('Not Found', $response->getReasonPhrase());
    }
}
