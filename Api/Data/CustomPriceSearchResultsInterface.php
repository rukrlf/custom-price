<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Api\Data;

interface CustomPriceSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get CustomPrice list.
     * @return \Rukshan\CustomPrice\Api\Data\CustomPriceInterface[]
     */
    public function getItems();

    /**
     * Set customer_email list.
     * @param \Rukshan\CustomPrice\Api\Data\CustomPriceInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

