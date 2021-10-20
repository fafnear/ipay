<?php

namespace Fafnear\IPay;

use UnexpectedValueException;

trait UrlsSupport
{
    protected $urls = [];

    public function getUrls(): array
    {
        if (empty($this->urls)) {
            throw new UnexpectedValueException('Urls are not specified.');
        }

        return $this->urls;
    }

    public function addSuccessUrl(string $url)
    {
        $this->urls['good'] = $url;
    }

    public function addFailureUrl(string $url)
    {
        $this->urls['bad'] = $url;
    }
}