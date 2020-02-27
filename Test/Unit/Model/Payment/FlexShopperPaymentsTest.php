<?php


namespace FlexShopper\Payments\Test\Unit\Model\Payment;


use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use FlexShopper\Payments\Model\Payment\FlexShopperPayments as Fs;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FlexShopperPaymentsTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @var Fs
     */
    private $fs;

    /**  @var \PHPUnit_Framework_MockObject_MockObject */
    protected $currencyPrice;

    public function setUp()
    {
        $paymentData  = $this->createMock(\Magento\Payment\Helper\Data::class);
        $this->scopeConfig = $this->createMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);

        $context = $this->createPartialMock(\Magento\Framework\Model\Context::class, ['getEventDispatcher']);
        $eventManagerMock = $this->createMock(\Magento\Framework\Event\ManagerInterface::class);
        $context->expects($this->any())->method('getEventDispatcher')->willReturn($eventManagerMock);

        $resource = $this->createMock(\Magento\Framework\Model\ResourceModel\AbstractResource::class);
        $resourceCollection = $this->createMock(\Magento\Framework\Data\Collection\AbstractDb::class);
        $directoryHelper = $this->createMock(\Magento\Directory\Helper\Data::class);

        $registry = $this->createMock(\Magento\Framework\Registry::class);
        $extensionAttributesFactory = $this->createMock(\Magento\Framework\Api\ExtensionAttributesFactory::class);
        $customAttributeFactory = $this->createMock(\Magento\Framework\Api\AttributeValueFactory::class);

        $loggerMock = $this->getMockBuilder(\Magento\Payment\Model\Method\Logger::class)
            ->setConstructorArgs([$this->getMockForAbstractClass(\Psr\Log\LoggerInterface::class)])
            ->getMock();
        $this->fs = new \FlexShopper\Payments\Model\Payment\FlexShopperPayments (
            $context,
            $registry,
            $extensionAttributesFactory,
            $customAttributeFactory,
            $paymentData,
            $this->scopeConfig,
            $loggerMock,
            $resource,
            $resourceCollection,
            [],
            $directoryHelper
        );
    }

    /**
     * Is available for a single flex product in cart
     */
    public function testIsAvailableFlexProductOnly()
    {
        $quote = null;
        $grandTotal = 1000;

        $quote = $this->createMock(\Magento\Quote\Model\Quote::class);

        $product = $this->createMock(\Magento\Catalog\Model\Product::class);
        $product->expects($this->once())
            ->method('getData')
            ->with('flexshopper_leasing_enabled')
            ->will($this->returnValue(true));

        $item = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $item->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($product));

        $items = [$item];

        $quote->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue($items));

        $quote->expects($this->any())
            ->method('__call')
            ->with($this->equalTo('getGrandTotal'))
            ->will($this->returnValue($grandTotal));

        $this->scopeConfig->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue(true));

        $this->assertEquals(true, $this->fs->isAvailable($quote));
    }

    /**
     * Is available for a single flex product in cart
     */
    public function testIsAvailableFlexProductAndNonFlexProduct()
    {
        $quote = null;
        $grandTotal = 1000;

        $quote = $this->createMock(\Magento\Quote\Model\Quote::class);

        $products = [];
        $items = [];

        $products[0] = $this->createMock(\Magento\Catalog\Model\Product::class);
        $products[0]->expects($this->once())
            ->method('getData')
            ->with('flexshopper_leasing_enabled')
            ->will($this->returnValue(true));

        $items[0] = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $items[0]->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($products[0]));

        $products[1] = $this->createMock(\Magento\Catalog\Model\Product::class);
        $products[1]->expects($this->once())
            ->method('getData')
            ->with('flexshopper_leasing_enabled')
            ->will($this->returnValue(false));

        $items[1] = $this->createMock(\Magento\Quote\Model\Quote\Item::class);
        $items[1]->expects($this->once())
            ->method('getProduct')
            ->will($this->returnValue($products[1]));

        $quote->expects($this->any())
            ->method('getItems')
            ->will($this->returnValue($items));

        $quote->expects($this->any())
            ->method('__call')
            ->with($this->equalTo('getGrandTotal'))
            ->will($this->returnValue($grandTotal));

        $this->scopeConfig->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue(true));

        $this->assertEquals(false, $this->fs->isAvailable($quote));
    }

}
