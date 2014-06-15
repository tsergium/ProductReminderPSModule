<?php
require_once (dirname(__FILE__).'/ProductReminder.php');

class AdminProductReminderController extends ModuleAdminController 
{    
    
    public function __construct() {        
        $this->context      = Context::getContext();
        $this->bootstrap    = true;
        $this->table        = 'product_reminder';
        $this->identifier   = 'id_product_reminder';
        $this->className    = 'ProductReminder';
        $this->lang         = false;
        
        $this->addRowAction('edit');
        $this->addRowAction('delete'); 
        
        $this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'icon' => 'icon-trash',
				'confirm' => $this->l('Delete selected items?')
			)
		);
        
       $this->initList();
       
        parent::__construct();
    }  
    
    // listing helper
    private function initList()
    {
        $id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
         
        $this->fields_list = array(
            'id_product_reminder' => array(
                'title' => $this->l('Id'),
                'width' => 100,
                'type' => 'text',
            ),
            'id_customer' => array(
                'title' => $this->l('Customer Fistname'),
                'width' => 150,
                'type' => 'text',
                'filter_key' => 'c!firstname',
                'callback'     => 'getCustomerFirstName'
            ),
            'lastname' => array(
                'title' => $this->l('Lastname'),
                'width' => 150,
                'type' => 'text',
                'filter_key' => 'c!lastname',
                'callback'     => 'getCustomerLastName'
            ),
            'id_product' => array(
                'title' => $this->l('Product'),
                'width' => 250,
                'type' => 'text',
                'filter_key' => 'p!name',
                'callback'     => 'getProductName'
            ),
            'period' => array(
                'title' => $this->l('Period (months)'),
                'width' => 100,
                'type' => 'text',
            ),
        ); 

        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'customer` AS c ON (c.`id_customer` = a.`id_customer`) ';
        $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` AS p '
            . '                     ON (p.`id_product` = a.`id_product` AND p.`id_lang` = '.(int)$id_lang.') ';
        $this->_select .= ' c.firstname,c.lastname,p.name ';
        
        $helper = new HelperList();
        $helper->shopLinkType = '';
        $helper->simple_header = true;
        $helper->show_toolbar   = true;

        // Actions to be displayed in the "Actions" column
        $helper->actions = array('edit', 'delete');
        $helper->identifier     = 'id_product_reminder';        
        $helper->className      = 'ProductReminder';
        $helper->title          = 'HelperList';
        $helper->table          = 'product_reminder';

        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        return $helper;
    }
    
    //Assign default action in toolbar_btn smarty var, if they are not set. 
    //Override to specifically add, modify or remove items
    public function initToolbar()
    {
            parent::initToolbar();
            unset($this->toolbar_btn['new']);
    }
    
    /**
     * Returns product name for listing
     * @param type $echo
     * @param type $row
     * @return string
     */
    public function getProductName($echo, $row)
    {
        $defaultLangId = (int)Configuration::get('PS_LANG_DEFAULT');
        $id_product = $row['id_product'];
        $product = new ProductCore($id_product);
        return $product->name[$defaultLangId];     
    }  
	
    /**
     * Returns customer firstname for listing
     * @param type $echo
     * @param type $row
     * @return string
     */
    public function getCustomerFirstName($echo, $row) {
        $id_customer = $row['id_customer'];
        $customer = new Customer($id_customer);
        return $customer->firstname;
    }  
    
    /**
     * Returns customer lastname for listing
     * @param type $echo
     * @param type $row
     * @return type
     */
    public function getCustomerLastName($echo, $row) {
        $id_customer = $row['id_customer'];
        $customer = new Customer($id_customer);
        return $customer->lastname;
    } 
    

    // This method generates the Add/Edit form
    public function renderForm() {        
        
        $id_product_reminder = (int)Tools::getValue('id_product_reminder');
        $reminder = new ProductReminder($id_product_reminder);
        $period= $reminder->period;
        
        $options = array(
            array(
              'id_option' => 1,       // The value of the 'value' attribute of the <option> tag.
              'name' => '1 month'    // The value of the text content of the  <option> tag.
            ),
            array(
              'id_option' => 3,
              'name' => '3 months'
            ),
            array(
              'id_option' => 6,
              'name' => '6 months'
            ),
            array(
              'id_option' => 12,
              'name' => '12 months'
            ),
         );
        
        //creating form fileds
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Product Reminder')
            ),
            'input' => array(               
                array(
                    'type'  => 'select',
                    'label' => $this->l('Period:'),
                    'name'  => 'period',
                    'size'  => 1,                    
                    'required'  => true,
                    'options'   => array(
                        'query' => $options,                           // $options contains the data itself.
                        'id'    => 'id_option',                           // The value of the 'id' key must be the same as the key for 'value' attribute of the <option> tag in each $options sub-array.
                        'name'  => 'name' ,                              // The value of the 'name' key must be the same as the key for the text content of the <option> tag in each $options sub-array.
                    ),
                   
                ),
                 array(
                    'type'  => 'hidden',
                    'name'  => 'id_product_reminder',
                    'size'  => 1
                ),
            ),             
            'submit' => array(
                'title' => $this->l('   Save   '),
                'class' => 'button'
            )
        );

        //set values
        $this->fields_value['id_product_reminder'] = $id_product_reminder;
        $this->fields_value['period'] = $period;
        
        return parent::renderForm();
    }
        
}