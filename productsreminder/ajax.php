<?php
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
$return = "";

function productIdByOrderProductId($orderProductId)
{
	$sql = "
		SELECT `product_id`
		FROM `"._DB_PREFIX_."order_detail`
		WHERE `id_order_detail` = '{$orderProductId}';
	";
	$result = Db::getInstance()->ExecuteS($sql);
	if($result)
	{
		return $result[0]['product_id'];
	}
	return null;
}

function recordExists($productId, $returnResult = false)
{
	$userId = (int)Context::getContext()->cookie->id_customer;
	$sql = "
		SELECT *
		FROM `"._DB_PREFIX_."product_reminder`
		WHERE `id_product` = '{$productId}'
		AND `id_customer` = '{$userId}';
	";
	$result = Db::getInstance()->ExecuteS($sql);
	if($result)
	{
		if($returnResult)
		{
			return $result;
		}
		return true;
	}
	return false;
}

function updateRecord($productId, $period)
{
	$userId = (int)Context::getContext()->cookie->id_customer;
	$sql = "
		UPDATE `"._DB_PREFIX_."product_reminder`
		SET `period` = '{$period}'
		WHERE `id_product` = '{$productId}'
		AND `id_customer` = '{$userId}';
	";
	$result = Db::getInstance()->ExecuteS($sql);
	if($result)
	{
		return true;
	}
	return false;
}

function insertRecord($productId, $period)
{
	$userId = (int)Context::getContext()->cookie->id_customer;
	$sql = "
		INSERT INTO `"._DB_PREFIX_."product_reminder`
		(`id_product_reminder`, `id_product`, `id_customer`, `period`, `last_reminded`, `reminded`, `date`)
		VALUES (NULL, '{$productId}', '{$userId}', '{$period}', NULL, NULL, NULL);
		UPDATE `"._DB_PREFIX_."product_reminder`
		SET `period` = '{$period}'
		WHERE `id_product` = '{$productId}'
		AND `id_customer` = '{$userId}';
	";
	$result = Db::getInstance()->ExecuteS($sql);
	if($result)
	{
		return true;
	}
	return false;
}

function deleteRecord($productId)
{
	$userId = (int)Context::getContext()->cookie->id_customer;
	$sql = "
		DELETE FROM `"._DB_PREFIX_."product_reminder`
		WHERE `id_product` = '{$productId}'
		AND `id_customer` = '{$userId}';
	";
	$result = Db::getInstance()->ExecuteS($sql);
	if($result)
	{
		return true;
	}
	return false;
}

$action = Tools::getValue('action');
if($action)
{
	switch($action)
	{
		case 'order-history-create-elem':
			$productId	= (int) productIdByOrderProductId(Tools::getValue('idProduct'));
			$record = recordExists($productId, true);
			$return .= "<td class=\"cart_total item\">";
			$return .= "<select data-id=\"".$productId."\" class=\"remindPeriod\" name=\"remindPeriod\">";
			$return .= "<option value=\"\">Select period</option>";
			$optionsArray = array(1, 3, 6, 12);
			foreach($optionsArray as $option)
			{
				$selected = isset($record[0]['period']) && $record[0]['period'] == $option ? 'selected="selected"':'';
				$return .= "<option ".$selected." value=\"".$option."\">after ".$option." month".($option !=1?'s':'')."</option>";
			}
		
			$return .= "</select>";
			$return .= "</td>";
			break;
		case 'cart-summary-create-elem':
			$productId	= (int) Tools::getValue('idProduct');
			$record = recordExists($productId, true);
			$return .= "<td class=\"cart_total item\">";
			$return .= "<select data-id=\"".$productId."\" class=\"remindPeriod\" name=\"remindPeriod\">";
			$return .= "<option value=\"\">Select period</option>";
			$optionsArray = array(1, 3, 6, 12);
			foreach($optionsArray as $option)
			{
				$selected = isset($record[0]['period']) && $record[0]['period'] == $option ? 'selected="selected"':'';
				$return .= "<option ".$selected." value=\"".$option."\">after ".$option." month".($option !=1?'s':'')."</option>";
			}
		
			$return .= "</select>";
			$return .= "</td>";
			break;
		case 'cart-summary-save':
			$productId	= (int) Tools::getValue('idProduct');
			$period		= (int) Tools::getValue('period');
			if(recordExists($productId))
			{
				if($period)
				{
					updateRecord($productId, $period);
					$return = 'update';
				}
				else
				{
					deleteRecord($productId);
					$return = 'delete';
				}
			}
			else
			{
				insertRecord($productId, $period);
				$return = 'insert';
			}
			break;
		default:
			// code is poetry
			break;
	}
}

echo Tools::jsonEncode( $return );
exit;