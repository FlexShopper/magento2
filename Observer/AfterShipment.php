<?php


namespace FlexShopper\Payments\Observer;


use FlexShopper\Payments\Model\Client;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order\Shipment;

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
        /** @var Shipment $shipment */
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();
        if ($shipment->getTotalQty() == $order->getTotalQtyOrdered()) {
            $items = false;
        } else {
            $items = $shipment->getAllItems();
        }
        $flexshopperId = $order->getFlexshopperId();
        if ($flexshopperId) {
            $this->client->confirmShipment($flexshopperId, 'ground', $items);
        }
    }
}
