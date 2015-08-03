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

use DS\Library\ReCaptcha\Http\Message;
use DS\Library\ReCaptcha\Http\Stream;
use Psr\Http\Message\StreamInterface;

/**
 * ReCaptcha library, request to google reCAPTCHA API.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    public function testProtocolVersion()
    {
        $message = new Message();
        $this->assertInstanceOf('Psr\Http\Message\MessageInterface', $message);
        $message->withProtocolVersion('test');
        $this->assertEquals('test', $message->getProtocolVersion());
    }

    public function testHeaders()
    {
        $message = new Message();
        $message->withHeader('type', 'new');
        $this->assertTrue($message->hasHeader('type'));
        $this->assertEquals(array('new'), $message->getHeader('type'));
        $this->assertEquals('new', $message->getHeaderLine('type'));
        $this->assertEquals(array('type' => array('new')), $message->getHeaders());
        $message->withAddedHeader('type', 'old');
        $this->assertEquals('new, old', $message->getHeaderLine('type'));
        $message->withHeader('temp', 'temp');
        $this->assertTrue($message->hasHeader('temp'));
        $message->withoutHeader('temp');
        $this->assertFalse($message->hasHeader('temp'));
        $newMessage = $message->withoutHeader('not_found');
        $this->assertInstanceOf('Psr\Http\Message\MessageInterface', $newMessage);
        $message->withHeader('array', array('one', ' two'));
        $this->assertEquals('one, two', $message->getHeaderLine('array'));

    }

    public function testBody()
    {
        $message = new Message();
        $this->assertInstanceOf('Psr\Http\Message\StreamInterface', $message->getBody());
        $this->assertEquals('', (string)$message->getBody());
        $message->withBody(new Stream('test'));
        $this->assertEquals('test', (string)$message->getBody());
    }
}