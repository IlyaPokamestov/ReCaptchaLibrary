<?php
/*
 * This file is part of the ReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha\Tests;

use DS\Library\ReCaptcha\Client;
use DS\Library\ReCaptcha\Http\Request;
use DS\Library\ReCaptcha\Http\Response;
use DS\Library\ReCaptcha\Http\Stream;
use DS\Library\ReCaptcha\Http\Uri;

/**
 * ReCaptcha library, client for google reCAPTCHA API.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $secret = 'secret';

        $client = new Client($secret);
        $this->assertInstanceOf('DS\Library\ReCaptcha\Client', $client);
        $this->assertEquals($secret, $client->getSecret());

        $client = new Client($secret, null, new Uri(Client::$siteVerifyUri));
        $this->assertInstanceOf('DS\Library\ReCaptcha\Client', $client);
    }

    public function testCreateBody()
    {
        $client = new Client('secret');
        $this->assertInstanceOf('DS\Library\ReCaptcha\Client', $client);

        $stream = $client->createBody('token', '1.1.1.1');
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $stream);

        $content = $stream->getContents();
        $this->assertEquals('secret=secret&response=token&remoteip=1.1.1.1', $content);
    }

    public function testSend()
    {
        $simpleClient = $this->getMockBuilder('stdClass')
            ->setMethods(array('send'))
            ->getMock();
        $simpleClient->expects($this->once())->method('send')->will($this->returnValue(new Response()));

        $client = new Client('secret', $simpleClient);
        $this->assertInstanceOf('DS\Library\ReCaptcha\Client', $client);

        $request = new Request('post', 'https://developers.google.com/recaptcha/');
        $response = $client->send($request);

        $this->assertEquals(200, $response->getStatusCode());

        $client = new Client('secret', new \stdClass());
        $this->assertInstanceOf('DS\Library\ReCaptcha\Client', $client);

        $request = new Request('post', 'https://developers.google.com/recaptcha/');
        $this->setExpectedException('RuntimeException', 'HTTP Client must implement method send, and return ResponseInterface');
        $client->send($request);
    }

    public function testIsValidException()
    {
        $response = $this->getMockBuilder('DS\Library\ReCaptcha\Http\Response')
            ->setMethods(array('getBody'))
            ->getMock();
        $response->expects($this->once())->method('getBody')->will($this->returnValue(
            new Stream(json_encode(array('error-codes' => array(1))))
        ));

        $client = new Client('secret');
        $this->assertInstanceOf('DS\Library\ReCaptcha\Client', $client);

        $this->setExpectedException('DS\Library\ReCaptcha\ValidationException');
        $client->isValid($response);
    }

    public function testIsValidSuccess()
    {
        $client = new Client('secret');
        $this->assertInstanceOf('DS\Library\ReCaptcha\Client', $client);

        $responseSuccess = $this->getMockBuilder('DS\Library\ReCaptcha\Http\Response')
            ->setMethods(array('getBody'))
            ->getMock();
        $responseSuccess->expects($this->once())->method('getBody')->will($this->returnValue(
            new Stream(json_encode(array('success' => true)))
        ));

        $this->assertTrue($client->isValid($responseSuccess));
    }

    public function testIsValidFail()
    {
        $client = new Client('secret');
        $this->assertInstanceOf('DS\Library\ReCaptcha\Client', $client);

        $responseFail = $this->getMockBuilder('DS\Library\ReCaptcha\Http\Response')
            ->setMethods(array('getBody'))
            ->getMock();
        $responseFail->expects($this->once())->method('getBody')->will($this->returnValue(
            new Stream(json_encode(array('success' => false)))
        ));

        $this->assertFalse($client->isValid($responseFail));
    }

    public function testValidateRequest()
    {
        $simpleClient = $this->getMockBuilder('stdClass')
            ->setMethods(array('send'))
            ->getMock();
        $simpleClient->expects($this->once())->method('send')->will($this->returnValue(
            new Response(200, array(), json_encode(array('success' => true)))
        ));

        $client = new Client('secret', $simpleClient);
        $this->assertInstanceOf('DS\Library\ReCaptcha\Client', $client);

        $request = new Request('post', 'https://developers.google.com/recaptcha/');
        $result = $client->validateRequest($request);

        $this->assertTrue($result);
    }

    public function testValidate()
    {
        $simpleClient = $this->getMockBuilder('DS\Library\ReCaptcha\Http\Client\SimpleClient')
            ->setMethods(array('send'))
            ->getMock();
        $simpleClient->expects($this->once())->method('send')->will($this->returnValue(
            new Response(200, array(), json_encode(array('success' => true)))
        ));

        $client = new Client('secret', $simpleClient);
        $this->assertInstanceOf('DS\Library\ReCaptcha\Client', $client);

        $request = new Request('post', 'https://developers.google.com/recaptcha/');
        $result = $client->validate('token', '1.1.1.1');

        $this->assertTrue($result);
    }
}
