<?php 
if (!defined('_PS_VERSION_'))
    exit;
 
class productReminder extends Module
{
    /* @var boolean error */
    protected $_errors = false;
     
    public function __construct()
    {
        $this->name = 'productreminder';
        $this->tab = 'front_office_features';
        $this->version = '1.0';
        $this->author = 'Anamari@';
        $this->need_instance = 0;
 
        parent::__construct();
 
        $this->displayName = $this->l('Remind products');
        $this->description = $this->l('This module will allow users to select products that they want to be reminded about and the administrator can see the list of all reminders.');
    }
	
    public function install()
    {  
        if (!parent::install() OR
            !$this->alterTable('add') OR
            !$this->registerHook('displayShoppingCart')
            ){     
                return false;
            }
            
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() OR !$this->alterTable('remove')){
                return false;
        }        
        return true;
    }


    public function alterTable($method)
    {
        switch ($method) {
            case 'add':
                $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "product_reminder` (
                        `id_product_reminder` int(11) NOT NULL AUTO_INCREMENT,
                        `id_product` int(11) NOT NULL,
                        `id_customer` int(11) NOT NULL,
                        `period` enum('1','3','6','12') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',
                        `last_reminded` datetime DEFAULT NULL,
                        `reminded` datetime DEFAULT NULL,
                        `date` datetime DEFAULT NULL,
                        PRIMARY KEY (`id_product_reminder`),
                        KEY `id_product` (`id_product`,`id_customer`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
                break;

            case 'remove':
                $sql = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'product_reminder` ;';
                break;
        }

        if(!Db::getInstance()->Execute($sql)){
            return false;
        }    
        return true;
    }
//    if ($this->context->customer->isLogged())
         
}
?>