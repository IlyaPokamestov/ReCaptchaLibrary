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
 * DsReCaptcha library, driver for unit tests.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 */
class TestDriver implements DriverInterface
{
    /** @var bool */
    protected $success;
    /** @var bool */
    protected $empty;

    /**
     * If success = true, then validation passed
     *
     * @param bool $success
     * @param bool $empty
     */
    public function __construct($success = true, $empty = false)
    {
        $this->success = $success;
        $this->empty = $empty;
    }

    /**
     * {@inheritdoc}
     */
    public function get($url, array $parameters = array())
    {
        if ($this->empty) {
            return false;
        }

        if ($this->success) {
            return sprintf('{"success": %s}', 'true');
        } else {
            return sprintf('{"success": %s, "error-codes": ["%s"]}', 'false', 'invalid-input-response');
        }
    }
}
