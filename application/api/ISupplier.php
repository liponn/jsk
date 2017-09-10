<?php
namespace data\api;

/**
 * 供货商接口
 */
interface ISupplier
{
    /**
     * 供货商列表
     * @param number $page_index
     * @param number $page_size
     * @param string $condition
     * @param string $order
     * @param string $field
     */
    function getSupplierList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = '');
    /**
     * 添加供货商
     * @param unknown $uid
     * @param unknown $supplier_name
     * @param unknown $desc
     */
    function addSupplier($uid, $supplier_name, $desc);
    /**
     * 修改供货商
     * @param unknown $supplier_id
     * @param unknown $supplier_name
     * @param unknown $desc
     */
    function updateSupplier($supplier_id, $supplier_name, $desc);
    /**
     * 删除供货商
     * @param unknown $supplier_id
     */
    function deleteSupplier($supplier_id);
    /**
     * 获取单条供货商详情
     * @param unknown $supplier_id
     */
    function getSupplierInfo($supplier_id);
}
