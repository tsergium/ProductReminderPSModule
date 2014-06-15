<?php
class ProductReminder extends ObjectModel {
    //fields to store into the database
    public $id;
    public $id_product_reminder;
    public $id_product;
    public $id_customer;
    public $last_reminded;
    public $period;
    public $reminded;
    public $date;

     /* 
     *//**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'product_reminder',
        'primary' => 'id_product_reminder',
        'fields' => array(
            'id_product'        =>    array('type' => self::TYPE_INT,   'validate' => 'isInt',    'required' => true, 'size' => 11),
            'id_customer'       =>    array('type' => self::TYPE_INT,   'validate' => 'isInt',    'required' => true, 'size' => 11),
            'period'            =>    array('type' => self::TYPE_INT,   'validate' => 'isInt',    'required' => true, 'size' => 2),
            'last_reminded'     =>    array('type' => self::TYPE_DATE),
            'reminded'          =>    array('type' => self::TYPE_BOOL),
            'date'              =>    array('type' => self::TYPE_DATE),
        ),
    );
}