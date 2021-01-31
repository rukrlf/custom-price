<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Plugin\Model;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Rukshan\CustomPrice\Api\CustomPriceRepositoryInterface;
use Rukshan\CustomPrice\Model\Registry\CustomPriceProductsRegistry;

class Product
{
    /**
     * Customer email text
     */
    const CUSTOMER_EMAIL = 'customer_email';
    /**
     * Customer email null text
     */
    const CUSTOMER_EMAIL_NULL = null;
    /**
     * @var CustomPriceProductsRegistry
     */
    private $registry;
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @var ScopeInterface
     */
    private $scope;
    /**
     * @var Context
     */
    private $context;
    /**
     * @var CustomPriceRepositoryInterface
     */
    private $customPriceRepository;

    /**
     * Product constructor.
     * @param Session $customerSession
     * @param CustomPriceProductsRegistry $registry
     * @param TimezoneInterface $timezone
     * @param ScopeInterface $scope
     * @param Context $context
     * @param CustomPriceRepositoryInterface $customPriceRepository
     */
    public function __construct(
        Session $customerSession,
        CustomPriceProductsRegistry $registry,
        TimezoneInterface $timezone,
        ScopeInterface $scope,
        Context $context,
        CustomPriceRepositoryInterface $customPriceRepository
    ) {
        $this->customerSession = $customerSession;
        $this->registry = $registry;
        $this->timezone = $timezone;
        $this->scope = $scope;
        $this->context = $context;
        $this->customPriceRepository = $customPriceRepository;
    }

    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        $this->context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        if ($this->context->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
            if (!$this->registry->hasProducts()) {
                $customerEmail = $this->customerSession->getCustomer()->getEmail();
                $customerEmail = $customerEmail ?? $this->context->getValue(self::CUSTOMER_EMAIL);
                $products = $this->customPriceRepository->getCustomerByEmail($customerEmail);
                $this->registry->setProducts($products);
            }

            if ($this->registry->hasProducts()) {
                $products = $this->registry->getProducts();

                if (in_array($subject->getId(), array_keys($products))) {
                    if (
                    $this->timezone->isScopeDateInInterval(
                        $this->scope->getId(),
                        $products[$subject->getId()]['from_date'],
                        $products[$subject->getId()]['to_date']
                    )
                ) {
                        $result = $products[$subject->getId()]['custom_special_price'] ?? $result;
                    }
                }
            }
        }

        return $result;
    }
}
