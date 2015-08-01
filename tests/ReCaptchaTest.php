<?php
/*
 * This file is part of the DSReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha\Tests;
use DS\Library\ReCaptcha\Http\Driver\SimpleDriver;
use DS\Library\ReCaptcha\Http\Request;
use DS\Library\ReCaptcha\ReCaptcha;
use DS\Library\ReCaptcha\Http\Driver\TestDriver;

/**
 * DsReCaptcha library, test suit.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class ReCaptchaTest extends \PHPUnit_Framework_TestCase
{
    /** @var ReCaptcha */
    protected $reCaptcha;

    public function testReCaptchaValidateSuccess()
    {
        $this->reCaptcha = new ReCaptcha('secret', '127.0.0.1', 'google_response');
        $this->assertInstanceOf('DS\Library\ReCaptcha\ReCaptcha', $this->reCaptcha);
        $testDriver = new TestDriver(true);
        $response = $this->reCaptcha->buildRequest($testDriver)->send();
        $this->assertInstanceOf('DS\Library\ReCaptcha\Http\Response', $response);
        $this->assertTrue($response->isSuccess());
    }

    public function testReCaptchaValidateFailed()
    {
        $this->reCaptcha = new ReCaptcha('secret', '127.0.0.1', 'google_response');
        $this->assertInstanceOf('DS\Library\ReCaptcha\ReCaptcha', $this->reCaptcha);
        $testDriver = new TestDriver(false);

        $response = $this->reCaptcha->buildRequest($testDriver)->send();
        $this->assertInstanceOf('DS\Library\ReCaptcha\Http\Response', $response);
        $this->assertFalse($response->isSuccess());
        $this->assertNotEmpty($response->getErrors());
        $errors = $response->getErrors();
        $this->assertEquals($errors[0], 'invalid-input-response');
    }

    public function testSimpleDriver()
    {
        $driver = new SimpleDriver();
        $this->assertJsonStringEqualsJsonString('{"success": "true"}', $driver->get('http://echo.jsontest.com/success/true'));
    }

    public function testWrongSecretInReCaptcha()
    {
        $this->setExpectedException('Exception', sprintf('To use reCAPTCHA you must get an API key from <a href="%s">%s</a>', ReCaptcha::$signupUrl, ReCaptcha::$signupUrl));
        $this->reCaptcha = new ReCaptcha(null, '127.0.0.1', 'google_response');
    }

    public function testRequestDriver()
    {
        $request = new Request(ReCaptcha::$siteVerifyUrl);
        $this->assertInstanceOf('DS\Library\ReCaptcha\Http\Request', $request);
        $this->assertInstanceOf('DS\Library\ReCaptcha\Http\Driver\SimpleDriver', $request->getDriver());
        $request->setDriver(new TestDriver(true));
        $this->assertInstanceOf('DS\Library\ReCaptcha\Http\Driver\TestDriver', $request->getDriver());
    }

    public function testRequestUrl()
    {
        $request = new Request(ReCaptcha::$siteVerifyUrl);
        $this->assertEquals(ReCaptcha::$siteVerifyUrl, $request->getUrl());
        $request->setUrl('google.com');
        $this->assertEquals('google.com', $request->getUrl());
    }

    public function testEmptyResponse()
    {
        $request = new Request(ReCaptcha::$siteVerifyUrl, new TestDriver(true, true));
        $response = $request->send();
        $this->assertInstanceOf('DS\Library\ReCaptcha\Http\Response', $response);
        $this->assertFalse($response->isSuccess());
    }

    public function testClientIp() {
        $this->reCaptcha = new ReCaptcha('secret', '127.0.0.1', 'google_response');
        $this->assertEquals($this->reCaptcha->getClientIp(), '127.0.0.1');
        $this->reCaptcha->setClientIp('127.0.0.2');
        $this->assertNotEquals($this->reCaptcha->getClientIp(), '127.0.0.1');
        $this->assertEquals($this->reCaptcha->getClientIp(), '127.0.0.2');
    }

    public function testGResponse() {
        $this->reCaptcha = new ReCaptcha('secret', '127.0.0.1', 'google_response');
        $this->assertEquals($this->reCaptcha->getGReCaptchaResponse(), 'google_response');
        $this->reCaptcha->setGReCaptchaResponse('google_response123');
        $this->assertNotEquals($this->reCaptcha->getGReCaptchaResponse(), 'google_response');
        $this->assertEquals($this->reCaptcha->getGReCaptchaResponse(), 'google_response123');
    }

    public function testWithoutIp() {
        $this->setExpectedException('Exception', 'Client IP is required.');
        $this->reCaptcha = new ReCaptcha('secret');
        $this->reCaptcha->buildRequest();
    }

    public function testWithoutGResponse() {
        $this->setExpectedException('Exception', 'G reCaptcha response token is required.');
        $this->reCaptcha = new ReCaptcha('secret', '127.0.0.1');
        $this->reCaptcha->buildRequest();
    }
}
