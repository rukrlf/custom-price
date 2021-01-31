<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Rukshan\CustomPrice\Api\CustomPriceRepositoryInterface;
use Rukshan\CustomPrice\Api\Data\CustomPriceInterfaceFactory;
use Rukshan\CustomPrice\Api\Data\CustomPriceSearchResultsInterfaceFactory;
use Rukshan\CustomPrice\Model\ResourceModel\CustomPrice as ResourceCustomPrice;
use Rukshan\CustomPrice\Model\ResourceModel\CustomPrice\CollectionFactory as CustomPriceCollectionFactory;

class CustomPriceRepository implements CustomPriceRepositoryInterface
{
    /**
     * Custom price table
     */
    const TBL_CUSTOMPRICE = 'rukshan_customprice_customprice';
    /**
     * Custom price products table
     */
    const TBL_CUSTOMPRICE_PRODUCT = 'rukshan_customprice_customprice_product';
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CustomPriceCollectionFactory
     */
    protected $customPriceCollectionFactory;
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    /**
     * @var CustomPriceInterfaceFactory
     */
    protected $dataCustomPriceFactory;
    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;
    /**
     * @var CustomPriceFactory
     */
    protected $customPriceFactory;
    /**
     * @var CustomPriceSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;
    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;
    /**
     * @var ResourceCustomPrice
     */
    protected $resource;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @param ResourceCustomPrice $resource
     * @param CustomPriceFactory $customPriceFactory
     * @param CustomPriceInterfaceFactory $dataCustomPriceFactory
     * @param CustomPriceCollectionFactory $customPriceCollectionFactory
     * @param CustomPriceSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceCustomPrice $resource,
        CustomPriceFactory $customPriceFactory,
        CustomPriceInterfaceFactory $dataCustomPriceFactory,
        CustomPriceCollectionFactory $customPriceCollectionFactory,
        CustomPriceSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->customPriceFactory = $customPriceFactory;
        $this->customPriceCollectionFactory = $customPriceCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataCustomPriceFactory = $dataCustomPriceFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Rukshan\CustomPrice\Api\Data\CustomPriceInterface $customPrice
    ) {
        /* if (empty($customPrice->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $customPrice->setStoreId($storeId);
        } */

        $customPriceData = $this->extensibleDataObjectConverter->toNestedArray(
            $customPrice,
            [],
            \Rukshan\CustomPrice\Api\Data\CustomPriceInterface::class
        );

        $customPriceModel = $this->customPriceFactory->create()->setData($customPriceData);

        try {
            $this->resource->save($customPriceModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the customPrice: %1',
                $exception->getMessage()
            ));
        }
        return $customPriceModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($customPriceId)
    {
        $customPrice = $this->customPriceFactory->create();
        $this->resource->load($customPrice, $customPriceId);
        if (!$customPrice->getId()) {
            throw new NoSuchEntityException(__('CustomPrice with id "%1" does not exist.', $customPriceId));
        }
        return $customPrice->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerByEmail($email)
    {
        $tbl = $this->resource->getTable(self::TBL_CUSTOMPRICE);
        $productsTbl = $this->resource->getTable(self::TBL_CUSTOMPRICE_PRODUCT);
        $select = $this->resource->getConnection()->select()
            ->from(
                ['cp' => $tbl],
                ['cpp.product_id', 'customer_email', 'from_date', 'to_date', 'cp.customprice_id']
            )->join(
                ['cpp' => $productsTbl],
                "cp.customprice_id = cpp.customprice_id",
                ['product_id', 'custom_special_price']
            )->where('cp.customer_email = ?', $email);

        $products = $this->resource->getConnection()->fetchAssoc($select);

        return $products;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->customPriceCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Rukshan\CustomPrice\Api\Data\CustomPriceInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Rukshan\CustomPrice\Api\Data\CustomPriceInterface $customPrice
    ) {
        try {
            $customPriceModel = $this->customPriceFactory->create();
            $this->resource->load($customPriceModel, $customPrice->getCustompriceId());
            $this->resource->delete($customPriceModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the CustomPrice: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($customPriceId)
    {
        return $this->delete($this->get($customPriceId));
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts($customPriceId)
    {
        $customPrice = $this->customPriceFactory->create();
        $this->resource->load($customPrice, $customPriceId);
        if (!$customPrice->getId()) {
            throw new NoSuchEntityException(__('CustomPrice with id "%1" does not exist.', $customPriceId));
        }
        //return $customPrice->getDataModel();

        $tbl = $this->resource->getTable(self::TBL_CUSTOMPRICE_PRODUCT);
        $select = $this->resource->getConnection()->select()->from(
            $tbl,
            ['product_id']
        )
            ->where(
                'customprice_id = ?',
                (int)$customPriceId
            );
        return $this->resource->getConnection()->fetchCol($select);
    }

    public function saveProducts($customPrice, ?array $oldProducts, ?array $newProducts)
    {
        $table = $this->resource->getTable(self::TBL_CUSTOMPRICE_PRODUCT);
        $delete = array_diff($oldProducts, $newProducts);
        $insert = array_diff($newProducts, $oldProducts);

        if ($delete) {
            $where = ['customprice_id = ?' => (int)$customPrice->getCustompriceId(), 'product_id IN (?)' => $delete];
            $this->resource->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $key => $prie) {
                $data[] = [
                    'customprice_id' => (int)$customPrice->getCustompriceId(),
                    'product_id' => (int)$key,
                    'custom_special_price' => (int)$prie
                ];
            }
            $this->resource->getConnection()->insertMultiple($table, $data);
        }
    }
}
