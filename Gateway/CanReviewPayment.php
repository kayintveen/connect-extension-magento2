<?php

namespace Netresearch\Epayments\Gateway;

use Ingenico\Connect\Sdk\Domain\Payment\Definitions\Payment;
use Netresearch\Epayments\Model\Ingenico\StatusInterface;

class CanReviewPayment extends AbstractValueHandler
{
    /**
     * @param Payment $paymentResponse
     * @return bool
     */
    protected function getResponseValue($paymentResponse)
    {
        return $paymentResponse->status === StatusInterface::PENDING_FRAUD_APPROVAL;
    }
}
