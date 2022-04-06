<?php

namespace App\Recipient;

use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Component\Notifier\Exception\InvalidArgumentException;

class PushRecipient implements RecipientInterface
{

    public function __construct(private string $token = '')
    {
        if ('' === $token) {
            throw new InvalidArgumentException('The token must not be empty.');
        }
    }

    /**
     * Get the value of token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set the value of token
     *
     * @return  self
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }
}
