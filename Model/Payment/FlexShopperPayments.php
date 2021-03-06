<?php


namespace FlexShopper\Payments\Model\Payment;

use FlexShopper\Payments\Helper\Data;
use FlexShopper\Payments\Model\Client;
use Magento\CatalogInventory\Model\Configuration;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;

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
     * @var Client
     */
    private $client;



    /**
     * @var StockItemRepository
     */
    private $stockItemRepository;

    /**
     * @var Configuration
     */
    private $stockConfiguration;

    /**
     * FlexShopperPayments constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param Configuration $stockConfiguration
     * @param \Magento\Payment\Model\Method\Logger $logger
     * @param \Magento\Checkout\Model\Session $session
     * @param StockItemRepository $stockItemRepository
     * @param Data $helper
     * @param Client $client
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param DirectoryHelper|null $directory
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\CatalogInventory\Model\Configuration $stockConfiguration,
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Checkout\Model\Session $session,
        StockItemRepository $stockItemRepository,
        Data $helper,
        Client $client,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null
    ){
        //intentional blank lines in calling the constructor to prevent the ConstructorIntegrity check which is not aware of the conditional
        if (interface_exists("Magento\Framework\App\CsrfAwareActionInterface")) {
            parent::
            __construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $resource, $resourceCollection, $data, $directory);
        }
        else {
            parent::
            __construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $resource, $resourceCollection, $data);
        }

        $this->session = $session;
        $this->helper = $helper;
        $this->client = $client;
        $this->stockItemRepository = $stockItemRepository;
        $this->stockConfiguration = $stockConfiguration;
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
            if ($this->quoteItemHasBackorder($item)) {
                return false;
            }
        }

        try {
            $minimumAmount = $this->client->getMinimumAmount();
        } catch(\InvalidArgumentException $e) {
            // This happens when the customer is trying to check out from a non-US IP, fail gracefully
            // Flexshopper should not be available in this case
            $this->logger->debug(['Invalid response from payment gateway. GeoIP in effect?']);
            return false;
        }

        if ($quote->getGrandTotal() < $minimumAmount) {
            return false;
        }

        return parent::isAvailable($quote);
    }

    protected function quoteItemHasBackorder($quoteItem) {
        if ($quoteItem->getProductType() === 'bundle') {
            $options = $quoteItem->getOptions();
            foreach ($options as $option) {
                /** @var \Magento\Quote\Model\Quote\Item\Option $option */
                $typeId = $option->getProduct()->getTypeId();
                if ($typeId !== 'bundle') {
                    $stockItemInformation = $this->stockItemRepository->get($option->getProduct()->getId());
                    if($stockItemInformation) {

                        if($this->hasInfinteStock($stockItemInformation)) {
                            return false;
                        }
                        $backOrderQty = $option->getItem()->getQty() - $stockItemInformation->getQty();
                        if ($backOrderQty > 0) {
                            return true;
                        }
                    }

                }
            }

            return false;
        }

        if ($quoteItem->getProductType() === 'configurable') {
            //configurables do not have their own stock, so they cannot be a backorder
            return false;
        }

        try {
            $stockItemInformation = $this->stockItemRepository->get($quoteItem->getProductId());
            if($stockItemInformation) {
                if($this->hasInfinteStock($stockItemInformation)) {
                    return false;
                }
                $backOrderQty = $quoteItem->getQty() - $stockItemInformation->getQty();
                if ($backOrderQty > 0) {
                    return true;
                }
                else {
                    return false;
                }
            }
            else {
                return false;
            }    
        }
        catch(NoSuchEntityException $ex) {
            return false;
        }

    }

    /**
     * @param $stockItem
     * @return int
     */
    private function hasInfinteStock($stockItem) {
        if(!$stockItem->getUseConfigManageStock()) {
            return !$stockItem->getManageStock();
        }
        else {
            return !$this->stockConfiguration->getManageStock();
        }
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
        $apiMinValue = $this->client->getMinimumAmount();
        return $apiMinValue? $apiMinValue : $this->helper->getMinimumOrderValue();
    }
}

