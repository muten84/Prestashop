<?php
/**
 * This file is part of SplashSync Project.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  @author    Splash Sync <www.splashsync.com>
 *  @copyright 2015-2018 Splash Sync
 *  @license   MIT
 */

namespace Splash\Local\Objects\ThirdParty;

//====================================================================//
// Prestashop Static Classes
use Translate;

/**
 * @abstract    Access to thirdparty Meta Fields
 */
trait MetaTrait
{


    /**
    *   @abstract     Build Customers Unused Fields using FieldFactory
    */
    private function buildMetaFields()
    {

        //====================================================================//
        // Active
        $this->fieldsFactory()->create(SPL_T_BOOL)
                ->Identifier("active")
                ->Name(Translate::getAdminTranslation("Enabled", "AdminCustomers"))
                ->MicroData("http://schema.org/Organization", "active")
                ->isListed();
        
        //====================================================================//
        // Newsletter
        $this->fieldsFactory()->create(SPL_T_BOOL)
                ->Identifier("newsletter")
                ->Name(Translate::getAdminTranslation("Newsletter", "AdminCustomers"))
                ->Group(Translate::getAdminTranslation("Meta", "AdminThemes"))
                ->MicroData("http://schema.org/Organization", "newsletter");
        
        //====================================================================//
        // Adverstising
        $this->fieldsFactory()->create(SPL_T_BOOL)
                ->Identifier("optin")
                ->Name(Translate::getAdminTranslation("Opt-in", "AdminCustomers"))
                ->Group(Translate::getAdminTranslation("Meta", "AdminThemes"))
                ->MicroData("http://schema.org/Organization", "advertising");
    }

    /**
     *  @abstract     Read requested Field
     *
     *  @param        string    $Key                    Input List Key
     *  @param        string    $FieldName              Field Identifier / Name
     *
     *  @return         none
     */
    private function getMetaFields($Key, $FieldName)
    {
            
        //====================================================================//
        // READ Fields
        switch ($FieldName) {
            case 'active':
            case 'newsletter':
            case 'passwd':
            case 'optin':
                $this->out[$FieldName] = $this->object->$FieldName;
                break;
            default:
                return;
        }
        
        unset($this->in[$Key]);
    }

    /**
     *  @abstract     Write Given Fields
     *
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        mixed     $Data                   Field Data
     *
     *  @return         none
     */
    private function setMetaFields($FieldName, $Data)
    {
        //====================================================================//
        // WRITE Fields
        switch ($FieldName) {
            case 'active':
            case 'newsletter':
            case 'optin':
                if ($this->object->$FieldName != $Data) {
                    $this->object->$FieldName = $Data;
                    $this->needUpdate();
                }
                break;
            default:
                return;
        }
        unset($this->in[$FieldName]);
    }
}
