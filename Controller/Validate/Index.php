<?php

namespace FlexShopper\Payments\Controller\Validate;

use GuzzleHttp\Client;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Zend\Diactoros\Response\JsonResponse;

class Index extends \Magento\Framework\App\Action\Action implements \Magento\Framework\App\CsrfAwareActionInterface
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
    /**
     * @var Session
     */
    private $checkoutSession;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;
    /**
     * @var \FlexShopper\Payments\Model\Client
     */
    private $client;

    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\Serializer\Json $json,
        Session $checkoutSession,
        \FlexShopper\Payments\Model\Client $client,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
        $this->checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->client = $client;
    }

    public function execute()
    {
        try {

            $body = $this->json->unserialize($this->getRequest()->getContent());

            $leaseNumber = $body['leaseNumber'];
            $transactionId = $body['transactionId'];
            $transaction = $this->json->unserialize(
                $this->client->getTransaction($transactionId)
            );

            $this->logger->debug('Flexshopper lease number:' . $leaseNumber);
            $this->logger->debug('Flexshopper transaction id:' . $transactionId);

            $orderStatus = $this->checkOrder($transaction);

            $quote = $this->checkoutSession->getQuote();
            $quote->setFlexshopperId($leaseNumber);
            $quote->save();

            if (!$orderStatus) {
                $this->logger->debug('Invalid order');
                return $this->jsonFactory->create()
                    ->setData([
                        'valid' => false,
                        'errors' => 'Invalid order'
                    ]);
            }

            $this->client->finalizeTransaction($transactionId);

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
        $this->logger->debug($this->json->serialize($transaction));
        try {
            $this->checkoutSession->getQuote();
        } catch (NoSuchEntityException $e) {
            return false;
        } catch (LocalizedException $e) {
            return false;
        }

        if ($transaction['data']['lease']['status'] == 'signed') {
            return true;
        }

        return false; // If the user's session have a quote, this is always valid.
    }

    /**
     * @param RequestInterface $request
     *
     * @return bool|null
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @param RequestInterface $request
     *
     * @return InvalidRequestException|null
     */
    public function createCsrfValidationException(RequestInterface $request): ?\Magento\Framework\App\Request\InvalidRequestException
    {
        return null;
    }
}
