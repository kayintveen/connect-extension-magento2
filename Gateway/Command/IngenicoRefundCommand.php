<?php

declare(strict_types=1);

namespace Ingenico\Connect\Gateway\Command;

use Ingenico\Connect\Sdk\ResponseException;
use Magento\Payment\Gateway\CommandInterface;
use Ingenico\Connect\Model\Ingenico\Action\Refund\CreateRefund;
use Magento\Sales\Model\Order\Payment;

class IngenicoRefundCommand implements CommandInterface
{
    /**
     * @var CreateRefund
     */
    private $createRefund;

    /**
     * @var ApiErrorHandler
     */
    private $apiErrorHandler;

    /**
     * IngenicoRefundCommand constructor.
     *
     * @param CreateRefund $createRefund
     * @param ApiErrorHandler $apiErrorHandler
     */
    public function __construct(CreateRefund $createRefund, ApiErrorHandler $apiErrorHandler)
    {
        $this->createRefund = $createRefund;
        $this->apiErrorHandler = $apiErrorHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $commandSubject)
    {
        /** @var Payment $payment */
        $payment = $commandSubject['payment']->getPayment();
        $creditmemo = $payment->getCreditmemo();
        try {
            $this->createRefund->process(
                $payment->getOrder(),
                $creditmemo->getBaseGrandTotal()
            );
        } catch (ResponseException $e) {
            $this->apiErrorHandler->handleError($e);
        }
    }
}
