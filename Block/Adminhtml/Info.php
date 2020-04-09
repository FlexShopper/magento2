<?php


namespace FlexShopper\Payments\Block\Adminhtml;


class Info extends \Magento\Backend\Block\Template
{
    public function getFlexShopperId()
    {
        $layout = $this->getLayout();
        $paymentBlock = $layout->getBlock('order_payment');
        return $paymentBlock->getParentBlock()->getOrder()->getFlexshopperId();
    }

    public function getFlexShopperTxId()
    {
        $layout = $this->getLayout();
        $paymentBlock = $layout->getBlock('order_payment');
        return $paymentBlock->getParentBlock()->getOrder()->getFlexshopperTxid();
    }
}
