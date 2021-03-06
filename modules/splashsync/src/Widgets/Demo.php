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
                    
//====================================================================//
// *******************************************************************//
//                     SPLASH FOR DOLIBARR                            //
// *******************************************************************//
//                  TEST & DEMONSTRATION WIDGET                       //
// *******************************************************************//
//====================================================================//

namespace   Splash\Local\Widgets;

use Splash\Models\AbstractWidget;
use Splash\Core\SplashCore      as Splash;
use Splash\Local\Services\LanguagesManager;

/**
 *  \class      Address
 *  \brief      Address - Thirdparty Contacts Management Class
 */
class Demo extends AbstractWidget
{
    
    //====================================================================//
    // Object Definition Parameters
    //====================================================================//
    
    /**
     *  Widget Disable Flag. Uncomment thius line to Override this flag and disable Object.
     */
    protected static $DISABLED        =  true;
    
    /**
     *  Widget Name (Translated by Module)
     */
    protected static $NAME            =  "Demo Widget";
    
    /**
     *  Widget Description (Translated by Module)
     */
    protected static $DESCRIPTION     =  "TEST & DEMONSTRATION WIDGET";
    
    /**
     *  Widget Icon (FontAwesome or Glyph ico tag)
     */
    protected static $ICO            =  "fa fa-magic";
    
    //====================================================================//
    // Define Standard Options for this Widget
    // Override this array to change default options for your widget
    public static $OPTIONS       = array(
        "Width"     =>      self::SIZE_XL
    );
    
    //====================================================================//
    // General Class Variables
    //====================================================================//

    //====================================================================//
    // Class Constructor
    //====================================================================//
        
//    /**
//     *      @abstract       Class Constructor (Used only if localy necessary)
//     *      @return         int                     0 if KO, >0 if OK
//     */
//    function __construct()
//    {
//        //====================================================================//
//        // Place Here Any SPECIFIC Initialisation Code
//        //====================================================================//
//
//        return True;
//    }
    
    //====================================================================//
    // Class Main Functions
    //====================================================================//
    
    /**
     *      @abstract   Return Widget Customs Parameters
     */
    public function getParameters()
    {
        //====================================================================//
        // Reference
        $this->fieldsFactory()->create(SPL_T_VARCHAR)
                ->identifier("text_input")
                ->name("Text Input")
                ->description("Widget Specific Custom text Input");
        
        //====================================================================//
        // Reference
        $this->fieldsFactory()->create(SPL_T_INT)
                ->identifier("integer_input")
                ->name("Numeric Input")
                ->description("Widget Specific Custom Numeric Input");
        
        //====================================================================//
        // Publish Fields
        return $this->fieldsFactory()->publish();
    }
    
    /**
     *  @abstract     Return requested Customer Data
     *
     *  @param        array   $params               Search parameters for result List.
     *                        $params["start"]      Maximum Number of results
     *                        $params["end"]        List Start Offset
     *                        $params["groupby"]    Field name for sort list (Available fields listed below)

     */
    public function get($params = null)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Load Default Language
        LanguagesManager::loadDefaultLanguage();

        //====================================================================//
        // Setup Widget Core Informations
        //====================================================================//

        $this->setTitle($this->getName());
        $this->setIcon($this->getIcon());
        
        //====================================================================//
        // Build Intro Text Block
        //====================================================================//
        $this->buildIntroBlock();
          
        //====================================================================//
        // Build Inputs Block
        //====================================================================//
        $this->buildParametersBlock($params);
        
        //====================================================================//
        // Build Inputs Block
        //====================================================================//
        $this->buildNotificationsBlock();

        //====================================================================//
        // Set Blocks to Widget
        $blocks = $this->blocksFactory()->render();
        if (is_array($blocks)) {
            $this->setBlocks($blocks);
        }

        //====================================================================//
        // Publish Widget
        return $this->render();
    }
        

    //====================================================================//
    // Blocks Generation Functions
    //====================================================================//

    /**
    *   @abstract     Block Building - Text Intro
    */
    private function buildIntroBlock()
    {
        //====================================================================//
        // Into Text Block
        $this->blocksFactory()->addTextBlock("This is a Demo Text Block!!" . "You can repeat me as much as you want!");
    }
  
    /**
    *   @abstract     Block Building - Inputs Parameters
    */
    private function buildParametersBlock($Inputs = array())
    {

        //====================================================================//
        // verify Inputs
        if (!is_array($Inputs) && !is_a($Inputs, "ArrayObject")) {
            $this->blocksFactory()
                    ->addNotificationsBlock(array("warning" => "Inputs is not an Array! Is " . get_class($Inputs)));
        }
        
        //====================================================================//
        // Parameters Table Block
        $TableContents = array();
        $TableContents[]    =   array("Received " . count($Inputs) .  " inputs parameters","Value");
        foreach ($Inputs as $key => $value) {
            $TableContents[]    =   array($key, $value);
        }
        
        $this->blocksFactory()->addTableBlock($TableContents, array("Width" => self::SIZE_M));
    }
    
    /**
    *   @abstract     Block Building - Notifications Parameters
    */
    private function buildNotificationsBlock()
    {

        //====================================================================//
        // Notifications Block
        
        $Notifications = array(
            "error" =>  "This is a Sample Error Notification",
            "warning" =>  "This is a Sample Warning Notification",
            "success" =>  "This is a Sample Success Notification",
            "info" =>  "This is a Sample Infomation Notification",
        );
        
        
        $this->blocksFactory()->addNotificationsBlock($Notifications, array("Width" => self::SIZE_M));
    }
    
    //====================================================================//
    // Class Tooling Functions
    //====================================================================//
}
