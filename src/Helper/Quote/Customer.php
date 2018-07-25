<?php

/**
 * Copyright Shopgate Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @author    Shopgate Inc, 804 Congress Ave, Austin, Texas 78701 <interfaces@shopgate.com>
 * @copyright Shopgate Inc
 * @license   http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 */

namespace Shopgate\Base\Helper\Quote;

use Magento\Config\Model\Config\Backend\Admin\Custom;
use Magento\Customer\Model\Session;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote as MageQuote;
use Shopgate\Base\Helper\Customer as CustomerHelper;
use Shopgate\Base\Helper\Shopgate\Customer as SgCustomerHelper;
use Shopgate\Base\Model\Service\Config\Core as SgCoreConfig;
use Shopgate\Base\Model\Shopgate\Extended\Base;
use Shopgate\Base\Model\Utility\SgLoggerInterface;

/**
 * Class that helps set the customer object to quote and session
 */
class Customer
{
    /** @var Base */
    private $sgBase;
    /** @var CustomerHelper */
    private $customerHelper;
    /** @var Session */
    private $session;
    /** @var SgCustomerHelper */
    private $sgCustomer;
    /** @var SgLoggerInterface */
    private $log;
    /** @var SgCoreConfig */
    private $sgCoreConfig;

    /**
     * @param Base              $sgBase
     * @param CustomerHelper    $customerHelper
     * @param MageQuote         $quote
     * @param SgCustomerHelper  $sgCustomerHelper
     * @param Session           $session
     * @param SgLoggerInterface $log
     * @param SgCoreConfig      $sgCoreConfig
     */
    public function __construct(
        Base $sgBase,
        CustomerHelper $customerHelper,
        MageQuote $quote,
        Session $session,
        SgCustomerHelper $sgCustomerHelper,
        SgLoggerInterface $log,
        SgCoreConfig $sgCoreConfig
    ) {
        $this->sgBase         = $sgBase;
        $this->customerHelper = $customerHelper;
        $this->session        = $session;
        $this->sgCustomer     = $sgCustomerHelper;
        $this->log            = $log;
        $this->sgCoreConfig   = $sgCoreConfig;
    }

    /**
     * Just sets the customer to quote
     *
     * @param MageQuote $quote
     *
     * @throws \ShopgateLibraryException
     */
    public function setEntity(MageQuote $quote)
    {
        $id    = $this->sgBase->getExternalCustomerId();
        $email = $this->sgBase->getMail();

        try {
            $customer = $id ? $this->customerHelper->getById($id) : $this->customerHelper->getByEmail($email);
        } catch (NoSuchEntityException $e) {
            $customer = new DataObject();
            $this->log->debug('Could not load customer by id or mail.');
        }
        $quote->setCustomerEmail($email)
            ->setRemoteIp($this->sgBase->getCustomerIp());
        if ($this->sgBase->isGuest()) {
            $quote->setCustomerIsGuest(true);
        } elseif (!$this->sgBase->isGuest() && $customer->getId()) {
            $this->session->setCustomerId($customer->getId())->setCustomerGroupId($customer->getGroupId());

            $quote->setCustomer($customer)
                ->setCustomerIsGuest(false);
        } else {
            throw new \ShopgateLibraryException(
                \ShopgateLibraryException::UNKNOWN_ERROR_CODE,
                __('Customer with external id "%1" or email "%2" does not exist', $id, $email)->render()
            );
        }
    }

    /**
     * Sets Billing and Shipping addresses to quote
     *
     * @param MageQuote $quote
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setAddress(MageQuote $quote)
    {
        $billing = $this->sgBase->getInvoiceAddress();
        if (!empty($billing)) {
            $data = $this->sgCustomer->createAddressData($this->sgBase, $billing, $quote->getCustomerId());
            $quote->getBillingAddress()
                  ->addData($data)
                  ->setCustomerAddressId($billing->getId())
                  ->setCustomerId($this->sgBase->getExternalCustomerId())
                  ->setData('should_ignore_validation', true);
        }

        $shipping = $this->sgBase->getDeliveryAddress();
        if (!empty($shipping)) {
            $data = $this->sgCustomer->createAddressData($this->sgBase, $shipping, $quote->getCustomerId());
            $quote->getShippingAddress()
                  ->addData($data)
                  ->setCustomerAddressId($shipping->getId())
                  ->setCustomerId($this->sgBase->getExternalCustomerId())
                  ->setData('should_ignore_validation', true);
        }

        if (!$quote->getShippingAddress()->getCountryId()) {
            $defaultCountryItem = $this->sgCoreConfig->getConfigByPath(Custom::XML_PATH_GENERAL_COUNTRY_DEFAULT);
            $quote->getShippingAddress()->setCountryId($defaultCountryItem->getValue());
        }
    }

    /**
     * Helps reset the billing & shipping objects for guests
     *
     * @see https://shopgate.atlassian.net/browse/MAGENTO-429
     *
     * @param MageQuote $quote
     */
    public function resetGuest(MageQuote $quote)
    {
        if ($this->sgBase->isGuest()) {
            $quote->getBillingAddress()->isObjectNew(false);
            $quote->getShippingAddress()->isObjectNew(false);
        }
    }
}
