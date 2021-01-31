<?php

declare(strict_types = 1);

namespace Rukshan\CustomPrice\Model\Registry;

class CustomPriceProductsRegistry
{
    /**
     * @var bool|int|array
     */
    private $products = false;

    /**
     * @return bool|int|array
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return bool
     */
    public function hasProducts(): bool
    {
        if ($this->products === false) {
            return false;
        }
        return true;
    }

    /**
     * @param bool|int|array $products
     */
    public function setProducts($products): void
    {
        $this->products = $products;
    }

}
