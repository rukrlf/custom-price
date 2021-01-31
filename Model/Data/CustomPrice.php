<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Model\Data;

use Rukshan\CustomPrice\Api\Data\CustomPriceInterface;

class CustomPrice extends \Magento\Framework\Api\AbstractExtensibleObject implements CustomPriceInterface
{

    /**
     * Get customprice_id
     * @return string|null
     */
    public function getCustompriceId()
    {
        return $this->_get(self::CUSTOMPRICE_ID);
    }

    /**
     * Set customprice_id
     * @param string $custompriceId
     * @return \Rukshan\CustomPrice\Api\Data\CustomPriceInterface
     */
    public function setCustompriceId($custompriceId)
    {
        return $this->setData(self::CUSTOMPRICE_ID, $custompriceId);
    }

    /**
     * Get customer_email
     * @return string|null
     */
    public function getCustomerEmail()
    {
        return $this->_get(self::CUSTOMER_EMAIL);
    }

    /**
     * Set customer_email
     * @param string $customerEmail
     * @return \Rukshan\CustomPrice\Api\Data\CustomPriceInterface
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Rukshan\CustomPrice\Api\Data\CustomPriceExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Rukshan\CustomPrice\Api\Data\CustomPriceExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Rukshan\CustomPrice\Api\Data\CustomPriceExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get from_date
     * @return string|null
     */
    public function getFromDate()
    {
        return $this->_get(self::FROM_DATE);
    }

    /**
     * Set from_date
     * @param string $fromDate
     * @return \Rukshan\CustomPrice\Api\Data\CustomPriceInterface
     */
    public function setFromDate($fromDate)
    {
        return $this->setData(self::FROM_DATE, $fromDate);
    }

    /**
     * Get to_date
     * @return string|null
     */
    public function getToDate()
    {
        return $this->_get(self::TO_DATE);
    }

    /**
     * Set to_date
     * @param string $toDate
     * @return \Rukshan\CustomPrice\Api\Data\CustomPriceInterface
     */
    public function setToDate($toDate)
    {
        return $this->setData(self::TO_DATE, $toDate);
    }
}

