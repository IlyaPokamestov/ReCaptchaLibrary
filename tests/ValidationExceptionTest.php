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
use DS\Library\ReCaptcha\ValidationException;

/**
 * ReCaptcha library, client for google reCAPTCHA API.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class ValidationExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $exception = new ValidationException(array(), null, null);

        $this->assertEmpty($exception->getErrors());
        $this->assertNull($exception->getRequest());
        $this->assertNull($exception->getResponse());
    }
}
