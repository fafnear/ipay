<?php

namespace Fafnear\IPay;

final class Auth
{
    private $mchId;
    private $signKey;
    private $salt;
    private $systemKey;

    public function __construct(int $mchId, string $signKey, string $systemKey)
    {
        $this->mchId = $mchId;
        $this->signKey = $signKey;
        $this->systemKey = $systemKey;
    }

    public function validate(string $salt, string $sign): bool
    {
        return hash_hmac('sha512', $salt , $this->systemKey) == $sign;
    }

    public function getMerchantId(): int
    {
        return $this->mchId;
    }

    public function getSalt(): string
    {
        if ($this->salt === null) {
            $this->salt = sha1(microtime(true));
        }

        return $this->salt;
    }

    public function getSign(): string
    {
        return hash_hmac('sha512', $this->getSalt(), $this->signKey);
    }
}