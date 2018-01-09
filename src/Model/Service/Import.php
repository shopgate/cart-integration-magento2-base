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

namespace Shopgate\Base\Model\Service;

use Magento\Framework\Phrase;
use Magento\Framework\Webapi\Exception;
use Shopgate\Base\Api\ImportInterface;
use ShopgateCustomer;

class Import implements ImportInterface
{
    const NOT_ENABLED_ERROR = 'Shopgate_Import module is not enabled or installed';

    /**
     * @inheritdoc
     */
    public function registerCustomer($action, $shopNumber, $user, $pass, $traceId, $userData)
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     */
    public function registerCustomerRaw($user, $pass, ShopgateCustomer $customer)
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     */
    public function addOrder($order)
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }

    /**
     * @inheritdoc
     */
    public function updateOrder($order)
    {
        throw new Exception(new Phrase(self::NOT_ENABLED_ERROR));
    }
}
