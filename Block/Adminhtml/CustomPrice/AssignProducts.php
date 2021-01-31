<?php
/**
 * Copyright © rukshan, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Rukshan\CustomPrice\Block\Adminhtml\CustomPrice;

use Rukshan\CustomPrice\Api\CustomPriceRepositoryInterface;
use Rukshan\CustomPrice\Model\CustomPrice;
use Rukshan\CustomPrice\Model\CustomPriceFactory;
use Rukshan\CustomPrice\Model\CustomPriceRepository;

class AssignProducts extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'Rukshan_CustomPrice::price/edit/assign_products.phtml';

    /**
     * @var \Rukshan\CustomPrice\Block\Adminhtml\CustomPrice\Tab\Product
     */
    protected $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $jsonEncoder;
    /**
     * @var CustomPriceRepositoryInterface
     */
    private $customPriceRepository;
    /**
     * @var CustomPriceFactory
     */
    private $customPriceFactory;
    /**
     * @var \Rukshan\CustomPrice\Model\ResourceModel\CustomPrice
     */
    private $resourceModel;

    /**
     * AssignProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param CustomPriceRepositoryInterface $customPriceRepository
     * @param CustomPriceFactory $customPriceFactory
     * @param \Rukshan\CustomPrice\Model\ResourceModel\CustomPrice $resourceModel
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        CustomPriceRepositoryInterface $customPriceRepository,
        CustomPriceFactory $customPriceFactory,
        \Rukshan\CustomPrice\Model\ResourceModel\CustomPrice $resourceModel,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
        $this->customPriceRepository = $customPriceRepository;
        $this->customPriceFactory = $customPriceFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Rukshan\CustomPrice\Block\Adminhtml\CustomPrice\Tab\Product::class,
                'custom.price.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getProductsJson()
    {
        $custompriceId = $this->getRequest()->getParam('customprice_id');
        $customPrice = $this->customPriceFactory->create();
        $this->resourceModel->load($customPrice, $custompriceId);
        $products = $this->resourceModel->getCustomSpecialPrice($customPrice);

        if (!empty($products)) {
            return $this->jsonEncoder->encode($products);
        }
        return '{}';
    }
}
