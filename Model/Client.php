<?php


namespace FlexShopper\Payments\Model;

use FlexShopper\Payments\Helper\Data;
use GuzzleHttp\Client as GuzzleClient;
use Zend\Json\Json;

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

    private $timeout = "10.0";

    public $errorMessage = "";
    /**
     * @var \Magento\Payment\Model\Method\Logger
     */
    private $logger;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Magento\Payment\Model\Method\Logger $logger,
        Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->jsonFactory = $jsonFactory;
        $this->json = $json;
        $this->helper = $helper;
        $this->logger = $logger;
    }

    private function call($uri, $method = 'GET', $jsonBody = null) {
        try {
            $flexShopperClient = new GuzzleClient([
                'base_uri' => $this->helper->getBaseUri(),
                'timeout'  => $this->timeout,
                'headers' => [
                    'Authorization' => $this->helper->getApiKey()
                ]
            ]);
            $response = $flexShopperClient->request($method, '/v3' . $uri);
            $this->logger->debug($response->getBody());
            return $response->getBody();
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $this->logger->debug($this->errorMessage);
            return false;
        }
    }

    public function setTimeout($timeout) {
        $this->timeout = $timeout;
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
        return $this->call("/transactions/${flexshopperId}");
    }

    public function finalizeTransaction($flexshopperId) {
        return $this->call("/transactions/${flexshopperId}/finalize", 'POST');
    }

    public function confirmShipment($flexshopperId, $carrier = 'ground', $items = false) {
        $jsonItems = [];
        $jsonBody = '';
        if (is_array($items)) {
            /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
            foreach ($items as $item) {
                $jsonItems[] =
                    [
                        'sku' => $item->getSku(),
                        'carrier' => $carrier,
                        'quantity' => $item->getQty(),
                        'shipDate' =>  date('Y-m-d'),
                    ];

            }

            $jsonBody = $this->json->serialize(['items' => $jsonItems]);
        }
        return $this->call("/transactions/${flexshopperId}/confirm-shipment", 'POST', $jsonBody);
    }

    public function rma($flexshopperId, $items = false) {
        $jsonItems = [];
        $jsonBody = '';
        if (is_array($items)) {
            /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
            foreach ($items as $item) {
                /** @var \Magento\Rma\Api\Data\ItemInterface $item */
                $jsonItems[] =
                    [
                        'sku' => $item->getSku(),
                        'quantity' => $item->getQty(),
                    ];

            }

            $jsonBody = $this->json->serialize(['items' => $jsonItems]);
        }
        return $this->call("/transactions/${flexshopperId}/return-items", 'POST', $jsonBody);
    }

    public function cancelOrder($flexshopperId) {
        return $this->call("/transactions/${flexshopperId}/cancel", 'POST');
    }

}
