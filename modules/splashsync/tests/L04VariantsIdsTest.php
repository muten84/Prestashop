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

namespace Splash\Tests;

use Splash\Tests\Tools\TestCase;
use Splash\Local\Objects\Product;

/**
 * @abstract    Local Objects Test Suite - Specific Verifications for Products Id Encoder.
 */
class L04VariantsIdsTest extends TestCase
{
    
    /**
     * @abstract    Test Product Ids Encoder for Compatibility & Functionnality
     *
     * @param int $pId Product Id
     * @param int $aId Product Id
     * @param int|string $result Expected Id for Splash
     *
     * @dataProvider productIdsProvider
     */
    public function testEncodeDecode($pId, $aId, $result)
    {
        $this->assertTrue(true);
        
        //====================================================================//
        // TEST ENCODING
        $sId    =   Product::getUnikIdStatic($pId, $aId);
        $this->assertSame($result, $sId);
        //====================================================================//
        // TEST DECODING
        $productId =   Product::getId($sId);
        $this->assertSame((int) $pId, $productId);
        $attrId =   Product::getAttribute($sId);
        $this->assertSame((int) $aId, $attrId);
    }
    
    /**
     * @abstract    Override Parent Function to Filter on Products Fields
     */
    public function productIdsProvider()
    {
        $response = array();

        //====================================================================//
        // NO ATTRIBUTE ID => Single Int
        $response[] =   array(12345, 0, 12345 );
        $response[] =   array(12345, "0", 12345 );
        $response[] =   array(12345, null, 12345 );
        $response[] =   array("12345", 0, 12345 );
        $response[] =   array("12345", "0", 12345 );
        $response[] =   array("12345", null, 12345 );
        
        
        //====================================================================//
        // OLD ENCODING => Both Ids on A Single Int
        $response[] =   array(1, 1, 1 + (1 << 20));
        $response[] =   array(0xFFFFF, 0x7FF, 0xFFFFF + (0x7FF << 20));
            
        for ($i=0; $i<100; $i++) {
            $pId    =   rand(1, 0xFFFFF);
            $aId    =   rand(1, 0x7FF);
            $response[] =   array($pId, $aId, $pId + ($aId << 20));
        }

        //====================================================================//
        // NEW ENCODING => Both Ids on A Single String pId@@aId
        for ($i=0; $i<100; $i++) {
            $pId    =   rand(0xFFFFF + 1, PHP_INT_MAX);
            $aId    =   rand(0x7FF + 1, PHP_INT_MAX);
            $response[] =   array($pId, $aId, $pId . "@@" . $aId);
        }
        return $response;
    }
}
