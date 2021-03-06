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

namespace Splash\Local\Objects\Product;

use Splash\Client\Splash;
use Context;
use Product as PsProduct;

use Splash\Local\Objects\Product;

/**
 * @abstract Prestashop Hooks for Products
 */
trait HooksTrait
{
//====================================================================//
// *******************************************************************//
//  MODULE BACK OFFICE (PRODUCTS) HOOKS
// *******************************************************************//
//====================================================================//


    /**
    *   @abstract       This hook is called after a customer is created
    */
    public function hookactionObjectProductAddAfter($params)
    {
        return $this->hookactionProduct($params["object"], SPL_A_CREATE, $this->l('Product Created on Prestashop'));
    }
        
    /**
    *   @abstract       This hook is called after a customer is created
    */
    public function hookactionObjectProductUpdateAfter($params)
    {
        return $this->hookactionProduct($params["object"], SPL_A_UPDATE, $this->l('Product Updated on Prestashop'));
    }
    
    /**
    *   @abstract       This hook is called after a customer is created
    */
    public function hookactionObjectProductDeleteAfter($params)
    {
        return $this->hookactionProduct($params["object"], SPL_A_DELETE, $this->l('Product Deleted on Prestashop'));
    }
      
    /**
     * @abstract    Get Product Impacted Ids to Commit
     * @param       object   $product           Prestashop Product Object
     * @return      array                       Array of Unik Ids
     */
    private function getActionProductIds($product)
    {
        //====================================================================//
        // Ensure Input is Product Class
        if (!is_a($product, PsProduct::class)) {
            $product = new PsProduct($product["id_product"]);
        }
        //====================================================================//
        // Read Product Combinations
        $AttrList = $product->getAttributesResume(Context::getContext()->language->id);
        //====================================================================//
        // if Product has Combinations
        if (is_array($AttrList) && !empty($AttrList)) {
            //====================================================================//
            // JUST FOR TRAVIS => Only Commit Id of Curent Impacted Combination
            if (defined("SPLASH_DEBUG")) {
                if (isset(Splash::object("Product")->AttributeId) && !empty(Splash::object("Product")->AttributeId)) {
                    //====================================================================//
                    // Add Current Product Combinations to Commit Update List
                    return array(
                        (int) Product::getUnikIdStatic($product->id, Splash::object("Product")->AttributeId)
                    );
                }
            }
            //====================================================================//
            // Add Product Combinations to Commit Update List
            $IdList = array();
            foreach ($AttrList as $Attr) {
                $IdList[] =   (int) Product::getUnikIdStatic($product->id, $Attr["id_product_attribute"]);
            }
            return  $IdList;
        }
        return array($product->id);
    }
    
    /**
     *      @abstract   This function is called after each action on a product object
     *      @param      object   $product           Prestashop Product Object
     *      @param      string   $action            Performed Action
     *      @param      string   $comment           Action Comment
     */
    private function hookactionProduct($product, $action, $comment)
    {
        //====================================================================//
        // Safety Check
        if (!isset($product->id) || empty($product->id)) {
            Splash::log()->err("ErrLocalTpl", "Product", __FUNCTION__, "Unable to Read Product Id.");
        }
        //====================================================================//
        // Log
        $this->debugHook(__FUNCTION__, $product->id . " >> " . $comment);
        //====================================================================//
        // Combination Lock Mode => Splash is Creating a Variant Product
        if (Splash::object("Product")->isLocked("onCombinationLock")) {
            return;
        }
        //====================================================================//
        // Get Product Impacted Ids to Commit
        $IdList = $this->getActionProductIds($product);
        if (empty($IdList)) {
            return true;
        }
        //====================================================================//
        // Commit Update For Product
        return $this->doCommit("Product", $IdList, $action, $comment);
    }
    
    /**
    *   @abstract       This hook is called after a customer is created
    */
    public function hookactionObjectCombinationAddAfter($params)
    {
        return $this->hookactionCombination(
            $params["object"],
            SPL_A_CREATE,
            $this->l('Product Variant Created on Prestashop')
        );
    }
        
    /**
    *   @abstract       This hook is called after a customer is created
    */
    public function hookactionObjectCombinationUpdateAfter($params)
    {
        return $this->hookactionCombination(
            $params["object"],
            SPL_A_UPDATE,
            $this->l('Product Variant Updated on Prestashop')
        );
    }
    
    /**
    *   @abstract       This hook is called after a customer is created
    */
    public function hookactionObjectCombinationDeleteAfter($params)
    {
        return $this->hookactionCombination(
            $params["object"],
            SPL_A_DELETE,
            $this->l('Product Variant Deleted on Prestashop')
        );
    }
        
    /**
    *   @abstract       This hook is called after a customer effectively places their order
    */
    public function hookactionUpdateQuantity($params)
    {
        //====================================================================//
        // Log
        $this->debugHook(__FUNCTION__, 'Product Stock Updated on Prestashop');
        //====================================================================//
        // On Product Admin Page stock Update
        if (!isset($params["cart"])) {
            //====================================================================//
            // Commit Update For Product
            $this->doCommit(
                "Product",
                $this->getActionProductIds($params),
                SPL_A_UPDATE,
                $this->l('Product Stock Updated on Prestashop')
            );
            return;
        }
        //====================================================================//
        // Get Products from Cart
        $Products = $params["cart"]->getProducts();
        //====================================================================//
        // Init Products Id Array
        $UnikId = array();
        //====================================================================//
        // Walk on Products
        foreach ($Products as $Product) {
            foreach ($this->getActionProductIds($Product) as $ProductId) {
                array_push($UnikId, $ProductId);
            }
        }
        //====================================================================//
        // Commit Update For Product
        $this->doCommit("Product", $UnikId, SPL_A_UPDATE, $this->l('Product Stock Updated on Prestashop'));
    }
    
    /**
     *      @abstract   This function is called after each action on a Combination object
     *      @param      object   $combination          Prestashop Combination Object
     *      @param      string   $action            Performed Action
     *      @param      string   $comment           Action Comment
     */
    private function hookactionCombination($combination, $action, $comment)
    {
        //====================================================================//
        // Retrieve Combination Id
        $id_combination = null;
        if (isset($combination->id)) {
            $id_combination = $combination->id;
        } elseif (isset($combination->id_product_attribute)) {
            $id_combination = $combination->id_product_attribute;
        }
        //====================================================================//
        // Log
        $this->debugHook(__FUNCTION__, $id_combination . " >> " . $comment);
        //====================================================================//
        // Safety Check
        if (empty($id_combination)) {
            return Splash::log()
                    ->err("ErrLocalTpl", "Combination", __FUNCTION__, "Unable to Read Product Attribute Id.");
        }
        if (empty($combination->id_product)) {
            return Splash::log()
                    ->err("ErrLocalTpl", "Combination", __FUNCTION__, "Unable to Read Product Id.");
        }
        //====================================================================//
        // Generate Unik Product Id
        $UnikId       =   (int) Product::getUnikIdStatic($combination->id_product, $id_combination);
        //====================================================================//
        // Commit Update For Product Attribute
        $this->doCommit("Product", $UnikId, $action, $comment);
        if ($action ==  SPL_A_CREATE) {
            //====================================================================//
            // Commit Update For Product Attribute
            $this->doCommit("Product", $combination->id_product, SPL_A_DELETE, $comment);
        }
    }
}
