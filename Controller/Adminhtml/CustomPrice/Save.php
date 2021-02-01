<?php
/**
 * Copyright © rukshan All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Rukshan\CustomPrice\Controller\Adminhtml\CustomPrice;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\EmailAddress as EmailValidator;
use Rukshan\CustomPrice\Api\CustomPriceRepositoryInterface;
use Rukshan\CustomPrice\Api\Data\CustomPriceInterface;

class Save extends \Magento\Backend\App\Action
{
    protected $dataPersistor;
    /**
     * @var CustomPriceRepositoryInterface
     */
    private $customPriceRepository;
    /**
     * @var CustomPriceInterface
     */
    private $customPrice;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    private $dateFilter;
    /**
     * @var EmailValidator
     */
    private $emailValidator;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param CustomPriceRepositoryInterface $customPriceRepository
     * @param CustomPriceInterface $customPrice
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param EmailValidator $emailValidator
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        CustomPriceRepositoryInterface $customPriceRepository,
        CustomPriceInterface $customPrice,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        EmailValidator $emailValidator
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
        $this->customPriceRepository = $customPriceRepository;
        $this->customPrice = $customPrice;
        $this->dateFilter = $dateFilter;
        $this->emailValidator = $emailValidator;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $error = false;
            $data['customer_email'] = $data['data']['customer_email'] ?? ($data['customer_email'] ?? null);
            $data = $this->filter($data);
            $id = $this->getRequest()->getParam('customprice_id');
            if ($id) {
                try {
                    $this->validateEmailFormat($data['customer_email']);
                    $this->customPrice = $this->customPriceRepository->get($id);
                } catch (LocalizedException $e) {
                    $error = true;
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $error = true;
                    $this->messageManager->addErrorMessage($e, __('Something went wrong.'));
                }
            }

            if (!$error) {
                $this->customPrice->setCustomerEmail($data['customer_email']??null);
                $this->customPrice->setFromDate($data['from_date']??null);
                $this->customPrice->setToDate($data['to_date']??null);

                try {
                    $this->customPrice = $this->customPriceRepository->save($this->customPrice);
                    $this->saveProducts($this->customPrice, $data);

                    $this->messageManager->addSuccessMessage(__('You saved the Customprice.'));
                    $this->dataPersistor->clear('rukshan_customprice_customprice');

                    if ($this->getRequest()->getParam('back')) {
                        return $resultRedirect->setPath(
                            '*/*/edit',
                            ['customprice_id' => $this->customPrice->getCustompriceId()]
                        );
                    }
                    return $resultRedirect->setPath('*/*/');
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addExceptionMessage(
                        $e,
                        __('Something went wrong while saving the Customprice.')
                    );
                }
            }

            $this->dataPersistor->set('rukshan_customprice_customprice', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['customprice_id' => $this->getRequest()->getParam('customprice_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function saveProducts($customPrice, $post)
    {
        if (isset($post['customprice_products'])) {
            $productIds = json_decode($post['customprice_products'], true);
            try {
                $oldProducts = (array) $this->customPriceRepository->getProducts($customPrice->getCustompriceId());
                $newProducts = (array) $productIds;

                $this->customPriceRepository->saveProducts($customPrice, $oldProducts, $newProducts);
            } catch (\Exception | LocalizedException $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the custom prices.'));
            }
        }
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array $data
     * @return array
     */
    private function filter($data)
    {
        $filterRules = [];

        foreach (['from_date', 'to_date'] as $dateField) {
            if (!empty($data[$dateField])) {
                $filterRules[$dateField] = $this->dateFilter;
            }
        }

        return (new \Zend_Filter_Input($filterRules, [], $data))->getUnescaped();
    }

    /**
     * Validate email
     *
     * @param string $email
     * @throws LocalizedException
     */
    private function validateEmailFormat(string $email)
    {
        if (!$this->emailValidator->isValid($email)) {
            throw new LocalizedException(__('Please enter a valid email address.'));
        }
    }
}
