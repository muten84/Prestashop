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

namespace Splash\Local\Objects\Invoice;

use Splash\Core\SplashCore      as Splash;

//====================================================================//
// Prestashop Static Classes
//====================================================================//
// Prestashop Static Classes
use Order;
use OrderInvoice;
use TaxCalculator;

/**
 * @abstract    Prestashop Invoices CRUD Functions
 */
trait CRUDTrait
{
    /**
     * @var Order
     */
    protected $Order          = null;
    
    /**
     * @var TaxCalculator
     */
    protected $ShippingTaxCalculator = null;
    
    /**
     * @abstract    Load Request Object
     * @param       string  $Id               Object id
     * @return      mixed
     */
    public function load($Id)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        
        //====================================================================//
        // Load Object
        $Object   = new OrderInvoice($Id);
        if ($Object->id != $Id) {
            return Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, " Unable to load Invoice (" . $Id . ").");
        }
        $this->Order    = new Order($Object->id_order);
        if ($this->Order->id != $Object->id_order) {
            return Splash::log()->err(
                "ErrLocalTpl",
                __CLASS__,
                __FUNCTION__,
                " Unable to load Invoice Order (" . $Object->id_order . ")."
            );
        }
        
        //====================================================================//
        // Load Order Products
        $this->Products         = $Object->getProductsDetail();
        $this->Payments         = $Object->getOrderPaymentCollection();
        $this->PaymentMethod    = $this->Order->module;
        
        //====================================================================//
        // Load Shipping Tax Calculator
        $this->ShippingTaxCalculator    = (new \Carrier($this->Order->id_carrier))
                    ->getTaxCalculator(new \Address($this->Order->id_address_delivery));

        return $Object;
    }

    /**
     * @abstract    Create Request Object
     *
     * @return      object     New Object
     */
    public function create()
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        
        //====================================================================//
        // An Invoice Cannot Get deleted
        Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, "You Cannot Create Prestashop Invoices");
        
        return null;
    }
    
    /**
     * @abstract    Update Request Object
     *
     * @param       array   $Needed         Is This Update Needed
     *
     * @return      string      Object Id
     */
    public function update($Needed)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        if (!$Needed) {
            return (int) $this->object->id;
        }
        
        //====================================================================//
        // An Invoice Cannot Get deleted
        Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, "You Cannot Update Prestashop Invoices");
        
        return (int) $this->object->id;
    }
    
    /**
     * @abstract    Delete requested Object
     *
     * @param       int     $Id     Object Id.  If NULL, Object needs to be created.
     *
     * @return      bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function delete($Id = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        
        //====================================================================//
        // An Invoice Cannot Get deleted
        Splash::log()->err("ErrLocalTpl", __CLASS__, __FUNCTION__, "You Cannot Delete Prestashop Invoices");
        
        return true;
    }
}
