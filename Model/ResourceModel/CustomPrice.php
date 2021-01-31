<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Model\ResourceModel;

class CustomPrice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('rukshan_customprice_customprice', 'customprice_id');
    }

    public function getCustomSpecialPrice($customPrice)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('rukshan_customprice_customprice_product'),
            ['product_id', 'custom_special_price']
        )->where(
            "{$this->getTable('rukshan_customprice_customprice_product')}.customprice_id = ?",
            $customPrice->getId()
        );
        $bind = ['customprice_id' => (int)$customPrice->getId()];

        return $this->getConnection()->fetchPairs($select, $bind);
    }
}

