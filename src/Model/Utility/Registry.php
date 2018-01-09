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

namespace Shopgate\Base\Model\Utility;

class Registry extends \Magento\Framework\Registry
{
    const API      = 'shopgate_api';
    const ACTION   = 'shopgate_action';
    const REDIRECT = 'shopgate_redirect';

    /**
     * Sets current process as an API call,
     * normally made from the main controller
     */
    public function flagApi()
    {
        if (!$this->isApi()) {
            $this->register(self::API, true);
        }
    }

    /**
     * Check if the current process is API
     *
     * @return bool
     */
    public function isApi()
    {
        return $this->registry(self::API) === true;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->register(self::ACTION, $action);
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->registry(self::ACTION);
    }

    /**
     * Checks if current action is equal to passed param
     *
     * @param string $action
     *
     * @return bool
     */
    public function isAction($action)
    {
        return $this->getAction() === $action;
    }

    /**
     * Checks if the current action is in a list of actions
     *
     * @param string[] $list
     *
     * @return bool
     */
    public function isActionInList($list)
    {
        return in_array($this->getAction(), $list);
    }

    /**
     * Checks if this call is a front controller call
     *
     * @return bool
     */
    public function isRedirect()
    {
        return $this->registry(self::REDIRECT) === true;
    }

    /**
     * Sets redirect to true in case the call is made
     * from the frontend controller
     */
    public function flagRedirect()
    {
        if (!$this->isRedirect()) {
            $this->register(self::REDIRECT, true);
        }
    }
}
