<?php


namespace FlexShopper\Payments\Model;

use FlexShopper\Payments\Helper\Data;
use Zend\Json\Json;

class Client
{
    const EMPTY_ITEMS = '{"items": []}';
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

    /**
     * @var CurlClient 
     */
    private $curl;

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
        CurlClient $curl,
        \Magento\Payment\Model\Method\Logger $logger,
        Data $helper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->jsonFactory = $jsonFactory;
        $this->json = $json;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->curl = $curl;
    }

    private function call($uri, $method = 'GET', $jsonBody = null) {

        $log = [
            'uri' => $uri,
            'method' => $method,
            'jsonBody' => $jsonBody
        ];
        try {


            $this->curl->setTimeout($this->timeout);
            $this->curl->setHeaders(['Authorization' => $this->helper->getApiKey(), 'Content-Type' => 'application/json']);

            if($method == 'GET') {
                $this->curl->get( $this->helper->getBaseUri().$uri);
            }
            else {
                $this->curl->post($this->helper->getBaseUri().$uri, $jsonBody);
            }


            $log['response'] = $this->curl->getBody();
            $this->logger->debug($log);
            return $this->curl->getBody();
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            $log['errorMessage'] = $this->errorMessage;
            $this->logger->debug($log);
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
        $jsonBody = self::EMPTY_ITEMS;;
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
        $jsonBody = self::EMPTY_ITEMS;;
        if (is_array($items)) {
            /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
            foreach ($items as $item) {
                /** @var \Magento\Rma\Api\Data\ItemInterface $item */
                $jsonItems[] =
                    [
                        'sku' => $item->getProductSku(),
                        'quantity' => $item->getQtyReturned(),
                    ];

            }

            $jsonBody = $this->json->serialize(['items' => $jsonItems]);
        }
        return $this->call("/transactions/${flexshopperId}/return-items", 'POST', $jsonBody);
    }

    public function cancelOrder($flexshopperId) {
        $jsonBody = self::EMPTY_ITEMS;
        return $this->call("/transactions/${flexshopperId}/cancel", 'POST', $jsonBody);
    }

}
