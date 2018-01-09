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

namespace Shopgate\Base\Helper\Settings\Payment;

use Magento\Payment\Model\MethodList;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\StoreManagerInterface;

class Retriever
{

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CartInterface
     */
    private $quote;
    /**
     * @var MethodList
     */
    private $paymentModel;

    /**
     * @param StoreManagerInterface $storeManager - store id
     * @param CartInterface         $quote        - fake quote object
     * @param MethodList            $paymentModel - helps load all payment instances
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CartInterface $quote,
        MethodList $paymentModel
    ) {
        $this->storeManager = $storeManager;
        $this->quote        = $quote;
        $this->paymentModel = $paymentModel;
    }

    /**
     * Retrieves all available payment methods and formats
     * them to Shopgate Merchant API ready format
     *
     * @return array() - e.g. ['id' => 'checkmo', 'title' => 'Money Order', 'is_active' => 1]
     */
    public function getPaymentMethods()
    {
        $this->quote->setStoreId($this->storeManager->getStore()->getId());

        $export         = [];
        $paymentMethods = $this->paymentModel->getAvailableMethods($this->quote);

        foreach ($paymentMethods as $method) {
            $export[] = [
                'id'        => $method->getCode(),
                'title'     => $method->getTitle(),
                'is_active' => 1
            ];
        }

        return $export;
    }
}
