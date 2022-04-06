<?php

namespace App\Notifier;

use App\Transport\WebPushTransport\WebPushOptions;
use Symfony\Component\Notifier\Message\PushMessage;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Component\Notifier\Notification\PushNotificationInterface;

class PushNotification extends Notification implements PushNotificationInterface
{
    private string $importance = Notification::IMPORTANCE_MEDIUM;

    public function __construct(
        private string $subject,
        private string $content,
        private string $logo,
        private string $badge,
        private ?string $url = null,
        private ?string $type = null,
        private array $channels = []
    ) {
    }

    public function asPushMessage(RecipientInterface $recipient, string $transport = null): ?PushMessage
    {
        return (new PushMessage($this->getSubject(), $this->getContent()))
            ->transport($transport)
            ->fromNotification($this)
            ->options(
                (new WebPushOptions([
                    'logo' => $this->logo,
                    'badge' => $this->badge,
                    'url' => $this->url,
                    'type' => $this->type
                ]))->recipient($recipient->getToken())
            );
    }

    public function getChannels(RecipientInterface $recipient): array
    {
        return ['push/webpush'];
    }

    /**
     * @return $this
     */
    public function subject(string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return $this
     */
    public function content(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return $this
     */
    public function importance(string $importance): static
    {
        $this->importance = $importance;

        return $this;
    }

    public function getImportance(): string
    {
        return $this->importance;
    }
}
