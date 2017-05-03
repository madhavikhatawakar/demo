<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Catalog
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Url Rewrite Category Refresh Changelog
 *
 * @category    Enterprise
 * @package     Enterprise_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Catalog_Model_Index_Action_Url_Rewrite_Category_Refresh_Changelog
    extends Enterprise_Catalog_Model_Index_Action_Url_Rewrite_Category_Refresh
{
    /**
     * The list of changed entity ids
     *
     * @var null|array
     */
    protected $_changedIds;

    /**
     * Refresh rows by ids based on changelog table
     * - clean category url rewrites
     * - refresh category url rewrites
     * - refresh category to url rewrite relations
     *
     * @return Enterprise_Mview_Model_Action_Interface
     * @throws Enterprise_Mview_Exception
     */
    public function execute()
    {
        $this->_validate();
        $this->_connection->beginTransaction();
        try {
            $this->_cleanOldUrlRewrite();
            $this->_refreshUrlRewrite();
            $this->_refreshRelation();

            $this->_metadata->setVersionId($this->_selectLastVersionId());
            $this->_metadata->save();
            $this->_connection->commit();
        } catch (Exception $e) {
            $this->_connection->rollBack();
            throw new Enterprise_Mview_Exception($e->getMessage(), $e->getCode());
        }
        return $this;
    }

    /**
     * Validate metadata before execute
     *
     * @return Enterprise_Catalog_Model_Index_Action_Url_Rewrite_Category_Refresh_Changelog
     * @throws Enterprise_Index_Exception
     */
    protected function _validate()
    {
        if (!$this->_metadata->getId() || !$this->_metadata->getChangelogName()) {
            throw new Enterprise_Index_Exception('Can\'t perform operation, incomplete metadata!');
        }
        return $this;
    }

    /**
     * Returns select query for deleting old url rewrites.
     *
     * @return Varien_Db_Select
     */
    protected function _getCleanOldUrlRewriteSelect()
    {
        $select = parent::_getCleanOldUrlRewriteSelect();
        $select->where('rc.category_id IN (?)', $this->_getChangedIds());
        return $select;
    }

    /**
     * Prepares url rewrite select query
     *
     * @return Varien_Db_Select
     */
    protected function _getUrlRewriteSelectSql()
    {
        $select = parent::_getUrlRewriteSelectSql();
        $select->where('uk.entity_id IN (?)', $this->_getChangedIds());
        return $select;
    }

    /**
     * Prepares refresh relation select query for given category_id
     *
     * @return Varien_Db_Select
     */
    protected function _getRefreshRelationSelectSql()
    {
        $select = parent::_getRefreshRelationSelectSql();
        $select->where('uk.entity_id IN (?)', $this->_getChangedIds());
        return $select;
    }

    /**
     * Returns list of changed Ids
     *
     * @return array
     */
    protected function _getChangedIds()
    {
        if (null === $this->_changedIds) {
            $select = $this->_connection->select()
                ->from($this->_metadata->getChangelogName(),
                    array($this->_metadata->getKeyColumn()))
                ->where('version_id >= ?', $this->_metadata->getVersionId());
            $this->_changedIds = $this->_connection->fetchCol($select);
        }
        return $this->_changedIds;
    }
}
