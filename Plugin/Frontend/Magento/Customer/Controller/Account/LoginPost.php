<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Plugin\Frontend\Magento\Customer\Controller\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Rukshan\CustomPrice\Api\CustomPriceRepositoryInterface;
use Rukshan\CustomPrice\Model\Registry\CustomPriceProductsRegistry;
use Rukshan\CustomPrice\Plugin\Model\Product;

class LoginPost extends DataObject
{
    /**
     * @var Session
     */
    private $customerSession;
    /**
     * @var CustomPriceProductsRegistry
     */
    private $registry;
    /**
     * @var CustomPriceRepositoryInterface
     */
    private $customPriceRepository;
    /**
     * @var Context
     */
    private $context;

    /**
     * LoginPost constructor.
     * @param Session $customerSession
     * @param CustomPriceProductsRegistry $registry
     * @param CustomPriceRepositoryInterface $customPriceRepository
     * @param Context $context
     */
    public function __construct(
        Session $customerSession,
        CustomPriceProductsRegistry $registry,
        CustomPriceRepositoryInterface $customPriceRepository,
        Context $context
    ) {
        parent::__construct();
        $this->customerSession = $customerSession;
        $this->registry = $registry;
        $this->customPriceRepository = $customPriceRepository;
        $this->context = $context;
    }

    /**
     * After execute of LoginPost
     *
     * @param \Magento\Customer\Controller\Account\LoginPost $subject
     * @param Redirect $result
     * @return mixed
     */
    public function afterExecute(
        \Magento\Customer\Controller\Account\LoginPost $subject,
        Redirect $result
    ) {
        if ($this->customerSession->isLoggedIn() && !$this->registry->hasProducts()) {
            try {
                $customerEmail = $this->customerSession->getCustomer()->getEmail();
                $products = $this->customPriceRepository->getCustomerByEmail($customerEmail);
                $this->registry->setProducts($products);
            } catch (LocalizedException $e) {
                // Skip if something goes wrong
            }

        }
        return $result;
    }
}
