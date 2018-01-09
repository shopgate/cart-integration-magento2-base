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
namespace Shopgate\Base\Helper\Settings;

use Magento\Framework\Api\SimpleDataObjectConverter;

class Retriever
{

    /** @var array - allowed getSettings to export passed in DI */
    private $exportParams;
    /** @var Customer\Retriever */
    private $customerRetriever;
    /** @var Country\Retriever */
    private $countryRetriever;
    /** @var Payment\Retriever */
    private $paymentRetriever;
    /** @var Tax\Retriever */
    private $taxRetriever;

    /**
     * @param array              $exportParams
     * @param Customer\Retriever $customerGroupRetriever
     * @param Country\Retriever  $countryRetriever
     * @param Payment\Retriever  $paymentRetriever
     * @param Tax\Retriever      $taxRetriever
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        Customer\Retriever $customerGroupRetriever,
        Country\Retriever $countryRetriever,
        Payment\Retriever $paymentRetriever,
        Tax\Retriever $taxRetriever,
        $exportParams = []
    ) {
        $this->exportParams      = $exportParams;
        $this->customerRetriever = $customerGroupRetriever;
        $this->countryRetriever  = $countryRetriever;
        $this->paymentRetriever  = $paymentRetriever;
        $this->taxRetriever      = $taxRetriever;
    }

    /**
     * Returns an array of certain settings of the shop
     *
     * @see http://developer.shopgate.com/plugin_api/system_information/get_settings
     *
     * @throws \ShopgateLibraryException on invalid log in data or hard errors like database failure.
     */
    public function getSettings()
    {
        return $this->methodLoader($this->getExportParams());
    }

    /**
     * Traverses method array and calls the
     * functions of this class
     *
     * @param array $methods - array(snake_case)
     *
     * @return array
     */
    private function methodLoader($methods)
    {
        foreach ($methods as $key => $param) {
            if (is_array($param)) {
                $methods[$key] = $this->methodLoader($this->getExportParams($key));
                continue;
            }
            $method = 'get' . SimpleDataObjectConverter::snakeCaseToUpperCamelCase($param);

            if (method_exists($this, $method)) {
                $methods[$param] = $this->{$method}();
                unset($methods[$key]);
            }
        }

        return $methods;
    }

    /**
     * Retrieves all parameters if no key is specified
     *
     * @param string|null $key
     *
     * @return string|array
     */
    private function getExportParams($key = null)
    {
        if ($key && isset($this->exportParams[$key])) {
            return $this->exportParams[$key];
        }

        return $this->exportParams;
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    protected function getCustomerGroups()
    {
        return $this->customerRetriever->getCustomerGroups();
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    protected function getAllowedAddressCountries()
    {
        return $this->countryRetriever->getAllowedAddressCountries();
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    protected function getAllowedShippingCountries()
    {
        return $this->countryRetriever->getAllowedShippingCountries();
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    protected function getProductTaxClasses()
    {
        return $this->taxRetriever->getProductTaxClasses();
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    protected function getCustomerTaxClasses()
    {
        return $this->taxRetriever->getCustomerTaxClasses();
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    protected function getTaxRates()
    {
        return $this->taxRetriever->getTaxRates();
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    protected function getTaxRules()
    {
        return $this->taxRetriever->getTaxRules();
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    protected function getPaymentMethods()
    {
        return $this->paymentRetriever->getPaymentMethods();
    }
}
