<?php

namespace FlexShopper\Payments\Controller\Cancel;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Zend\Diactoros\Response\JsonResponse;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \FlexShopper\Payments\Model\Client
     */
    private $client;
    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $json;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $cartSession;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */

    public function __construct(
        Context $context,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \FlexShopper\Payments\Model\Client $client,
        \Magento\Checkout\Model\Session $cartSession
    ) {
        parent::__construct($context);
        $this->client = $client;
        $this->json = $json;
        $this->cartSession = $cartSession;
    }


    public function execute()
    {
        $transactionId = $this->cartSession->getQuote()->getFlexshopperTxid();
        if ($transactionId) {
            $this->client->cancelOrder($transactionId);
        }
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
