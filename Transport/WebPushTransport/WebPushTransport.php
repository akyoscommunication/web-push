<?php

namespace Akyos\WebPush\Transport\WebPushTransport;

use Exception;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Symfony\Component\Notifier\Message\SentMessage;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Transport\AbstractTransport;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class WebPushTransport extends AbstractTransport
{
    private const TRANSPORT = 'webpush';

    public function __construct(private string $publicKey, private string $privateKey, private string $subject, HttpClientInterface $client = null, EventDispatcherInterface $dispatcher = null)
    {
        parent::__construct($client, $dispatcher);
    }

    protected function doSend(MessageInterface $message): SentMessage
    {

        $webPush = new WebPush([
            "VAPID" => [
                "subject" => $this->subject,
                "publicKey" => $this->publicKey,
                "privateKey" => $this->privateKey
            ]
        ]);
        $webPush->setReuseVAPIDHeaders(true);

        $token = str_replace('.', '', uniqid(random_int(10000000, 99999999), true));

        $options = $message->getOptions()->toArray();

        $webPush->queueNotification(
            Subscription::create(json_decode($message->getRecipientId(), true)),
            '{"token":"' . $token . '","type":"' . $options['type'] . '","title":"' . $message->getSubject() . '", "content":"' . $message->getContent() . '", "url": "' . $options['url'] . '", "logo":"' . $options['logo'] . '", "badge":"' . $options['badge'] . '"}',
            ['topic' => $options['type']]
        );

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            if (!$report->isSuccess()) {
                throw new Exception("Message failed to sent for subscription {$endpoint}: {$report->getReason()}");
            }
        }

        $sentMessage = new SentMessage($message, (string) $this);

        $sentMessage->setMessageId($token);

        return $sentMessage;
    }

    public function supports(MessageInterface $message): bool
    {
        return self::TRANSPORT === $message->getTransport();
    }

    public function __toString(): string
    {
        return self::TRANSPORT;
    }
}
