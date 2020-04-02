<?php

namespace Ingenico\Connect\Model\Order;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Notification\NotifierInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Ingenico\Connect\Model\ConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Event\Manager;

class EmailManagerFraud
{
    const FRAUD_EMAIL_EVENT = 'ingenico_fraud';
    
    /** @var ConfigInterface */
    private $ePaymentsConfig;
    
    /** @var EmailProcessor */
    private $emailProcessor;
    
    /** @var UrlInterface */
    private $urlBuilder;
    
    /** @var Manager */
    private $manager;
    
    /** @var NotifierInterface */
    private $notifier;
    
    /**
     * @param ConfigInterface $ePaymentsConfig
     * @param EmailProcessor $emailProcessor
     * @param UrlInterface $urlBuilder
     * @param ManagerInterface $manager
     * @param NotifierInterface $notifier
     */
    public function __construct(
        ConfigInterface $ePaymentsConfig,
        EmailProcessor $emailProcessor,
        UrlInterface $urlBuilder,
        ManagerInterface $manager,
        NotifierInterface $notifier
    ) {
        $this->ePaymentsConfig = $ePaymentsConfig;
        $this->emailProcessor = $emailProcessor;
        $this->urlBuilder = $urlBuilder;
        $this->manager = $manager;
        $this->notifier = $notifier;
    }
    
    /**
     * @param OrderInterface $order
     * @throws LocalizedException
     * @throws MailException
     */
    public function process(OrderInterface $order)
    {
        $this->manager->dispatch(self::FRAUD_EMAIL_EVENT);
        $this->notify($order);
    }
    
    /**
     * @param OrderInterface $order
     * @throws LocalizedException
     * @throws MailException
     */
    private function notify(OrderInterface $order)
    {
        $storeId = $order->getStoreId();
        if ($this->ePaymentsConfig->getFraudManagerEmail($storeId)) {
            $adminOrderUrl = $this->urlBuilder->getUrl("sales/order/view/", ['order_id' => $order->getEntityId()]);
            $emailTemplateVariables = [
                'order'      => $order,
                'order_link' => $adminOrderUrl,
            ];
            
            $this->emailProcessor->processEmail(
                $storeId,
                $this->ePaymentsConfig->getFraudEmailTemplate($storeId),
                $this->ePaymentsConfig->getFraudManagerEmail($storeId),
                $this->ePaymentsConfig->getFraudEmailSender($storeId),
                $emailTemplateVariables
            );
            
            return;
        }
    
        $this->notifier->addMinor(
            __('Order suspected for fraud'),
            __(
                'Order #%1, placed on %2 by %3 (%4) suspected for fraud',
                $order->getIncrementId(),
                $order->getCreatedAt(),
                $order->getCustomerName(),
                $order->getCustomerEmail()
            )
        );
    }
}
