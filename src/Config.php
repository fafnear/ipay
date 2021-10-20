<?php

namespace Fafnear\IPay;

class Config
{
    const DEV_URL = 'https://api.sandbox.ipay.ua/';
    const PROD_URL = 'https://api.ipay.ua/';

    private $auth;
    private $data = [];

    public function __construct(Auth $auth, string $lang = 'ru', string $currency = 'UAH', int $lifetime = 24)
    {
        $this->auth = $auth;
        $this->data = compact('lang', 'currency', 'lifetime');
        $this->data['trademark'] = [];
        $this->data['url'] = self::PROD_URL;
    }

    public function setDevMode()
    {
        $this->data['url'] = self::DEV_URL;
    }

    public function getAuth(): Auth
    {
        return $this->auth;
    }

    public function getLang(): string
    {
        return $this->data['lang'];
    }

    public function getCurrency(): string
    {
        return $this->data['currency'];
    }

    public function addTrademark(string $lang, string $name)
    {
        $this->data['trademark'][$lang] = $name;
    }

    public function getTrademark(): array
    {
        return $this->data['trademark'];
    }

    public function getLifetime(): int
    {
        return $this->data['lifetime'];
    }

    public function getUrl(): string
    {
        return $this->data['url'];
    }
}