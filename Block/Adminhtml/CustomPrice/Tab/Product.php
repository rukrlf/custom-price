<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rukshan\CustomPrice\Block\Adminhtml\CustomPrice\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Registry;
use Rukshan\CustomPrice\Api\CustomPriceRepositoryInterface;
use Rukshan\CustomPrice\Model\CustomPriceFactory;
use Rukshan\CustomPrice\Model\ResourceModel\CustomPrice;

class Product extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var ProductFactory
     */
    protected $_productFactory;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var Visibility
     */
    private $visibility;
    /**
     * @var CustomPriceRepositoryInterface
     */
    private $customPriceRepository;
    /**
     * @var CustomPriceFactory
     */
    private $customPriceFactory;
    /**
     * @var CustomPrice
     */
    private $resourceModel;

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param ProductFactory $productFactory
     * @param Registry $coreRegistry
     * @param CustomPriceRepositoryInterface $customPriceRepository
     * @param CustomPriceFactory $customPriceFactory
     * @param CustomPrice $resourceModel
     * @param array $data
     * @param Visibility|null $visibility
     * @param Status|null $status
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        ProductFactory $productFactory,
        Registry $coreRegistry,
        CustomPriceRepositoryInterface $customPriceRepository,
        CustomPriceFactory $customPriceFactory,
        CustomPrice $resourceModel,
        array $data = [],
        Visibility $visibility = null,
        Status $status = null
    ) {
        $this->_productFactory = $productFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->visibility = $visibility ?: ObjectManager::getInstance()->get(Visibility::class);
        $this->status = $status ?: ObjectManager::getInstance()->get(Status::class);
        $this->customPriceRepository = $customPriceRepository;
        parent::__construct($context, $backendHelper, $data);
        $this->customPriceFactory = $customPriceFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('customprice_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return \Rukshan\CustomPrice\Model\CustomPrice
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomPrice()
    {
        $custompriceId = $this->getRequest()->getParam('customprice_id', 0);
        $customerPrice = $this->customPriceFactory->create();
        $this->resourceModel->load($customerPrice, $custompriceId);

        return $customerPrice;
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_category') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return Grid
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareCollection()
    {
        if ($this->getCustomPrice()->getId()) {
            $this->setDefaultFilter(['in_category' => 1]);
        }
        $collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'visibility'
        )->addAttributeToSelect(
            'status'
        )->addAttributeToSelect(
            'price'
        )->joinField(
            'custom_special_price',
            'rukshan_customprice_customprice_product',
            'custom_special_price',
            'product_id=entity_id',
            'customprice_id=' . (int)$this->getRequest()->getParam('customprice_id', 0),
            'left'
        );
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId > 0) {
            $collection->addStoreFilter($storeId);
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_category',
            [
                    'type' => 'checkbox',
                    'name' => 'in_category',
                    'values' => $this->_getSelectedProducts(),
                    'index' => 'entity_id',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select col-massaction'
                ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
        $this->addColumn('sku', ['header' => __('SKU'), 'index' => 'sku']);
        $this->addColumn(
            'visibility',
            [
                'header' => __('Visibility'),
                'index' => 'visibility',
                'type' => 'options',
                'options' => $this->visibility->getOptionArray(),
                'header_css_class' => 'col-visibility',
                'column_css_class' => 'col-visibility'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->status->getOptionArray()
            ]
        );

        $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'currency_code' => (string)$this->_scopeConfig->getValue(
                    \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                'index' => 'price'
            ]
        );
        $this->addColumn(
            'custom_special_price',
            [
                'header' => __('Custom Special Price'),
                'type' => 'number',
                'index' => 'custom_special_price',
                'editable' => true
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('rukshan_customprice/*/grid', ['_current' => true]);
    }

    /**
     * @return array
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');
        if ($products === null) {
            $custompriceId = $this->getRequest()->getParam('customprice_id');
            $customPrice = $this->customPriceFactory->create();
            $this->resourceModel->load($customPrice, $custompriceId);
            $products = $this->resourceModel->getCustomSpecialPrice($customPrice);

            return array_keys($products);
        }
        return $products;
    }
}
