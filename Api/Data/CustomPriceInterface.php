<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Api\Data;

interface CustomPriceInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const CUSTOMER_EMAIL = 'customer_email';
    const CUSTOMPRICE_ID = 'customprice_id';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';

    /**
     * Get customprice_id
     * @return string|null
     */
    public function getCustompriceId();

    /**
     * Set customprice_id
     * @param string $custompriceId
     * @return \Rukshan\CustomPrice\Api\Data\CustomPriceInterface
     */
    public function setCustompriceId($custompriceId);

    /**
     * Get customer_email
     * @return string|null
     */
    public function getCustomerEmail();

    /**
     * Set customer_email
     * @param string $customerEmail
     * @return \Rukshan\CustomPrice\Api\Data\CustomPriceInterface
     */
    public function setCustomerEmail($customerEmail);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Rukshan\CustomPrice\Api\Data\CustomPriceExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Rukshan\CustomPrice\Api\Data\CustomPriceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Rukshan\CustomPrice\Api\Data\CustomPriceExtensionInterface $extensionAttributes
    );

    /**
     * Get from_date
     * @return string|null
     */
    public function getFromDate();

    /**
     * Set from_date
     * @param string $fromDate
     * @return \Rukshan\CustomPrice\Api\Data\CustomPriceInterface
     */
    public function setFromDate($fromDate);

    /**
     * Get to_date
     * @return string|null
     */
    public function getToDate();

    /**
     * Set to_date
     * @param string $toDate
     * @return \Rukshan\CustomPrice\Api\Data\CustomPriceInterface
     */
    public function setToDate($toDate);
}

