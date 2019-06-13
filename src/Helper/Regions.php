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

use Magento\Customer\Api\Data\AddressInterface;
use Magento\Directory\Model\Region;
use Magento\Directory\Model\RegionFactory;
use Magento\Directory\Model\ResourceModel\Region\Collection;
use Shopgate\Base\Model\Utility\SgLogger;
use ShopgateAddress;

class Regions
{

    /** @var RegionFactory */
    private $regionFactory;
    /** @var SgLogger */
    private $logger;

    /**
     * @param RegionFactory $region
     * @param SgLogger      $logger
     */
    public function __construct(RegionFactory $region, SgLogger $logger)
    {
        $this->regionFactory = $region;
        $this->logger        = $logger;
    }

    /**
     * Return ISO-Code for Magento address
     *
     * @param AddressInterface $address
     *
     * @return null|string
     */
    public function getIsoStateByMagentoRegion(AddressInterface $address): ?string
    {
        $map      = $this->getIsoToMagentoMapping();
        $sIsoCode = null;

        if ($address->getCountryId() && $address->getRegion() && $address->getRegion()->getRegionCode()) {
            $sIsoCode = $address->getCountryId() . '-' . $address->getRegion()->getRegionCode();
        }

        if (isset($map[$address->getCountryId()])) {
            foreach ($map[$address->getCountryId()] as $isoCode => $mageCode) {
                if ($address->getRegion() && $mageCode === $address->getRegion()->getRegionCode()) {
                    $sIsoCode = $address->getCountryId() . '-' . $isoCode;
                    break;
                }
            }
        }

        return $sIsoCode;
    }

