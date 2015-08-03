<?php

namespace DS\Library\ReCaptcha\Tests\Http;

use DS\Library\ReCaptcha\Http\Stream;
use Psr\Http\Message\StreamInterface;

/**
 * Describes a data stream.
 *
 * Typically, an instance will wrap a PHP stream; this interface provides
 * a wrapper around the most common operations, including serialization of
 * the entire stream to a string.
 */
class StreamTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $stream = new Stream(array('test' => 'test'));
        $this->assertEquals('test=test', (string)$stream);

        $stream = new Stream(function(){return 'callable';});
        $this->assertEquals('callable', (string)$stream);

        $stream = new Stream('hello');
        $this->assertTrue($stream->isSeekable());
        $this->assertTrue($stream->isReadable());
        $this->assertTrue($stream->isWritable());

        $this->setExpectedException('RuntimeException', 'Callable must return string value');
        $stream = new Stream(function(){return array();});
    }

    public function testClose()
    {
        $stream = new Stream('reCAPTCHA');
        $this->assertTrue($stream->isReadable());
        $this->assertFalse($stream->eof());
        $stream->close();
        $this->assertFalse($stream->isReadable());
        $this->assertFalse($stream->isWritable());
        $this->assertFalse($stream->isSeekable());
        $this->assertNull($stream->detach());
        $this->assertEquals('', (string)$stream);

        $this->assertNull($stream->getSize());
    }

    public function testSize()
    {
        $stream = new Stream('reCAPTCHA');
        $this->assertEquals(9, $stream->getSize());
        $this->assertEquals(9, $stream->getSize());

        $stream = new Stream('');
        $this->assertEquals(0, $stream->getSize());
        $this->assertEquals('', (string)$stream);

        $stream->write(1);
        $file = fopen('php://input', 'r');
        $stream->setStream($file);
        $this->assertNull($stream->getSize());
    }

    public function testBadStream()
    {
        $file = fopen('php://temp', 'x');
        $stream = new Stream('new');

        $stream->setStream($file);
        $this->assertEquals('', $stream->getContents());
    }

    public function testContent()
    {
        $stream = new Stream('new');
        $stream->setStream(1);
        $this->setExpectedException('RuntimeException');
        $stream->getContents();
    }

    public function testTell()
    {
        $stream = new Stream('123');
        $this->assertEquals(0, $stream->tell());

        $stream->seek(1);
        $this->assertEquals(1, $stream->tell());

        $stream->rewind();
        $this->assertEquals(0, $stream->tell());

        $stream->setStream(1);
        $this->setExpectedException('RuntimeException');
        $stream->tell();
    }

    public function testSeekException()
    {
        $stream = new Stream('123');
        $this->assertEquals(0, $stream->tell());

        $this->setExpectedException('RuntimeException');
        $stream->seek(1000);
    }

    public function testRead()
    {
        $stream = new Stream('123');

        $this->assertEquals('1', $stream->read(1));

        $stream->detach();

        $this->setExpectedException('RuntimeException');
        $stream->read(12);
    }

    public function testWrite()
    {
        $stream = new Stream('123');

        $this->assertEquals(1, $stream->write('1'));

        $stream->detach();

        $this->setExpectedException('RuntimeException');
        $stream->write(12);
    }

    public function testMetadata()
    {
        $stream = new Stream('123');
        $stream->getMetadata();

        $this->assertEquals('php://temp', $stream->getMetadata('uri'));

        $stream = new Stream('1');
        $stream->setStream(null);

        $this->assertEquals(array(), $stream->getMetadata());
    }

    public function testNonStringStream()
    {
        $this->setExpectedException('InvalidArgumentException', '$resource type is invalid, support array and string only');
        $stream = new Stream(true);
    }
}
