<?php

namespace BestResponseMedia\CheckoutCustomization\Observer;

use Exception;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order;
use Magento\Quote\Model\QuoteRepository;
use Magento\Sales\Model\OrderRepository;
use Psr\Log\LoggerInterface;

class AfterCheckout implements ObserverInterface
{
    /** @var RequestInterface */
    private $request;

    /** @var QuoteRepository */
    private $quoteRepository;

    /** @var LoggerInterface */
    private $logger;

    /** @var OrderRepository */
    private $orderRepository;

    /**
     * @param RequestInterface $request
     * @param QuoteRepository $quoteRepository
     * @param OrderRepository $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestInterface $request,
        QuoteRepository  $quoteRepository,
        OrderRepository  $orderRepository,
        LoggerInterface  $logger
    )
    {
        $this->request = $request;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
    }

    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getData('order');

        try {
            $quote = $this->quoteRepository->get($order->getQuoteId());
            $order->setData('delivery_instructions', $quote->getData('delivery_instructions'));
            $this->orderRepository->save($order);
        } catch (Exception $e) {
            $this->logger->error(
                sprintf(
                    'Error while setting delivery instructions data to the order %s Trace: %s',
                    $e->getMessage(),
                    $e->getTraceAsString()
                )
            );
        }
    }
}