    /**
     * Magento default supported countries:
     * DE, AT, CH, CA, EE, ES, FI, FR, LT, LV, RO, US
     * Countries with correct iso-codes for region:
     * US, CA, CH, EE, FR, RO
     * Countries with incorrect iso-codes for region:
     * DE, AT, ES, FI, LT, LV
     * http://de.wikipedia.org/wiki/ISO_3166-2:DE
     * http://de.wikipedia.org/wiki/ISO_3166-2:AT
     * http://de.wikipedia.org/wiki/ISO_3166-2:ES
     * http://de.wikipedia.org/wiki/ISO_3166-2:FI
     * http://de.wikipedia.org/wiki/ISO_3166-2:LT
     * http://de.wikipedia.org/wiki/ISO_3166-2:LV
     *
     * @return array
     */
    protected function getIsoToMagentoMapping(): array
    {
        $map = [
            'DE' => [
                /* @see http://de.wikipedia.org/wiki/ISO_3166-2:DE */
                'BW' => 'BAW',
                'BY' => 'BAY',
                'BE' => 'BER',
                'BB' => 'BRG',
                'HB' => 'BRE',
                'HH' => 'HAM',
                'HE' => 'HES',
                'MV' => 'MEC',
                'NI' => 'NDS',
                'NW' => 'NRW',
                'RP' => 'RHE',
                'SL' => 'SAR',
                'SN' => 'SAS',
                'ST' => 'SAC',
                'SH' => 'SCN',
                'TH' => 'THE'
            ],
            'AT' => [
                /* @see http://de.wikipedia.org/wiki/ISO_3166-2:AT */
                '1' => 'BL',
                '2' => 'KN',
                '3' => 'NO',
                '4' => 'OO',
                '5' => 'SB',
                '6' => 'ST',
                '7' => 'TI',
                '8' => 'VB',
                '9' => 'WI',
            ],
            'ES' => [
                /* @see http://de.wikipedia.org/wiki/ISO_3166-2:ES */
                'C'  => 'A Coruсa',
                'VI' => 'Alava',
                'AB' => 'Albacete',
                'A'  => 'Alicante',
                'AL' => 'Almeria',
                'O'  => 'Asturias',
                'AV' => 'Avila',
                'BA' => 'Badajoz',
                'PM' => 'Baleares',
                'B'  => 'Barcelona',
                'BU' => 'Burgos',
                'CC' => 'Caceres',
                'CA' => 'Cadiz',
                'CS' => 'Castellon',
                'GI' => 'Girona',
                'CO' => 'Cordoba',
                'CU' => 'Cuenca',
                'GR' => 'Granada',
                'GU' => 'Guadalajara',
                'SS' => 'Guipuzcoa',
                'H'  => 'Huelva',
                'HU' => 'Huesca',
                'J'  => 'Jaen',
                'CR' => 'Ciudad Real',
                'S'  => 'Cantabria',
                'LO' => 'La Rioja',
                'GC' => 'Las Palmas',
                'LE' => 'Leon',
                'L'  => 'Lleida',
                'LU' => 'Lugo',
                'M'  => 'Madrid',
                'MA' => 'Malaga',
                'MU' => 'Murcia',
                'NA' => 'Navarra',
                'OR' => 'Ourense',
                'P'  => 'Palencia',
                'PO' => 'Pontevedra',
                'SA' => 'Salamanca',
                'TF' => 'Santa Cruz de Tenerife',
                'Z'  => 'Zaragoza',
                'SG' => 'Segovia',
                'SE' => 'Sevilla',
                'SO' => 'Soria',
                'T'  => 'Tarragona',
                'TE' => 'Teruel',
                'TO' => 'Toledo',
                'V'  => 'Valencia',
                'VA' => 'Valladolid',
                'BI' => 'Vizcaya',
                'ZA' => 'Zamora',
                'CE' => 'Ceuta',
                'ML' => 'Melilla',
            ],
            'LT' => [
                /* @see http://de.wikipedia.org/wiki/ISO_3166-2:LT */
                'AL' => 'LT-AL',
                'KU' => 'LT-KU',
                'KL' => 'LT-KL',
                'MR' => 'LT-MR',
                'PN' => 'LT-PN',
                'SA' => 'LT-SA',
                'TA' => 'LT-TA',
                'TE' => 'LT-TE',
                'UT' => 'LT-UT',
                'VL' => 'LT-VL',
            ],
            'FI' => [
                /* @see http://de.wikipedia.org/wiki/ISO_3166-2:FI */
                '01' => 'Ahvenanmaa',
                '02' => 'Etelä-Karjala',
                '03' => 'Etelä-Pohjanmaa',
                '04' => 'Etelä-Savo',
                '05' => 'Kainuu',
                '06' => 'Kanta-Häme',
                '07' => 'Keski-Pohjanmaa',
                '08' => 'Keski-Suomi',
                '09' => 'Kymenlaakso',
                '10' => 'Lappi',
                '11' => 'Pirkanmaa',
                '12' => 'Pohjanmaa',
                '13' => 'Pohjois-Karjala',
                '14' => 'Pohjois-Pohjanmaa',
                '15' => 'Pohjois-Savo',
                '16' => 'Päijät-Häme',
                '17' => 'Satakunta',
                '18' => 'Uusimaa',
                '19' => 'Varsinais-Suomi',
                '00' => 'Itä-Uusimaa', // !!not listed in wiki
            ],
            'LV' => [
                /* @see http://de.wikipedia.org/wiki/ISO_3166-2:LV */
                /* NOTE: 045 and 063 does not exist in magento */
                '001' => 'Aglonas novads',
                '002' => 'AI',
                '003' => 'Aizputes novads',
                '004' => 'Aknīstes novads',
                '005' => 'Alojas novads',
                '006' => 'Alsungas novads',
                '007' => 'AL',
                '008' => 'Amatas novads',
                '009' => 'Apes novads',
                '010' => 'Auces novads',
                '011' => 'Ādažu novads',
                '012' => 'Babītes novads',
                '013' => 'Baldones novads',
                '014' => 'Baltinavas novads',
                '015' => 'BL',
                '016' => 'BU',
                '017' => 'Beverīnas novads',
                '018' => 'Brocēnu novads',
                '019' => 'Burtnieku novads',
                '020' => 'Carnikavas novads',
                '021' => 'Cesvaines novads',
                '022' => 'CE',
                '023' => 'Ciblas novads',
                '024' => 'Dagdas novads',
                '025' => 'DA',
                '026' => 'DO',
                '027' => 'Dundagas novads',
                '028' => 'Durbes novads',
                '029' => 'Engures novads',
                '030' => 'Ērgļu novads',
                '031' => 'Garkalnes novads',
                '032' => 'Grobiņas novads',
                '033' => 'GU',
                '034' => 'Iecavas novads',
                '035' => 'Ikšķiles novads',
                '036' => 'Ilūkstes novads',
                '037' => 'Inčukalna novads',
                '038' => 'Jaunjelgavas novads',
                '039' => 'Jaunpiebalgas novads',
                '040' => 'Jaunpils novads',
                '041' => 'JL',
                '042' => 'JK',
                '043' => 'Kandavas novads',
                '044' => 'Kārsavas novads',
                /*'045' => '',*/
                '046' => 'Kokneses novads',
                '047' => 'KR',
                '048' => 'Krimuldas novads',
                '049' => 'Krustpils novads',
                '050' => 'KU',
                '051' => 'Ķeguma novads',
                '052' => 'Ķekavas novads',
                '053' => 'Lielvārdes novads',
                '054' => 'LM',
                '055' => 'Līgatnes novads',
                '056' => 'Līvānu novads',
                '057' => 'Lubānas novads',
                '058' => 'LU',
                '059' => 'MA',
                '060' => 'Mazsalacas novads',
                '061' => 'Mālpils novads',
                '062' => 'Mārupes novads',
                /*'063' => '',*/
                '064' => 'Naukšēnu novads',
                '065' => 'Neretas novads',
                '066' => 'Nīcas novads',
                '067' => 'OG',
                '068' => 'Olaines novads',
                '069' => 'Ozolnieku novads',
                '070' => 'Pārgaujas novads',
                '071' => 'Pāvilostas novads',
                '072' => 'Pļaviņu novads',
                '073' => 'PR',
                '074' => 'Priekules novads',
                '075' => 'Priekuļu novads',
                '076' => 'Raunas novads',
                '077' => 'RE',
                '078' => 'Riebiņu novads',
                '079' => 'Rojas novads',
                '080' => 'Ropažu novads',
                '081' => 'Rucavas novads',
                '082' => 'Rugāju novads',
                '083' => 'Rundāles novads',
                '084' => 'Rūjienas novads',
                '085' => 'Salas novads',
                '086' => 'Salacgrīvas novads',
                '087' => 'Salaspils novads',
                '088' => 'SA',
                '089' => 'Saulkrastu novads',
                '090' => 'Sējas novads',
                '091' => 'Siguldas novads',
                '092' => 'Skrīveru novads',
                '093' => 'Skrundas novads',
                '094' => 'Smiltenes novads',
                '095' => 'Stopiņu novads',
                '096' => 'Strenču novads',
                '097' => 'TA',
                '098' => 'Tērvetes novads',
                '099' => 'TU',
                '100' => 'Vaiņodes novads',
                '101' => 'VK',
                '102' => 'Varakļānu novads',
                '103' => 'Vārkavas novads',
                '104' => 'Vecpiebalgas novads',
                '105' => 'Vecumnieku novads',
                '106' => 'VE',
                '107' => 'Viesītes novads',
                '108' => 'Viļakas novads',
                '109' => 'Viļānu novads',
                '110' => 'Zilupes novads',
                // cities
                'DGV' => 'LV-DGV',
                'JKB' => 'Jēkabpils',
                'JEL' => 'LV-JEL',
                'JUR' => 'LV-JUR',
                'LPX' => 'LV-LPX',
                'REZ' => 'LV-REZ',
                'RIX' => 'LV-RIX',
                'VMR' => 'Valmiera',
                'VEN' => 'LV-VEN',
                // Unknown
                // '' => 'LV-LE', '' => 'LV-RI', '' => 'LV-VM',
            ],
        ];

        return $map;
    }

    /**
     * @param ShopgateAddress $address
     *
     * @return Region
     */
    public function getMageRegionByAddress($address): Region
    {
        $map = $this->getIsoToMagentoMapping();

        $state = preg_replace("/{$address->getCountry()}\-/", '', $address->getState());

        /** @var Collection $set */
        /** @var Region $region */
        $set    = $this->regionFactory->create()->getCollection();
        $region = $set->addCountryFilter($address->getCountry())
                      ->addFieldToFilter('main_table.code', $state)
                      ->getFirstItem();

        // If no region was found
        if (!empty($state) && !$region->getId() && isset($map[$address->getCountry()][$state])) {
            $regionCode = $map[$address->getCountry()][$state];

            $set    = $this->regionFactory->create()->getCollection();
            $region = $set->addRegionCodeFilter($regionCode)->addCountryFilter($address->getCountry())->getFirstItem();
        }

        if (!$region->getId()) {
            $this->logger->error('Region not found, state provided: ' . $state . ' country: ' . $address->getCountry());
        }

        return $region;
    }
}
