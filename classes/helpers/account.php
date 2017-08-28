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

use Nosto\Object\Signup\Account as NostoSDKAccount;
use Nosto\Request\Api\Token as NostoSDKAPIToken;
use Nosto\Types\Signup\AccountInterface as NostoSDKSignupAccountInterface;

/**
 * Helper class for managing Nosto accounts.
 */
class NostoHelperAccount
{

    /**
     * Saves a Nosto account to PS config.
     * Also handles any attached API tokens.
     *
     * @param NostoSDKSignupAccountInterface $account the account to save.
     * @return bool true if the save was successful, false otherwise.
     */
    public static function save(NostoSDKSignupAccountInterface $account)
    {
        $success = NostoHelperConfig::saveAccountName($account->getName());
        if ($success) {
            foreach ($account->getTokens() as $token) {
                $success = $success && NostoHelperConfig::saveToken($token->getName(), $token->getValue());
            }
        }
        return $success;
    }

    /**
     * Deletes a Nosto account from the PS config.
     * Also sends a notification to Nosto that the account has been deleted.
     *
     * @return bool true if successful, false otherwise.
     */
    public static function delete()
    {
        $success = NostoHelperConfig::deleteAllFromContext();
        return $success;
    }

    /**
     * Deletes all Nosto accounts from the system and notifies nosto that accounts are deleted.
     *
     * @return bool
     */
    public static function deleteAll()
    {
        NostoHelperContext::runInAContextForEachLanguageEachShop(function (){
            self::delete();
        });

        return true;
    }

    /**
     * Finds and returns an account for given criteria.
     *
     * @return NostoSDKAccount|null the account with loaded API tokens, or null if not found.
     * @internal param int|null $lang_id the ID of the language.
     */
    public static function find()
    {
        $account_name = NostoHelperConfig::getAccountName(
            NostoHelperContext::getLanguageId(),
            NostoHelperContext::getShopGroupId(),
            NostoHelperContext::getShopId()
        );
        if (!empty($account_name)) {
            $account = new NostoSDKAccount($account_name);
            $tokens = array();
            foreach (NostoSDKAPIToken::getApiTokenNames() as $token_name) {
                $token_value = NostoHelperConfig::getToken($token_name);
                if (!empty($token_value)) {
                    $tokens[$token_name] = $token_value;
                }
            }

            if (!empty($tokens)) {
                foreach ($tokens as $name => $value) {
                    $account->addApiToken(new NostoSDKAPIToken($name, $value));
                }
            }

            return $account;
        }
        return null;
    }

    /**
     * Checks if an account exists and is "connected to Nosto" for given criteria.
     *
     * @return bool true if it does, false otherwise.
     */
    public static function existsAndIsConnected()
    {
        $account = self::find();
        return ($account !== null && $account->isConnectedToNosto());
    }
}
