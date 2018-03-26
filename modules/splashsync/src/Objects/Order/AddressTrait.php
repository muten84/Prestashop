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

namespace Splash\Local\Objects\Order;

/**
 * @abstract    Access to Order Address Fields
 * @author      B. Paquier <contact@splashsync.com>
 */
trait AddressTrait {

    /**
    *   @abstract     Build Fields using FieldFactory
    */
    private function buildAddressFields()
    {
        
        //====================================================================//
        // Billing Address
        $this->FieldsFactory()->Create(self::Objects()->Encode( "Address" , SPL_T_ID))
                ->Identifier("id_address_invoice")
                ->Name('Billing Address ID')
                ->MicroData("http://schema.org/Order","billingAddress")
                ->isRequired();  
        
        //====================================================================//
        // Shipping Address
        $this->FieldsFactory()->Create(self::Objects()->Encode( "Address" , SPL_T_ID))
                ->Identifier("id_address_delivery")
                ->Name('Shipping Address ID')
                ->MicroData("http://schema.org/Order","orderDelivery")
                ->isRequired();  
        
    }
  
    
    /**
     *  @abstract     Read requested Field
     * 
     *  @param        string    $Key                    Input List Key
     *  @param        string    $FieldName              Field Identifier / Name
     * 
     *  @return         none
     */
    private function getAddressFields($Key,$FieldName)    
    {
        //====================================================================//
        // READ Fields
        switch ($FieldName)
        {  
            //====================================================================//
            // Customer Address Ids
            case 'id_address_invoice':
            case 'id_address_delivery':
                if ( get_class($this) ===  "Splash\Local\Objects\Invoice" ) {
                    $this->Out[$FieldName] = self::Objects()->Encode( "Address" , $this->Order->$FieldName );
                } else {
                    $this->Out[$FieldName] = self::Objects()->Encode( "Address" , $this->Object->$FieldName );
                }
                break;   
            default:
                return;
        }
        unset($this->In[$Key]);
    }
    
    /**
     *  @abstract     Write Given Fields
     * 
     *  @param        string    $FieldName              Field Identifier / Name
     *  @param        mixed     $Data                   Field Data
     * 
     *  @return         none
     */
    private function setAddressFields($FieldName,$Data) 
    {     
        //====================================================================//
        // WRITE Field
        switch ($FieldName)
        {
            //====================================================================//
            // Customer Address Ids
            case 'id_address_invoice':
            case 'id_address_delivery':
                $this->setSimple($FieldName,self::Objects()->Id( $Data ));
                break;   
                
            default:
                return;
        }
        unset($this->In[$FieldName]);
    }

}