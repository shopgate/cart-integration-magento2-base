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

namespace Shopgate\Base\Helper;

use Magento\Framework\Serialize\SerializerInterface;

class Encoder
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Serialize data into string
     *
     * @param string|int|float|bool|array|null $data
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function encode($data)
    {
        $result = json_encode($data);
        if (false === $result) {
            throw new \InvalidArgumentException('Unable to serialize value.');
        }

        return $result;
    }

    /**
     * Unserialize the given string
     *
     * @param string $string
     *
     * @return string|int|float|bool|array|null
     * @throws \InvalidArgumentException
     */
    public function decode($string)
    {
        if (!$string) {
            return [];
        }

        if ($this->isSerialized($string)) {
            return $this->unserialize($string);
        }

        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }

        return $result;
    }

    /**
     * Check if value is serialized string
     *
     * @param string $value
     *
     * @return boolean
     */
    private function isSerialized(string $value): bool
    {
        return preg_match('/^((s|i|d|b|a|O|C):|N;)/', $value);
    }

    /**
     * Uses the old school unserialization
     *
     * @param string $string
     *
     * @return array
     * @throws \InvalidArgumentException
     */
    private function unserialize($string)
    {
        if (false === $string || null === $string || '' === $string) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }
        set_error_handler(
            function () {
                restore_error_handler();
                throw new \InvalidArgumentException('Unable to unserialize value, string is corrupted.');
            },
            E_NOTICE
        );
        $result = $this->serializer->unserialize($string);
        restore_error_handler();

        return $result;
    }
}
