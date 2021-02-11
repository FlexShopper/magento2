<?php


namespace FlexShopper\Payments\Helper;


use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    const CONFIG_AUTH_KEY = 'payment/flexshopperpayments/auth_key';
    const CONFIG_SANDBOX_FLAG = 'payment/flexshopperpayments/sandbox_flag';
    const CONFIG_API_KEY = 'payment/flexshopperpayments/api_key';
    const CONFIG_MIN_ORDER_VALUE = 'payment/flexshopperpayments/minimum_order_value';
    const CONFIG_BRAND_ATTRIBUTE = 'payment/flexshopperpayments/brand';
    const CONFIG_AUTO_INVOICE = 'payment/flexshopperpayments/auto_invoice';

    public function getMode()
    {
        if ($this->scopeConfig->getValue(self::CONFIG_SANDBOX_FLAG)) {
            return 'sandbox';
        } else {
            return 'production';
        }
    }

    public function getBaseUri()
    {
        if ($this->getMode() == 'sandbox') {
            return 'https://apis.sandbox.flexshopper.com/v3/';
        }
        return 'https://apis.flexshopper.com/v3/';
    }

    public function getAuthKey()
    {
        return $this->scopeConfig->getValue(self::CONFIG_AUTH_KEY);
    }

    public function getApiKey()
    {
        return $this->scopeConfig->getValue(self::CONFIG_API_KEY);
    }

    public function getMinimumOrderValue()
    {
        return $this->scopeConfig->getValue(self::CONFIG_MIN_ORDER_VALUE);
    }

    public function getBrandAttribute()
    {
        return $this->scopeConfig->getValue(self::CONFIG_BRAND_ATTRIBUTE);
    }

    public function getAutoInvoice()
    {
        return $this->scopeConfig->getValue(self::CONFIG_AUTO_INVOICE);
    }

    /**
     * Get carrier code based on method name, defaults to 'ground'
     * Note to extension developers: use a `before` plugin to return with your carrier mapping
     *
     * Possible mappings are:
     *  - freight
     *  - ground
     *  - store
     *  - 2-day-air
     *  - overnight
     * @param $method
     * @return string
     */
    public function getCarrierCode($method) {
        /*
         * Possible mappings are:
         */
        switch ($method) {
            case 'free':
                return 'ground';
            default:
                return 'ground';
        }
    }
}
