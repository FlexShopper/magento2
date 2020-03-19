<?php


namespace FlexShopper\Payments\Model;

use FlexShopper\Payments\Helper\Data;
use GuzzleHttp\Client as GuzzleClient;

class Client
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;

    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->jsonFactory = $jsonFactory;
        $this->json = $json;
        $this->helper = $helper;
    }

    private function call($uri, $method = 'GET', $jsonBody = null) {
        try {
            $flexShopperClient = new GuzzleClient([
                'base_uri' => $this->helper->getBaseUri(),
                'timeout'  => 2.0,
                'headers' => [
                    'Authorization' => $this->helper->getApiKey()
                ]
            ]);
            $response = $flexShopperClient->request($method, '/v3/' . $uri);
            return $response->getBody();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getMinimumAmount()
    {
        $response = $this->call('settings/lease');
        if (!$response) {
            return $response;
        }

        $data = $this->json->unserialize($response);
        return $data['data']['minimumOrderValue'];
    }

    public function getTransaction($flexshopperId) {
        return $this->call("/transactions/${$flexshopperId}");
    }

    public function finalizeTransaction($flexshopperId) {
        return $this->call("/transactions/${$flexshopperId}/finalize", 'POST');
    }

    public function confirmShipment($flexshopperId) {
        return $this->call("/transactions/${$flexshopperId}/confirm-shipment");
    }

    public function cancelOrder($flexshopperId) {
        return $this->call("/transactions/${$flexshopperId}/confirm-shipment", 'POST');
    }

}
