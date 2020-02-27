<?php


namespace FlexShopper\Payments\Model\Payment;

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
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        DirectoryHelper $directory = null
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $paymentData, $scopeConfig, $logger, $resource, $resourceCollection, $data, $directory);
    }


    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
        $items = $quote->getItems();

        foreach($items as $item) {
            if ($item->getProduct()->getData('flexshopper_leasing_enabled') === false) {
                return false;
            }
        }

        return parent::isAvailable($quote);
    }
}
