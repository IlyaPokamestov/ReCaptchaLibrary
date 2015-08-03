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

use DS\Library\ReCaptcha\Http\Request;
use DS\Library\ReCaptcha\Http\Uri;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * ReCaptcha library, request to google reCAPTCHA API.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $request = new Request('post', 'https://developers.google.com/recaptcha/');
        $this->assertEquals('POST', $request->getMethod());
        $this->assertInstanceOf('Psr\Http\Message\MessageInterface', $request);
        $this->assertInstanceOf('Psr\Http\Message\RequestInterface', $request);
        $this->assertInstanceOf('Psr\Http\Message\UriInterface', $request->getUri());
        $this->assertEquals('https://developers.google.com/recaptcha/', (string)$request->getUri());

        $request = new Request('post', 'https://developers.google.com/recaptcha/');
        $this->assertEquals('developers.google.com', $request->getHeaderLine('host'));
    }

    public function testAddHostHeader()
    {
        $uri = new Uri('https://developers.google.com/recaptcha/');
        $uri->withPort('2222');
        $request = new Request('post', $uri);
        $this->assertEquals('developers.google.com:2222', $request->getHeaderLine('host'));
    }

    public function testRequestTarget()
    {
        $uri = new Uri();
        $request = new Request('post', $uri);
        $this->assertEquals('/', $request->getRequestTarget());

        $uri->withPath('developers.google.com/recaptcha/')
            ->withQuery(array('test' => 'true'));
        $request = new Request('post', $uri);
        $this->assertEquals('developers.google.com/recaptcha/?test=true', $request->getRequestTarget());

        $uri = new Uri('https://google.com/recaptcha/');
        $request = new Request('post', $uri);
        $this->assertEquals('/recaptcha/', $request->getRequestTarget());

        $request->withRequestTarget('/test');
        $this->assertEquals('/test', $request->getRequestTarget());

        $this->setExpectedException('InvalidArgumentException', 'Invalid request target provided; cannot contain whitespace');
        $request->withRequestTarget('   ');
    }

    public function testMethod()
    {
        $request = new Request('post', 'https://developers.google.com/recaptcha/');
        $this->assertEquals('POST', $request->getMethod());
        $request->withMethod('get');
        $this->assertEquals('GET', $request->getMethod());
    }

    public function testUri()
    {
        $request = new Request('post', 'https://developers.google.com/recaptcha/');
        $request->withUri(new Uri('http://test.com'));
        $this->assertEquals('http://test.com', (string)$request->getUri());
        $this->assertEquals('test.com', $request->getHeaderLine('host'));

    }
}
