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

namespace Shopgate\Base\Tests\Integration\Db;

class WebsiteManager
{

    /**
     * @var Data\Website[]
     */
    protected $websites = [];

    /**
     * The magic unicorns create a store for you
     *
     * @return Data\Website
     */
    public function createSite()
    {
        $site = new Data\Website();
        $site->createWebsite();
        $site->createGroup();
        $site->createStore();
        $this->addWebsite($site);

        return $site;
    }

    /**
     * Adds the website to the stack for removal
     * purposes later
     *
     * @param Data\Website $website
     */
    private function addWebsite(Data\Website $website)
    {
        $this->websites[] = $website;
    }

    /**
     * Creates a new store from the website object
     *
     * @param Data\Website $site
     *
     * @return Data\Website
     */
    public function createStore(Data\Website $site)
    {
        $website = new Data\Website();
        $website->setWebsite($site->getWebsite());
        $website->setGroup($site->getGroup());
        $website->createStore($site->getWebsite()->getId(), $site->getGroup()->getId());
        $this->addWebsite($website);

        return $website;
    }

    /**
     * Remove all created sites
     *
     * todo-sg: clean DB indexes
     */
    public function removeSites()
    {
        foreach ($this->getWebsites() as $website) {
            $website->delete();
        }

        $this->websites = [];
    }

    /**
     * @return Data\Website[]
     */
    public function getWebsites()
    {
        return $this->websites;
    }

    /**
     * Gives us item in first position
     *
     * @return Data\Website
     */
    public function getFirstItem()
    {
        return current($this->websites);
    }
}
