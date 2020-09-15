<?php

namespace FlexShopper\Payments\Controller\Minimumamount;

use Magento\Framework\App\Action\Context;
use Zend\Diactoros\Response\JsonResponse;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \FlexShopper\Payments\Model\Client
     */
    private $client;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Context $context,
        \FlexShopper\Payments\Model\Client $client,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->client = $client;
        $this->jsonFactory = $jsonFactory;
        $this->scopeConfig = $scopeConfig;
    }


    public function execute()
    {
        $this->client->setTimeout('2.0');
        try {
            $minimumAmount = $this->client->getMinimumAmount();
        } catch(\InvalidArgumentException $e) {
            // This happens when the customer is trying to check out from a non-US IP, fail gracefully
            $minimumAmount = null;
        }
        $result = $this->jsonFactory->create();

        if ($minimumAmount) {
            return $result->setData([
                'minimumAmount' => $minimumAmount
            ]);
        } else {
            return $result->setData([
                'minimumAmount' => $this->scopeConfig->getValue('payment/flexshopperpayments/minimum_order_value'),
                'error' => 1
            ]);
        }
    }

}
