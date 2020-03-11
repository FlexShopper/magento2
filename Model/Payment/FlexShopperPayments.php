<?php


namespace FlexShopper\Payments\Model\Payment;

use FlexShopper\Payments\Helper\Data;
use Magento\Directory\Helper\Data as DirectoryHelper;

/**
 * Class FlexShopperPayments
 *
 * @package FlexShopper\Payments\Model\Payment
 */
class FlexShopperPayments extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = "flexshopperpayments";
    protected $_isOffline = true;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $session;

    /**
     * @var Data
     */
    private $helper;

    /**
     * FlexShopperPayments constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param DirectoryHelper|null $directory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Checkout\Model\Session $session,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        Data $helper,
        array $data = [],
        DirectoryHelper $directory = null
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $resource, $resourceCollection, $data, $directory);
        $this->session = $session;
        $this->helper = $helper;
    }


    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        $sessionQuote = $this->session->getQuote();
        if(!$sessionQuote) {
            $sessionQuote = $quote;
        }
        $items = $sessionQuote->getAllItems();

        if (!$this->apiCredentialsExist()) {
            return false;
        }

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach($items as $item) {
            if ($item->getProduct()->getData('flexshopper_leasing_enabled') == false) { // this = to '0' or '1', or null
                return false;
            }
            if ($item->getQtyBackordered() > 0) {
                return false;
            }
        }

        if ($quote->getGrandTotal() < $this->getMinimumOrdervalue()) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    public function apiCredentialsExist() {
        if ($this->helper->getAuthkey() == '' ||
            $this->helper->getApiKey() == ''
        ) {
            return false;
        }

        return true;
    }

    protected function getMinimumOrdervalue()
    {
        return $this->helper->getMinimumOrderValue();
    }
}

