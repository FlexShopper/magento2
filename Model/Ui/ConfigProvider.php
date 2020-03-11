<?php

namespace FlexShopper\Payments\Model\Ui;

use FlexShopper\Payments\Helper\Data;
use \Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'flexshopper';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var Data
     */
    private Data $helper;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
    }


    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'authKey' => $this->helper->getAuthKey(),
                    'mode' => $this->helper->getMode()
                ]
            ]
        ];
    }
}
