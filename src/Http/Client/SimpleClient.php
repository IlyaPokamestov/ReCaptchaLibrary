<?php
/*
 * This file is part of the ReCaptcha Library.
 *
 * (c) Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace DS\Library\ReCaptcha\Http\Client;

use DS\Library\ReCaptcha\Http\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * ReCaptcha library, simple http file_get_content client.
 *
 * @author Ilya Pokamestov <dario_swain@yahoo.com>
 *
 * Class SimpleClient
 * @package DS\Library\ReCaptcha\Http\Client
 */
class SimpleClient implements ClientInterface
{
    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function send(RequestInterface $request)
    {
        $headers = array();
        foreach ($request->getHeaders() as $name => $values) {
             $headers[] = sprintf('%s: %s', $name, implode(', ', $values));
        }

        $options = array(
            'http' => array(
                'header'  => implode("\r\n", $headers),
                'method'  => (string)$request->getMethod(),
                'content' => (string)$request->getBody(),
            ),
        );

        $response = file_get_contents(
            (string)$request->getUri(),
            false,
            stream_context_create($options)
        );

        $status = 400;
        $protocol = null;

        if (isset($http_response_header) && is_array($http_response_header)) {
            $statusLine = array_shift($http_response_header);
            $statusHeaderPattern = '#^HTTP/(?P<version>[1-9]\d*\.\d) (?P<status>[1-5]\d{2})(\s+(?P<reason>.+))?$#';
            if (preg_match($statusHeaderPattern, $statusLine, $matches)) {
                $status = $matches['status'];
                $protocol = $matches['version'];
            }
        }

        $response = new Response($status, $http_response_header, $response === false ? null : $response);
        $response->withProtocolVersion($protocol);

        return $response;
    }
}
