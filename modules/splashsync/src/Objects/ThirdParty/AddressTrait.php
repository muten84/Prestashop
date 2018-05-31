<?php

/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Splash\Local\Objects\ThirdParty;

use Splash\Core\SplashCore      as Splash;

//====================================================================//
// Prestashop Static Classes	
use Address, Gender, Context, State, Country, Translate, Validate;
use DbQuery, Db, Customer, Tools;

/**
 * @abstract    Access to thirdparty Primary Address Fields
 * @author      B. Paquier <contact@splashsync.com>
 */
trait AddressTrait {

    /**
    *   @abstract     Build Customers Main Fields using FieldFactory
    */
    private function buildPrimaryAddressFields()
    {
        $GroupName  =   Translate::getAdminTranslation("Address", "AdminCustomers");

        //====================================================================//
        // Addess
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("address1")
                ->Name($GroupName)
                ->MicroData("http://schema.org/PostalAddress","streetAddress")
                ->Group($GroupName)
                ->isReadOnly();

        //====================================================================//
        // Addess Complement
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("address2")
                ->Name($GroupName . " (2)")
                ->MicroData("http://schema.org/PostalAddress","postOfficeBoxNumber")
                ->Group($GroupName)
                ->isReadOnly();
        
        //====================================================================//
        // Zip Code
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("postcode")
                ->Name(Translate::getAdminTranslation("Zip/Postal Code", "AdminAddresses"))
                ->MicroData("http://schema.org/PostalAddress","postalCode")
                ->Group($GroupName)
                ->isReadOnly();
        
        //====================================================================//
        // City Name
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("city")
                ->Name(Translate::getAdminTranslation("City", "AdminAddresses"))
                ->MicroData("http://schema.org/PostalAddress","addressLocality")
                ->Group($GroupName)
                ->isReadOnly();
        
        //====================================================================//
        // State Name
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("state")
                ->Name(Translate::getAdminTranslation("State", "AdminAddresses"))
                ->MicroData("http://schema.org/PostalAddress","addressRegion")
                ->Group($GroupName)
                ->isReadOnly();
        
        //====================================================================//
        // State code
        $this->fieldsFactory()->Create(SPL_T_STATE)
                ->Identifier("id_state")
                ->Name(Translate::getAdminTranslation("State", "AdminAddresses") . " (Code)")
                ->MicroData("http://schema.org/PostalAddress","addressRegion")
                ->Group($GroupName)
                ->isReadOnly();
        
        //====================================================================//
        // Country Name
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("country")
                ->Name(Translate::getAdminTranslation("Country", "AdminAddresses"))
                ->MicroData("http://schema.org/PostalAddress","addressCountry")
                ->Group($GroupName)
                ->isReadOnly();
        
        //====================================================================//
        // Country ISO Code
        $this->fieldsFactory()->Create(SPL_T_COUNTRY)
                ->Identifier("id_country")
                ->Name(Translate::getAdminTranslation("Country", "AdminAddresses") . " (Code)")
                ->MicroData("http://schema.org/PostalAddress","addressCountry")
                ->Group($GroupName)
                ->isReadOnly();
        
        //====================================================================//
        // Phone
        $this->fieldsFactory()->Create(SPL_T_PHONE)
                ->Identifier("phone")
                ->Name(Translate::getAdminTranslation("Home phone", "AdminAddresses"))
                ->MicroData("http://schema.org/PostalAddress","telephone")
                ->Group($GroupName)
                ->isReadOnly();
        
        //====================================================================//
        // Mobile Phone
        $this->fieldsFactory()->Create(SPL_T_PHONE)
                ->Identifier("phone_mobile")
                ->Name(Translate::getAdminTranslation("Mobile phone", "AdminAddresses"))
                ->MicroData("http://schema.org/Person","telephone")
                ->Group($GroupName)
                ->isReadOnly();

        //====================================================================//
        // VAT Number
        $this->fieldsFactory()->Create(SPL_T_VARCHAR)
                ->Identifier("vat_number")
                ->Name(Translate::getAdminTranslation("VAT Number", "AdminAddresses"))
                ->MicroData("http://schema.org/Organization","vatID")
                ->isReadOnly();        
        
    }
    
    /**
     *  @abstract     Read requested Field
     * 
     *  @return         bool
     */
    private function getAddressList() {
        
        //====================================================================//
        // Create List If Not Existing
        if (!isset($this->Out["contacts"])) {
            $this->Out["contacts"] = array();
        }

        //====================================================================//
        // Read Address List
        $AddresList = $this->Object->getAddresses(Context::getContext()->language->id);

        //====================================================================//
        // If Address List Is Empty => Null
        if (empty($AddresList)) {
            return True;
        }
                
        //====================================================================//
        // Run Through Address List
        foreach ($AddresList as $Key => $Address) {
            $this->Out["contacts"][$Key] = array ( "address" => self::Objects()->Encode( "Address" , $Address["id_address"]) );
        }
                
        return True;
    }    
    
    /**
     *  @abstract     Read requested Field
     * 
     *  @param        string    $Key                    Input List Key
     *  @param        string    $FieldName              Field Identifier / Name
     * 
     *  @return         none
     */
    private function getPrimaryAddressFields($Key,$FieldName)    
    {
        //====================================================================//
        // Identify Main Address Id
        $MainAddress = new Address( Address::getFirstCustomerAddressId($this->Object->id) );
        
        //====================================================================//
        // If Empty, Create A New One 
        if ( !$MainAddress ) {
            $MainAddress = new Address();
        }        
        
        //====================================================================//
        // READ Fields
        switch ($FieldName)
        {
            case 'address1':
            case 'address2':
            case 'postcode':
            case 'city':
            case 'country':
            case 'phone':
            case 'phone_mobile':
            case 'vat_number':
                //====================================================================//
                // READ Directly on Class
                $this->Out[$FieldName] = $MainAddress->$FieldName;
                unset($this->In[$Key]);
                break;
            case 'id_country':
                //====================================================================//
                // READ With Convertion
                $this->Out[$FieldName] = Country::getIsoById($MainAddress->id_country);
                unset($this->In[$Key]);
                break;
            case 'state':
                //====================================================================//
                // READ With Convertion
                $state = new State($MainAddress->id_state);
                $this->Out[$FieldName] = $state->name;
                unset($this->In[$Key]);
                break;
            case 'id_state':
                //====================================================================//
                // READ With Convertion
                $state = new State($MainAddress->id_state);
                $this->Out[$FieldName] = $state->iso_code;
                unset($this->In[$Key]);
                break;
        }
    }
    
    
}
