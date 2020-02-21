<?php


namespace FlexShopper\Payments\Model\Payment;

/**
 * Class FlexShopperPayments
 *
 * @package FlexShopper\Payments\Model\Payment
 */
class FlexShopperPayments extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = "flexshopperpayments";
    protected $_isOffline = true;

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        return parent::isAvailable($quote);
    }
}

