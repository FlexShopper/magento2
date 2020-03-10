<?php

namespace FlexShopper\Payments\Controller\Validate;

use GuzzleHttp\Client;
use Magento\Framework\App\Action\Context;
use Zend\Diactoros\Response\JsonResponse;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;

    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\Serializer\Json $json
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
    }

    public function execute()
    {
        try {

            $flexShopperClient = new Client([
                'base_uri' => 'https://apis.sandbox.flexshopper.com/v3',
                'headers' => [
                    'Authorization' => $this->getApiKey()
                ]
            ]);

            $body = $this->json->unserialize($this->getRequest()->getContent());

            $transactionId = $body['transactionId'];
            $transaction = $this->json->unserialize(
                $flexShopperClient->get("/transactions/${transactionId}")->getBody()
            );
            $orderStatus = $this->checkOrder($transaction);

            if (!$orderStatus) {
                return new JsonResponse(['valid' => false, 'errors' => $orderStatus->errors], 400);
            }

            $flexShopperClient->post("/transactions/${transactionId}/finalize");


            return $this->jsonFactory->create()
                ->setData([
                    'valid' => true,
                    'parsed_body' => $transactionId
                ]);
        } catch (\Exception $e) {
            $result = $this->jsonFactory->create();
            return $result->setData([
                'valid' => false,
                'errors' => $e->getMessage(),
            ]);
        }
    }

    private function checkOrder($transaction)
    {
        return true; // TODO check if quote_id exists.
    }

    private function getApiKey() {
        return $this->scopeConfig->getValue('payment/flexshopperpayments/api_key');
    }

}
