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

use Shopgate\Base\Model\Utility\Profiler\SgProfile;

class SgProfiler
{
    /** @var SgLogger */
    protected $logger;
    /** @var array */
    protected $profiles = [];
    /** @var SgProfile */
    protected $currentProfile = false;

    /**
     * Loads only logger
     *
     * @param SgLogger $logger
     */
    public function __construct(SgLogger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Loads and starts a profile.
     * If nothing is provided, then
     * it starts a generic profile.
     *
     * @param string | null $identifier
     *
     * @return $this
     */
    public function start($identifier = null)
    {
        $this->loadProfile($identifier);
        $this->currentProfile->start();

        return $this;
    }

    /**
     * Retrieves the current profile
     *
     * @param string | null $identifier
     *
     * @return SgProfile
     */
    protected function loadProfile($identifier = null)
    {
        if (!$identifier) {
            $identifier = 'generic';
        }

        if (isset($this->profiles[$identifier])) {
            $this->currentProfile = $this->profiles[$identifier];
        } else {
            $this->currentProfile        = new SgProfile();
            $this->profiles[$identifier] = $this->currentProfile;
        }

        return $this->currentProfile;
    }

    /**
     * Ends timing on current profile
     *
     * @return $this
     */
    public function end()
    {
        $this->currentProfile->end();

        return $this;
    }

    /**
     * Retrieves the difference
     * between start and end time
     * of the profile in seconds
     *
     * @return float
     */
    public function getDuration()
    {
        return $this->currentProfile->getDuration();
    }

    /**
     * Prints a message to debug log
     *
     * @param string $message - message needs to contain "%s" to print timing
     *
     * @return bool
     */
    public function debug($message = "%s seconds")
    {
        $this->logger->debug(sprintf($message, $this->currentProfile->getDuration()));
    }
}
