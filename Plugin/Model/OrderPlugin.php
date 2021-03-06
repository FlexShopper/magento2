<?php

namespace FlexShopper\Payments\Plugin\Model;

use FlexShopper\Payments\Exception\InvalidFlexshopperResponse;
use FlexShopper\Payments\Model\Client;

class OrderPlugin
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


    public function beforeCancel(\Magento\Sales\Model\Order $subject)
    {
        $transactionId = $subject->getFlexshopperTxid();
        if ($transactionId) {
            $result = $this->client->cancelOrder($transactionId);
            if ($result === false) {
                $errorMessage = $this->client->errorMessage;
                throw new InvalidFlexshopperResponse(__("Invalid response from FlexShopper, order can't be canceled: $errorMessage"));
            }
        }
    }
}
