<?php


namespace FlexShopper\Payments\Observer;


use FlexShopper\Payments\Model\Client;
use Magento\Framework\Event\Observer;

/**
 * Class Cancel
 * @deprecated See plugin around \Magento\Sales\Model\Order::cancel
 * @package FlexShopper\Payments\Observer
 */
class Cancel implements \Magento\Framework\Event\ObserverInterface
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
        $order = $observer->getEvent()->getOrder();
        $transactionId = $order->getFlexshopperTxid();
        if ($transactionId) {
            $this->client->cancelOrder($transactionId);
        }
    }
}
