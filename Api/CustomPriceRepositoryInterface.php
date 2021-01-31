<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Api;

use Rukshan\CustomPrice\Api\Data\CustomPriceInterface;

interface CustomPriceRepositoryInterface
{

    /**
     * Save CustomPrice
     * @param CustomPriceInterface $customPrice
     * @return CustomPriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        CustomPriceInterface $customPrice
    );

    /**
     * Retrieve CustomPrice
     * @param string $custompriceId
     * @return CustomPriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($custompriceId);

    /**
     * Retrieve CustomPrice by customer email
     * @param string $email
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerByEmail($email);

    /**
     * Retrieve CustomPrice matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Rukshan\CustomPrice\Api\Data\CustomPriceSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete CustomPrice
     * @param CustomPriceInterface $customPrice
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        CustomPriceInterface $customPrice
    );

    /**
     * Delete CustomPrice by ID
     * @param string $custompriceId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($custompriceId);

    /**
     * Retrieve CustomPrice products
     * @param string $custompriceId
     * @return CustomPriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProducts($custompriceId);

    /**
     * Save CustomPrice Products
     * @param CustomPriceInterface $customPrice
     * @param null|array $oldProducts
     * @param null|array $newProducts
     * @return CustomPriceInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveProducts(
        CustomPriceInterface $customPrice,
        ?array $oldProducts,
        ?array $newProducts
    );
}
