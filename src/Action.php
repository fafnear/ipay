<?php

namespace Fafnear\IPay;

use GuzzleHttp\{
    Client, ClientInterface, Exception\TransferException
};
use Psr\Http\Message\ResponseInterface;

abstract class Action
{
    const ACTION_TYPE = null;

    protected $root = 'payment';
    protected $config;
    protected $body;
    protected $client;

    abstract public function execute();

    public function __construct(Config $config, ClientInterface $client = null)
    {
        $this->config = $config;
        $this->client = $client ?: new Client(['verify' => false]);
        $this->body = simplexml_load_string("<?xml version='1.0' encoding='utf-8' standalone='yes'?><{$this->root} />");
    }

    public function getType(): int
    {
        return static::ACTION_TYPE;
    }

    protected function initAuth()
    {
        $auth = $this->body->addChild('auth');
        $auth->addChild('mch_id', $this->config->getAuth()->getMerchantId());
        $auth->addChild('salt', $this->config->getAuth()->getSalt());
        $auth->addChild('sign', $this->config->getAuth()->getSign());
    }

    protected function sendRequest()
    {
        $response = $this->client->post($this->config->getUrl(), [
            'form_params' => ['data' => $this->body->saveXML()],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ]);

        return $this->prepareResponse($response);
    }

    protected function prepareResponse(ResponseInterface $response)
    {
        $contents = $response->getBody()->getContents();

        if ($loadedData = simplexml_load_string($contents)) {
            $loadedData = (array)$loadedData;

            if (isset($loadedData['error'])) {
                throw new TransferException($loadedData['error']->reason);
            }

            if (isset($loadedData['salt'], $loadedData['sign'])) {
                if (! $this->config->getAuth()->validate($loadedData['salt'], $loadedData['sign'])) {
                    throw new TransferException('The sign is invalid.');
                }
            }

            return $loadedData;
        }

        throw new TransferException('Invalid server response: ' . $contents);
    }
}