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

namespace Shopgate\Base\Model\Utility\Profiler;

class SgProfile
{
    /** @var string */
    protected $identifier;
    /** @var int */
    protected $start = 0;
    /** @var int */
    protected $end = 0;

    /**
     * @return $this
     */
    public function start()
    {
        $this->start = microtime(true);

        return $this;
    }

    /**
     * @return $this
     */
    public function end()
    {
        $this->end = microtime(true);

        return $this;
    }

    /**
     * @return float
     */
    public function getDuration()
    {
        return round($this->end - $this->start, 2);
    }
}
