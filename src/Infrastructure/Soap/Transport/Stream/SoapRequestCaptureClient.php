<?php

namespace ANZ\BitUmc\SDK\Infrastructure\Soap\Transport\Stream;

use SoapClient;

final class SoapRequestCaptureClient extends SoapClient
{
    public string $request = '';

    public string $action = '';

    public function __doRequest($request, $location, $action, $version, $oneWay = false): ?string
    {
        $this->request = $request;
        $this->action = $action;

        return '';
    }
}
