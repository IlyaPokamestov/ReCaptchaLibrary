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
class UriTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $this->setExpectedException('InvalidArgumentException');
        new Uri(1);
    }

    public function testToString()
    {
        $uri = new Uri('http://google.com?hi=1#active');
        $this->assertEquals('http://google.com?hi=1#active', $uri->__toString());

        $uri->withUserInfo('dario', 'swain');
        $this->assertEquals('dario:swain', $uri->getUserInfo());

        $uri->withPort(3008);
        $this->assertEquals('dario:swain@google.com:3008', $uri->getAuthority());

        $uri->withHost('');
        $this->assertEmpty($uri->getAuthority());

    }

    public function testQueryException()
    {
        $uri = new Uri('http://google.com');

        $this->setExpectedException('InvalidArgumentException');
        $uri->withQuery(1);
    }

    public function testPathException()
    {
        $uri = new Uri('http://google.com');

        $this->setExpectedException('InvalidArgumentException');
        $uri->withPath(1);
    }
}
