<?php

namespace FlexShopper\Payments\Plugin;

use Magento\Rma\Model\Rma;
use FlexShopper\Payments\Model\Client;

class BeforeRma {
    
    /** @var Client  */
    private $client;

    /**
     * BeforeRma constructor.
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param Rma $subject
     */
    public function beforeBeforeSave(Rma $subject) {
        $flexTransactionId = $subject->getOrder()->getFlexshopperTxid();
        
        if($flexTransactionId) {
            $returnableItems = [];

            foreach($subject->getItems() as $rmaItem) {
                //Make sure this is executed only once
                if($rmaItem->getData('status') == Rma\Source\Status::STATE_RECEIVED &&
                    $rmaItem->getOrigData('status') != Rma\Source\Status::STATE_RECEIVED ) {
                    $returnableItems[]=$rmaItem;
                }
            }
            
            if(count($returnableItems)) {
                $this->client->rma($flexTransactionId, $returnableItems);
            }
        }
    }
}