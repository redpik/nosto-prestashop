<?php
/**
 * 2013-2016 Nosto Solutions Ltd
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
 * @copyright 2013-2016 Nosto Solutions Ltd
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Meta data class for oauth related information needed when connecting accounts to Nosto.
 */
class NostoTaggingMetaOauth extends Nosto\OAuth
{
    /**
     * Loads meta data from the given context and language.
     *
     * @param Context $context the context to use as data source.
     * @param int $id_lang the language to use as data source.
     * @param $moduleName
     * @param $modulePath
     * @return NostoTaggingMetaOauth|null
     */
    public static function loadData($context, $id_lang, $moduleName, $modulePath)
    {
        $language = new Language($id_lang);
        if (!Validate::isLoadedObject($language)) {
            return null;
        }

        $id_lang = (int)$context->language->id;
        $id_shop = (int)$context->shop->id;

        $oauthParams = new NostoTaggingMetaOauth();

        try {
            $oauthParams->setScopes(Nosto\Request\Api\Token::getApiTokenNames());
            /** @var NostoTaggingHelperUrl $url_helper */
            $url_helper = Nosto::helper('nosto_tagging/url');

            $redirectUrl = $url_helper->getModuleUrl(
                $moduleName,
                $modulePath,
                'oauth2',
                $id_lang,
                $id_shop,
                array('language_id' => (int)$language->id)
            );

            $oauthParams->setClientId('prestashop');
            $oauthParams->setClientSecret('prestashop');
            $oauthParams->setRedirectUrl($redirectUrl);
            $oauthParams->setLanguageIsoCode($language->iso_code);
        } catch (Nosto\NostoException $e) {
            /* @var NostoTaggingHelperLogger $logger */
            $logger = Nosto::helper('nosto_tagging/logger');
            $logger->error(
                __CLASS__.'::'.__FUNCTION__.' - '.$e->getMessage(),
                $e->getCode()
            );
        }

        return $oauthParams;
    }
}
