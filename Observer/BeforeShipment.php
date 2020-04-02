<?php


namespace FlexShopper\Payments\Observer;

use FlexShopper\Payments\Exception\InvalidFlexshopperResponse;
use FlexShopper\Payments\Model\Client;
use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order\Shipment;

class BeforeShipment implements \Magento\Framework\Event\ObserverInterface
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
            $result = $this->client->confirmShipment($flexshopperId, 'ground', $items);
            if ($result === false) {
                throw new InvalidFlexshopperResponse(__("Invalid response from FlexShopper, shipment can't proceed."));
            }
        }
    }
}
