<?php

namespace App\Transport\WebPushTransport;

use Symfony\Component\Notifier\Transport\Dsn;
use Symfony\Component\Notifier\Transport\TransportInterface;
use Symfony\Component\Notifier\Transport\AbstractTransportFactory;
use Symfony\Component\Notifier\Exception\UnsupportedSchemeException;

class WebPushTransportFactory extends AbstractTransportFactory
{
    protected function getSupportedSchemes(): array
    {
        return ['webpush'];
    }

    public function create(Dsn $dsn): TransportInterface
    {
        $scheme = $dsn->getScheme();

        if ('webpush' !== $scheme) {
            throw new UnsupportedSchemeException($dsn, 'webpush', $this->getSupportedSchemes());
        }

        $publicKey = $this->getUser($dsn);
        $privateKey = $this->getPassword($dsn);
        $host = urldecode($dsn->getHost());

        return (new WebPushTransport($publicKey, $privateKey, $host, $this->client, $this->dispatcher));
    }
}
