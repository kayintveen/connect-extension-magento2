<?php

declare(strict_types=1);

namespace Worldline\Connect\Model\Order;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;

// phpcs:ignore SlevomatCodingStandard.Classes.SuperfluousInterfaceNaming.SuperfluousSuffix
interface OrderServiceInterface
{
    /**
     * @param string $incrementId
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getByIncrementId(string $incrementId): OrderInterface;

    /**
     * @param string $hostedCheckoutId
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getByHostedCheckoutId(string $hostedCheckoutId): OrderInterface;
}
