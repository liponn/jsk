<?php
namespace data\api;

/**
 * 升级接口
 */
interface IUpgrade
{
    function getVersionPatch();  
    /**
     * 获得当前域名的授权信息
     */  
    function getUserDevolution($user_name, $password);
    /**
     * 查询可以升级的补丁信息
     * @param unknown $patch_release
     * @param unknown $host_url
     * @param unknown $devolution_version
     * @param unknown $devolution_code
     */
    function getVersionPatchList($user_name, $password);
    
    /**
     * 版本补丁列表
     * (non-PHPdoc)
     */
    public function getProductPatchList($page_index = 1, $page_size = 0,  $condition = '', $order = '');
    
    /**
     * 查询补丁的具体信息
     * @param unknown $patch_release
     * @param unknown $devolution_version
     * @param unknown $devolution_code
     */
    function getVersionPatchDetail($patch_release, $user_name, $password);
    /**
     * 修改更新状态
     */
    public function updateVersionPatchState($patch_release);
    /**
     * 查询需要升级的所有数据
     */
    public function getUpgradePatchList();
    /**
     *  查询授权账户表是否有数据
     */
    public function getVersionDevolution();
    /**
     * 给授权账户表添加一条数据
     */
    public function addVersionDevolution($user_name, $password);

    /**
    *  判断当前用户是否需要升级
    */
    public function devolutionUpdate();
    /**
     * 是否加载版权
     */
    public function isLoadCopyRight();
    /**
     * 得到服务器的最新版本
     */
    function getLatestVersion();
}

