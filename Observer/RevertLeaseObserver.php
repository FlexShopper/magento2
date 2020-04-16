<?php


namespace FlexShopper\Payments\Observer;


use FlexShopper\Payments\Model\Client;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Rma\Model\Rma;

class RevertLeaseObserver implements ObserverInterface
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(
        Client $client
    ) {
        $this->client = $client;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        /** @var Quote $quote */
        $quote = $observer->getEvent()->getQuote();

        $transactionId = $quote->getFlexshopperTxid();

        if ($transactionId) {
            $this->client->cancelOrder($transactionId);
        }
    }
}
