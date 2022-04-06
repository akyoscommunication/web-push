<?php

namespace App\Transport\WebPushTransport;

use App\Notifier\PushNotification;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Message\MessageOptionsInterface;


final class WebPushOptions implements MessageOptionsInterface
{
    private $options;

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }


    public static function fromNotification(Notification $notification): self
    {
        $options = new self();
        $options->headings(['en' => $notification->getSubject()]);
        $options->contents(['en' => $notification->getContent()]);

        return $options;
    }

    public function headings(array $headings): self
    {
        $this->options['headings'] = $headings;

        return $this;
    }

    public function contents(array $contents): self
    {
        $this->options['contents'] = $contents;

        return $this;
    }

    public function url(string $url): self
    {
        $this->options['url'] = $url;

        return $this;
    }

    public function data(array $data): self
    {
        $this->options['data'] = $data;

        return $this;
    }

    public function recipient(string $id): self
    {
        $this->options['recipient_id'] = $id;

        return $this;
    }

    public function getRecipientId(): ?string
    {
        return $this->options['recipient_id'] ?? null;
    }

    public function toArray(): array
    {
        $options = $this->options;
        unset($options['recipient_id']);

        return $options;
    }
}
