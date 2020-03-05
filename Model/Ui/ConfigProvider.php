<?php

namespace FlexShopper\Payments\Model\Ui;

use \Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'flexshopper';
    const CONFIG_AUTH_KEY = 'payment/flexshopperpayments/auth_key';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }


    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'authKey' => $this->getAuthKey(),
                    'mode' => 'test' // TODO from config
                ]
            ]
        ];
    }

    private function getAuthKey()
    {
        return $this->scopeConfig->getValue(self::CONFIG_AUTH_KEY);
    }
}
