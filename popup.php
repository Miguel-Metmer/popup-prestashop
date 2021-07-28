<?php
/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Popup extends Module
{
    protected $config_form = false;
    public $isUploaded;

    public function __construct()
    {
        $this->name = 'popup';
        $this->tab = 'front_office_features';
        $this->version = '0.0.5';
        $this->author = 'Miguel Metmer';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('popup');
        $this->description = $this->l('Module de popup pour page d\'accueil');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if(!parent::install() || !$this->createTable() || !$this->registerHook('backOfficeHeader') || !$this->registerHook('displayHome'))
        {
            return false;
        }
        else
        {
            return true;
        }
        // return parent::install() &&
        //     $this->registerHook('backOfficeHeader') &&
        //     $this->registerHook('displayHome');
    }

    private function createTable()
    {
        return Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS le_lpfpopup(id int(6) AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) NOT NULL)" );
    }

    public function uninstall()
    {
        Db::getInstance()->Execute("DROP TABLE IF EXISTS le_lpfpopup");
        return parent::uninstall();
    }

    private function removeImage($img)
    {
        $query = "DELETE FROM le_lpfpopup WHERE name = '$img'";

        return Db::getInstance()->execute($query);
    }

    public function processContent()
    {

        if (Tools::isSubmit("popup-remove-image"))
        {
            $img = Tools::getValue("rm");
            $this->removeImage($img);
        }

        if (Tools::isSubmit("popup-submit")) 
        {
            $url = Tools::getValue("url");
            ConfigurationCore::updateValue("popup-link", $url);
        }

        if (Tools::isSubmit("popup-submit2")) 
        {
            $url = Tools::getValue("url2");
            ConfigurationCore::updateValue("popup-link2", $url);
        }

        if (Tools::isSubmit("popup-submit3")) 
        {
            $url = Tools::getValue("url3");
            ConfigurationCore::updateValue("popup-link3", $url);
        }

        if (Tools::isSubmit("popup-submit4")) 
        {
            $url = Tools::getValue("url4");
            ConfigurationCore::updateValue("popup-link4", $url);
        }

        if (Tools::isSubmit("popup-date-submit")) 
        {
            $date = Tools::getValue("date");
            $dateB = Tools::getValue("dateBegin");
            ConfigurationCore::updateValue("popup-date", $date);
            ConfigurationCore::updateValue("popup-date-begin", $dateB);
        }

        if (Tools::isSubmit("popup-date-submit2")) 
        {
            $date = Tools::getValue("date2");
            $dateB = Tools::getValue("dateBegin2");
            ConfigurationCore::updateValue("popup-date2", $date);
            ConfigurationCore::updateValue("popup-date-begin2", $dateB);
        }

        if (Tools::isSubmit("popup-date-submit3")) 
        {
            $date = Tools::getValue("date3");
            $dateC = Tools::getValue("dateBegin3");
            ConfigurationCore::updateValue("popup-date3", $date);
            ConfigurationCore::updateValue("popup-date-begin3", $dateC);
        }

        if (Tools::isSubmit("popup-date-submit4")) 
        {
            $date = Tools::getValue("date4");
            $dateD = Tools::getValue("dateBegin4");
            ConfigurationCore::updateValue("popup-date4", $date);
            ConfigurationCore::updateValue("popup-date-begin4", $dateD);
        }

        if (Tools::isSubmit("popup-color-submit"))
        {
            $color = Tools::getValue("color");
            ConfigurationCore::updateValue("color-button", $color);
        }

        if (Tools::isSubmit("popup-color-submit2"))
        {
            $color = Tools::getValue("color2");
            ConfigurationCore::updateValue("color-button2", $color);
        }

        if (Tools::isSubmit("popup-color-submit3"))
        {
            $color = Tools::getValue("color3");
            ConfigurationCore::updateValue("color-button3", $color);
        }

        if (Tools::isSubmit("popup-color-submit4"))
        {
            $color = Tools::getValue("color4");
            ConfigurationCore::updateValue("color-button4", $color);
        }

        if (Tools::isSubmit("popup-submit-image"))
        {
           $this->uploadImage();
        }
    }


    private function uploadImage()
    {
        // var_dump($_FILES["media"]);   

        $file = basename(str_replace(' ', '_', $_FILES["media"]["name"]));
        $file_target = _PS_MODULE_DIR_ . 'popup/upload/' . $file;

        if(file_exists($file))
        {
            echo 'Le fichier existe déjà !';
        }
        else
        {
            if(move_uploaded_file($_FILES["media"]["tmp_name"], $file_target))
            {
                $this->setUploadGood();
                $query = 'INSERT INTO le_lpfpopup (name) VALUES("' . str_replace(' ', '_',$_FILES["media"]["name"]) . '")';
                Db::getInstance()->execute($query);
            }
            else
            {
                // $isUploaded = false;
            }
        }
    }

    private function setUploadGood()
    {
        $this->$isUploaded = true;
    }

    private function getUploadState()
    {
        return $this->$isUploaded;
    }

    private function getImages()
    {
        $query = 'SELECT * FROM le_lpfpopup';
        $result = Db::getInstance()->executeS($query);
        return $result;
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {

        MediaCore::addJsDef(array('popup' => array('color' => ConfigurationCore::get("color"))));
        $this->context->controller->addJS($this->_path.'/views/js/back.js');
        $this->context->controller->addCSS($this->_path.'/views/css/back.css');

        $this->processContent();


        $url = ConfigurationCore::get("popup-link");
        $url2 = ConfigurationCore::get("popup-link2");
        $url3 = ConfigurationCore::get("popup-link3");
        $url4 = ConfigurationCore::get("popup-link4");

        $date = ConfigurationCore::get("popup-date");
        $dateB = ConfigurationCore::get("popup-date-begin");
        
        $date2 = ConfigurationCore::get("popup-date2");
        $dateB2 = ConfigurationCore::get("popup-date-begin2");

        $date3 = ConfigurationCore::get("popup-date3");
        $dateB3 = ConfigurationCore::get("popup-date-begin3");

        $date4 = ConfigurationCore::get("popup-date4");
        $dateB4 = ConfigurationCore::get("popup-date-begin4");

        $now = new DateTime("now");
        $dateEnd = new DateTime($date);
        $dateBegin = new DateTime($dateB);

        $dateEnd2 = new DateTime($date2);
        $dateBegin2 = new DateTime($dateB2);

        $dateEnd3 = new DateTime($date3);
        $dateBegin3 = new DateTime($dateB3);

        $dateEnd4 = new DateTime($date4);
        $dateBegin4 = new DateTime($dateB4);


        if ($now < $dateEnd && $now > $dateBegin) 
        {
            $isActive = true;
        }
        else
        {
            $isActive = false;
        }

        if ($now < $dateEnd2 && $now > $dateBegin2) 
        {
            $isActive2 = true;
        }
        else
        {
            $isActive2 = false;
        }

        if ($now < $dateEnd3 && $now > $dateBegin3) 
        {
            $isActive3 = true;
        }
        else
        {
            $isActive3 = false;
        }

        if ($now < $dateEnd4 && $now > $dateBegin4) 
        {
            $isActive4 = true;
        }
        else
        {
            $isActive4 = false;
        }

        $this->context->smarty->assign(array(
            'url' => $url,
            'url2' => $url2,
            'url3' => $url3,
            'url4' => $url4,
            "date" => $date,
            "date2" => $date2,
            "date3" => $date3,
            "date4" => $date4,
            "dateBegin" => $dateB,
            "dateBegin2" => $dateB2,
            "dateBegin3" => $dateB3,
            "dateBegin4" => $dateB3,
            "isActive" => $isActive,
            "isActive2" => $isActive2,
            "isActive3" => $isActive3,
            "isActive4" => $isActive4,
            "color" => ConfigurationCore::get("color-button"),
            "color2" => ConfigurationCore::get("color-button2"),
            "color3" => ConfigurationCore::get("color-button3"),
            "color4" => ConfigurationCore::get("color-button4"),
            'confirmation' => "Ok",
            'isUploaded' => $this->getUploadState(),
            'images' => $this->getImages()
        ));

        return $this->display(__FILE__, "getContent.tpl");
    }


  

    public function hookDisplayHome()
    {
        $url = ConfigurationCore::get("popup-link");
        $url2 = ConfigurationCore::get("popup-link2");
        $url3 = ConfigurationCore::get("popup-link3");
        $url4 = ConfigurationCore::get("popup-link4");

        $date = ConfigurationCore::get("popup-date");
        $dateB = ConfigurationCore::get("popup-date-begin");
        
        $date2 = ConfigurationCore::get("popup-date2");
        $dateB2 = ConfigurationCore::get("popup-date-begin2");

        $date3 = ConfigurationCore::get("popup-date3");
        $dateB3 = ConfigurationCore::get("popup-date-begin3");

        $date4 = ConfigurationCore::get("popup-date4");
        $dateB4 = ConfigurationCore::get("popup-date-begin4");

        $now = new DateTime("now");
        $dateEnd = new DateTime($date);
        $dateBegin = new DateTime($dateB);

        $dateEnd2 = new DateTime($date2);
        $dateBegin2 = new DateTime($dateB2);

        $dateEnd3 = new DateTime($date3);
        $dateBegin3 = new DateTime($dateB3);

        $dateEnd4 = new DateTime($date4);
        $dateBegin4 = new DateTime($dateB4);


        if ($now < $dateEnd && $now > $dateBegin) 
        {
            $isActive = true;
        }
        else
        {
            $isActive = false;
        }

        if ($now < $dateEnd2 && $now > $dateBegin2) 
        {
            $isActive2 = true;
        }
        else
        {
            $isActive2 = false;
        }

        if ($now < $dateEnd3 && $now > $dateBegin3) 
        {
            $isActive3 = true;
        }
        else
        {
            $isActive3 = false;
        }

        if ($now < $dateEnd4 && $now > $dateBegin4) 
        {
            $isActive4 = true;
        }
        else
        {
            $isActive4 = false;
        }

        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');

        $this->context->smarty->assign(array(
            'url' => $url,
            'url2' => $url2,
            'url3' => $url3,
            'url4' => $url4,
            'isActive' => $isActive,
            'isActive2' => $isActive2,
            'isActive3' => $isActive3,
            'isActive4' => $isActive4,
            "color" => ConfigurationCore::get("color-button"),
            "color2" => ConfigurationCore::get("color-button2"),
            "color3" => ConfigurationCore::get("color-button3"),
            "color4" => ConfigurationCore::get("color-button4"),
            'images' => $this->getImages()
        ));

        return $this->display(__FILE__, 'displayHome.tpl');
    }
}
