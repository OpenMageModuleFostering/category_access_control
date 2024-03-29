<?php
/**
 * AccessControl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   ZetaPrints
 * @package    ZetaPrints_AccessControl
 * @copyright  Copyright (c) 2010 ZetaPrints Ltd. http://www.zetaprints.com/
 * @attribution Vinai Kopp http://www.magentocommerce.com/extension/reviews/module/635
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Used for the "global" settings in the extension configuration
 *
 * Also is the basis of the other config source classes, as it implements
 * the required abstract classes.
 *
 * @category   ZetaPrints
 * @package    ZetaPrints_AccessControl
 * @author     Anatoly A. Kazantsev <anatoly.kazantsev@gmail.com>
 */
class ZetaPrints_AccessControl_Model_Config_Source_Customergroups
  extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

  protected $_options;

  public function toOptionArray () {
    if (!$this->_options) {
      $this->_options = Mage::getResourceModel('customer/group_collection')
        /* ->setRealGroupsFilter() */
        ->loadData()
        ->toOptionArray();

      array_shift($this->_options);
      array_unshift($this->_options,
        array('value'=> ZetaPrints_AccessControl_Helper_Data::NONE,
              'label'=> Mage::helper('accesscontrol')->__('NONE')),
        array('value'=> ZetaPrints_AccessControl_Helper_Data::ALL,
              'label'=> Mage::helper('accesscontrol')->__('ALL')),
        array('value'=> ZetaPrints_AccessControl_Helper_Data::REGISTERED,
              'label'=> Mage::helper('accesscontrol')->__('REGISTERED')) );
    }

    return $this->_options;
  }

  public function getAllOptions () {
    return $this->toOptionArray();
  }

  public function getDefaultValue () {
    return ZetaPrints_AccessControl_Helper_Data::USE_DEFAULT;
  }

  /**
   * Return the database column definition to use for this attribute
   *
   * @return array
   */
  public function getFlatColums () {
    $size = $this->getAttribute()->getBackend()->getDbFieldLength();

    return array($this->getAttribute()->getAttributeCode() =>
      array(
        'type'      => sprintf('varchar(%d)', $size),
        'unsigned'  => false,
        'is_null'   => true,
        'default'   => $this->getDefaultValue(),
        'extra'     => null) );
  }

  /**
   * Because getFlatColums is spelled wrong and might get fixed one day
   *
   * @return array
   */
  public function getFlatColumns () {
    return $this->getFlatColums();
  }

  /**
   * Retrieve Select For Flat Attribute update
   *
   * @param int $store
   * @return Varien_Db_Select|null
   */
  public function getFlatUpdateSelect ($store) {
    $attribute = $this->getAttribute();
    $joinCondition = "`e`.`entity_id`=`t1`.`entity_id`";

    if ($attribute->getFlatAddChildData())
      $joinCondition .= " AND `e`.`child_id`=`t1`.`entity_id`";

    $select = $attribute->getResource()->getReadConnection()->select()
      ->joinLeft(array('t1' => $attribute->getBackend()->getTable()),
        $joinCondition,
        array() )
      ->joinLeft(array('t2' => $attribute->getBackend()->getTable()),
        "t2.entity_id = t1.entity_id"
        . " AND t1.entity_type_id = t2.entity_type_id"
        . " AND t1.attribute_id = t2.attribute_id"
        . " AND t2.store_id = {$store}",
        array($attribute->getAttributeCode() => "IFNULL(t2.value, t1.value)"))
      ->where("t1.entity_type_id=?", $attribute->getEntityTypeId())
      ->where("t1.attribute_id=?", $attribute->getId())
      ->where("t1.store_id=?", 0);

    if ($attribute->getFlatAddChildData())
      $select->where("e.is_child=?", 0);

    return $select;
  }
}

?>
