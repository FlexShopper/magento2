<?php


namespace FlexShopper\Payments\Observer;

use FlexShopper\Payments\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class OrderPlaceObserver implements ObserverInterface
{
    private $orderRepository;
    private $invoiceService;
    private $transactionFactory;
    private $messageManager;
    /**
     * @var Order\Email\Sender\InvoiceSender
     */
    private $invoiceSender;
    private $order;
    /**
     * @var Data
     */
    private Data $helper;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Sales\Api\Data\OrderInterface $order,
        Data $helper
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transactionFactory = $transactionFactory;
        $this->messageManager = $messageManager;
        $this->invoiceSender = $invoiceSender;
        $this->order = $order;
        $this->helper = $helper;
    }

    /**
     * @inheritDoc
     */
    public function execute(Observer $observer)
    {
        $orderids = $observer->getEvent()->getOrderIds();

        $createInvoice = $this->helper->getAutoInvoice();

        foreach($orderids as $orderid){
            $order = $this->order->load($orderid);
        }

        if ($order->canInvoice() && $createInvoice && $order->getPayment()->getMethod() === 'flexshopperpayments') {
            $invoice = $this->invoiceService->prepareInvoice($order);
            if (!$invoice) {
                throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t save the invoice right now.'));
            }
            if (!$invoice->getTotalQty()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can\'t create an invoice without products.')
                );
            }
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
            $invoice->register();
            $invoice->getOrder()->setCustomerNoteNotify(false);
            $invoice->getOrder()->setIsInProcess(true);
            $order->addStatusHistoryComment('Automatically INVOICED', false);
            $transactionSave = $this->transactionFactory->create()->addObject($invoice)->addObject($invoice->getOrder());
            $transactionSave->save();

            // send invoice emails, If you want to stop mail disable below try/catch code
            try {
                $this->invoiceSender->send($invoice);
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t send the invoice email right now.'));
            }
        }
    }

}
