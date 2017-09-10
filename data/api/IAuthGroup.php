<?php
namespace data\api;
/**
 * 系统用户组以及权限管理
 */
interface IAuthGroup
{
    /**
     * 获取系统用户组
     * @param unknown $where
     * @param unknown $order
     * @param unknown $page_size
     * @param unknown $page_index
     */
    function getSystemUserGroupList($page_index, $page_size=0, $order='', $where='' );
    
    /**
     * 添加系统用户组
     * @param unknown $group_id
     * @param unknown $group_name
     * @param unknown $is_system
     * @param unknown $module_id_array
     * @param unknown $desc
     */
    function addSystemUserGroup($group_id, $group_name, $is_system, $module_id_array, $desc);
    
    /**
     * 修改系统用户组
     * @param unknown $group_id
     * @param unknown $group_name
     * @param unknown $is_system
     * @param unknown $module_id_array
     * @param unknown $desc
     */
    function updateSystemUserGroup($group_id, $group_name, $group_status, $is_system, $module_id_array, $desc);
    
    /**
     * 修改用户组的状态
     * @param unknown $group_id
     * @param unknown $group_status
     */
    function ModifyUserGroupStatus($group_id, $group_status);
    /**
     * 删除用户组
     * @param unknown $group_id
     */
    function deleteSystemUserGroup($group_id);
    /**
     * 获取用户组详情
     * @param unknown $group_id
     */
    function getSystemUserGroupDetail($group_id);
    /**
     * 查询所有用户组
     */
    function getSystemUserGroupAll($where);
}