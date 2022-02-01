<?php

namespace BestResponseMedia\CheckoutCustomization\Plugin\Checkout;

use Exception;
use Magento\Checkout\Model\ShippingInformationManagement as BaseShippingInformationManagement;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Model\QuoteRepository;
use Psr\Log\LoggerInterface;

class ShippingInformationManagement
{
    /** @var QuoteRepository */
    protected $quoteRepository;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param QuoteRepository $quoteRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        LoggerInterface $logger
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    /**
     * @param BaseShippingInformationManagement $subject
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     * @return void
     */
    public function beforeSaveAddressInformation(
        BaseShippingInformationManagement $subject,
        int                               $cartId,
        ShippingInformationInterface      $addressInformation
    )
    {
        try {
            $extensionAttribute = $addressInformation->getShippingAddress()->getExtensionAttributes();
            $deliveryInstructions = $extensionAttribute->getDeliveryInstructions();
            $quote = $this->quoteRepository->getActive($cartId);
            $quote->setData('delivery_instructions', $deliveryInstructions);
        } catch (Exception $e) {
            $this->logger->error(
                sprintf(
                    'Error while retrieving last active quote, %s Trace: %s',
                    $e->getMessage(),
                    $e->getTraceAsString()
                )
            );
        }
    }
}
