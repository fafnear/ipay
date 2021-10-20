<?php

namespace Fafnear\IPay\Action;

use UnexpectedValueException;
use Fafnear\IPay\{
    Action, Payment\Transaction, UrlsSupport
};

class Create extends Action
{
    use UrlsSupport;

    const ACTION_TYPE = 11;

    private $transactions = [];

    public function execute()
    {
        $this->initAuth();
        $this->initUrls();
        $this->initTransactions();
        $this->initFooter();

        return $this->sendRequest();
    }

    public function addTransaction(Transaction $transaction)
    {
        $this->transactions[] = $transaction;
    }

    public function getTransactions(): array
    {
        if (empty($this->transactions)) {
            throw new UnexpectedValueException('No one transaction has been initialized.');
        }

        return $this->transactions;
    }

    private function initUrls()
    {
        $urls = $this->getUrls();
        $urlsNode = $this->body->addChild('urls');

        foreach ($urls as $type => $url) {
            $urlsNode->addChild($type, $url);
        }
    }

    private function initTransactions()
    {
        $transactions = $this->body->addChild('transactions');

        foreach ($this->getTransactions() as $transaction) {
            /**
             * @var Transaction $transaction
             */
            $transactionNode = $transactions->addChild('transaction');
            $info = $transaction->info();
            $info['type'] = $this->getType();
            $info['mch_id'] = $this->config->getAuth()->getMerchantId();
            $info['srv_id'] = 0;

            if (! isset($info['currency'])) {
                $info['currency'] = $this->config->getCurrency();
            }

            foreach ($info as $item => $value) {
                $transactionNode->addChild($item, $value);
            }
        }
    }

    private function initFooter()
    {
        if ($tradeMark = $this->config->getTrademark()) {
            $this->body->addChild('trademark', json_encode($tradeMark));
        }

        $this->body->addChild('lifetime', $this->config->getLifetime());
        $this->body->addChild('lang', $this->config->getLang());
        #$this->body->addChild('version', '3.00');
    }
}
