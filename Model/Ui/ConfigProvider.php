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
    private $helper;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlInterface;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Data $helper,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        $this->urlInterface = $urlInterface;
    }


    public function getConfig()
    {
        return [
            'payment' => [
                self::CODE => [
                    'authKey' => $this->helper->getAuthKey(),
                    'mode' => $this->helper->getMode(),
                    'validateUrl' => $this->urlInterface->getUrl('flexshopper/validate/index'),
                    'brandAttr' => $this->helper->getBrandAttribute()
                ]
            ]
        ];
    }
}
