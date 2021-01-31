<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Model\ResourceModel\CustomPrice;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'customprice_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Rukshan\CustomPrice\Model\CustomPrice::class,
            \Rukshan\CustomPrice\Model\ResourceModel\CustomPrice::class
        );
    }
}

