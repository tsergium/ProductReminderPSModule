<?php
/**
* productsreminder
*
* Remind user to buy selected products again.
* 
* @author anna@
**/

if (!defined('_PS_VERSION_'))
	exit;

class productsreminder extends Module
{
    public function __construct()
    {
        $this->name = 'productsreminder';
        $this->tab = 'front_office_features';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Product Reminder');
        $this->description = $this->l('Remind user to buy selected products again.');

        $this->version = '1.0';
        $this->author = 'anna@';
    }

    /**
    * @return bool success
    **/
    public function install()
    {
        return parent::install() 
            && $this->dbInstall()
            && $this->installModuleTab('AdminProductReminder', array(1=>'Product Reminder', 2=>'Product Reminder'), 0)
            && $this->registerHook('displayShoppingCartFooter')
			&& $this->registerHook('displayShoppingCart')
			&& $this->registerHook('displayOrderDetail')
		;
 	}

    /**
    * @return bool success
    **/
    public function uninstall()
    {
        return parent::uninstall() 
            && $this->dbUninstall()
            && $this->uninstallModuleTab('AdminProductReminder')
            ;            
    }
    
    /*
     * Create new admin tab
     */
   private function installModuleTab($tabClass, $tabName, $idTabParent)
    {
      $tab = new Tab();
      $tab->name = $tabName;
      $tab->class_name = $tabClass;
      $tab->module = $this->name;
      $tab->id_parent = $idTabParent;
      if(!$tab->save()){
        return false;
      }  
      return true;
    } 
    
     /*
     * Remove new admin tab
     */
    private function uninstallModuleTab($tabClass)
    {
      $idTab = Tab::getIdFromClassName($tabClass);
      if($idTab != 0)
      {
        $tab = new Tab($idTab);
        $tab->delete();
        return true;
      }
      return false;
    } 

    /**
    * Enable/Activate module.
    *
    * @param bool $forceAll If true, enable module for all shop
    */
    public function enable($forceAll = false)
    {
        return parent::enable($forceAll);
    }

    /**
    * Disable/Deactivate module.
    *
    * @param bool $forceAll If true, disable module for all shop
    */
    public function disable($forceAll = false)
    {
        return parent::disable($forceAll);
    }

    /**
     * TODO: Send an email containing the product needed to be reminded
     *
     * @param $email
     * @param $code
     *
     * @return bool|int
     */
    protected function sendReminder($email, $code)
    {
            return Mail::Send($this->context->language->id, 'reminder', Mail::l('Product Reminder', $this->context->language->id), array('{code}' => $code), $email, null, null, null, null, null, dirname(__FILE__).'/mails/', false, $this->context->shop->id);
    }

    /**
    * Module tables installation
    * 
    * @return bool success
    **/
    protected function dbInstall()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "product_reminder` (
                        `id_product_reminder` int(11) NOT NULL AUTO_INCREMENT,
                        `id_product` int(11) NOT NULL,
                        `id_customer` int(11) NOT NULL,
                        `period` enum('1','3','6','12') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
                        `last_reminded` datetime DEFAULT NULL,
                        `reminded` tinyint(1) DEFAULT '0',
                        `date` datetime DEFAULT NULL,
                        PRIMARY KEY (`id_product_reminder`),
                        KEY `id_product` (`id_product`,`id_customer`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
         if(!Db::getInstance()->Execute($sql)){
            return false;
        }    
        return true;
    }

    /**
    * Module tables uninstallation
    * 
    * @return bool success
    **/
    protected function dbUninstall()
    {
        $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'product_reminder` ;';
        if(!Db::getInstance()->Execute($sql)){
            return false;
        }    
        return true;
    }
    
    public function hookDisplayShoppingCart($params)
    {
            return $this->display(__FILE__, 'views/templates/front/cart-summary.tpl');
    }

    public function hookDisplayOrderDetail($params)
    {
            return $this->display(__FILE__, 'views/templates/front/order-history.tpl');
    }
}