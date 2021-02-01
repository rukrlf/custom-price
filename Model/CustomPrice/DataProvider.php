<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Model\CustomPrice;

use Magento\Framework\App\Request\DataPersistorInterface;
use Rukshan\CustomPrice\Model\ResourceModel\CustomPrice\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $dataPersistor;

    protected $collection;

    protected $loadedData;

    /**
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $model) {
            $this->loadedData[$model->getId()] = $model->getData();
            $this->loadedData[$model->getId()]['data']['customer_email'] = $model->getData('customer_email');
        }
        $data = $this->dataPersistor->get('rukshan_customprice_customprice');

        if (!empty($data)) {
            $model = $this->collection->getNewEmptyItem();
            $model->setData($data);
            $this->loadedData[$model->getId()] = $model->getData();
            $this->dataPersistor->clear('rukshan_customprice_customprice');
        }

        return $this->loadedData;
    }
}
