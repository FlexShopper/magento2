<?php


namespace FlexShopper\Payments\Observer;


use FlexShopper\Payments\Model\Client;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Rma\Model\Rma;

class RmaSaveBeforeObserver implements ObserverInterface
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
        /** @var Rma $rma */
        $rma = $observer->getEvent()->getShipment();
        $order = $rma->getOrder();
        if (count($rma->getItems()) == $order->getTotalQtyOrdered()) {
            $items = false;
        } else {
            $items = $rma->getItems();
        }
        $transactionId = $order->getFlexshopperTxid();

        if ($transactionId) {
            $this->client->rma($transactionId, $items);
        }
    }
}
