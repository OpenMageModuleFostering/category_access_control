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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   ZetaPrints
 * @package    ZetaPrints_AccessControl
 * @copyright  Copyright (c) 2010 ZetaPrints Ltd. http://www.zetaprints.com/
 * @attribution Vinai Kopp http://www.magentocommerce.com/extension/reviews/module/635
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adjust the cache for the catalog navigation to be cached
 * depending on the customer group
 *
 * @category   ZetaPrints
 * @package    ZetaPrints_AccessControl
 * @author     Anatoly A. Kazantsev <anatoly.kazantsev@gmail.com>
 */
class ZetaPrints_AccessControl_Block_Navigation
  extends Mage_Catalog_Block_Navigation {

  /**
   * Set the module translation namespace
   */
  public function _construct() {
    $this->setData('module_name', 'Mage_Catalog');
  }

  /**
   * Set this so the navigation is cached depending on the customer group.
   * Otherwise, the cached navigation could shown a state not matching
   * the current customer group
   *
   * @return string
   */
  public function getCacheKey () {
    return parent::getCacheKey()
      . Mage::helper('accesscontrol')->get_current_customer_group();
  }
}

?>
