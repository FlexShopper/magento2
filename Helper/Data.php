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
            return 'https://apis.sandbox.flexshopper.com/v3';
        }
        return 'https://apis.flexshopper.com';
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
        return $this->scopeConfig->getValue(self::CONFIG_API_KEY);
    }

    public function getBrandAttribute()
    {
        return $this->scopeConfig->getValue(self::CONFIG_BRAND_ATTRIBUTE);
    }
}
