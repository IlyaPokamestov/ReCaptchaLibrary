<?php
/*
 * This file is part of the ReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha\Http\Client\Guzzle;

/**
 * ReCaptcha library, Guzzle client supported versions enum.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * Class Version
 * @package DS\Library\ReCaptcha\Http\Client\Guzzle
 */
final class Version
{
    const VERSION_SIX = 'six';
    const VERSION_FOUR_FIVE = 'four_or_five';
    const VERSION_THREE = 'three';

    public function getSupportedVersions()
    {
        return array(
            self::VERSION_THREE,
            self::VERSION_FOUR_FIVE,
            self::VERSION_SIX
        );
    }
}
