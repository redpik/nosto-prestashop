<?php

/**
 * 2013-2017 Nosto Solutions Ltd
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@nosto.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    Nosto Solutions Ltd <contact@nosto.com>
 * @copyright 2013-2017 Nosto Solutions Ltd
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class NostoSearchTagging
{
    /**
     * Renders the current search term tagging by checking if the controller either has
     * the search_query or the s parameter (as it differs between versions.)
     * @return string|null the tagging
     */
    public static function get()
    {
        $searchTerm = Tools::getValue('search_query', Tools::getValue('s'));
        //$searchTerm could be boolean value false. Don't use is_null() here to verify the value
        if (!is_string($searchTerm) || $searchTerm == '') {
            return null;
        }

        $nostoQuery = NostoSearch::loadData($searchTerm);

        return $nostoQuery ? $nostoQuery->toHtml() : null;
    }
}
