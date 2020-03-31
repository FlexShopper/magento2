<?php

namespace FlexShopper\Payments\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesEventQuoteSubmitBeforeObserver
 * Please note, `etc/fieldset.xml` does not work on all Magento versions, therefore, this observer is required
 * to copy attributes between quote and order.
 * More info: https://magento.stackexchange.com/a/133258
 * @package FlexShopper\Payments\Observer
 */
class SalesEventQuoteSubmitBeforeObserver implements ObserverInterface
{
    /**
     * List of attributes that should be added to an order.
     *
     * @var array
     */
    private $attributes = [
        'flexshopper_id',
    ];


    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');
        /* @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        foreach ($this->attributes as $attribute) {
            if ($quote->hasData($attribute)) {
                $order->setData($attribute, $quote->getData($attribute));
            }
        }

        return $this;
    }
}
