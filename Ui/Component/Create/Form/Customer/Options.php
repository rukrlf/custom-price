<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Ui\Component\Create\Form\Customer;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;

class Options implements OptionSourceInterface
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var array
     */
    private $customerTree;

    /**
     * @param CustomerCollectionFactory $customerCollectionFactory
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getCustomerTree();
    }

    /**
     * Retrieve Ccustomer tree
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCustomerTree()
    {
        if ($this->customerTree === null) {
            $collection = $this->customerCollectionFactory->create();
            $collection->addNameToSelect();

            $customerByEmail = [];
            /** @var \Magento\Customer\Model\Customer $customer */
            foreach ($collection as $customer) {
                $customerEmail = $customer->getEmail();
                if (!isset($customerByEmail[$customerEmail])) {
                    $customerByEmail[$customerEmail] = [
                        'value' => $customerEmail
                    ];
                }
                $customerByEmail[$customerEmail]['label'] = $customer->getName();
            }
            $this->customerTree = $customerByEmail;
        }
        return $this->customerTree;
    }
}
