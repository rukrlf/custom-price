<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Rukshan\CustomPrice\Api\Data\CustomPriceInterface;
use Rukshan\CustomPrice\Api\Data\CustomPriceInterfaceFactory;
use Rukshan\CustomPrice\Model\ResourceModel\CustomPrice\Collection;

class CustomPrice extends AbstractModel
{
    protected $_eventPrefix = 'rukshan_customprice_customprice';
    protected $dataObjectHelper;

    protected $custompriceDataFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CustomPriceInterfaceFactory $custompriceDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\CustomPrice $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CustomPriceInterfaceFactory $custompriceDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\CustomPrice $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        $this->custompriceDataFactory = $custompriceDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve customprice model with customprice data
     * @return CustomPriceInterface
     */
    public function getDataModel()
    {
        $custompriceData = $this->getData();

        $custompriceDataObject = $this->custompriceDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $custompriceDataObject,
            $custompriceData,
            CustomPriceInterface::class
        );

        return $custompriceDataObject;
    }
}
