<?php


namespace FlexShopper\Payments\Observer;


use FlexShopper\Payments\Model\Client;
use Magento\Framework\Event\Observer;

class AfterShipment implements \Magento\Framework\Event\ObserverInterface
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
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        $flexshopperId = $order->getFlexshopperId();
        if ($flexshopperId) {
            $this->client->confirmShipment();
        }
    }
}
