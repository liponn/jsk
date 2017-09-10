<?php
/**
 * Cms.php
 * Niushop商城系统 - 团队十年电商经验汇集巨献!
 * =========================================================
 * Copy right 2015-2025 山西牛酷信息科技有限公司, 保留所有权利。
 * ----------------------------------------------
 * 官方网址: http://www.niushop.com.cn
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用。
 * 任何企业和个人不允许对程序代码以任何形式任何目的再发布。
 * =========================================================
 * @author : niuteam
 * @date : 2015.1.17
 * @version : v1.0.0.0
 */
namespace data\extend\upgrade;

use think\Db;
use data\service\Upgrade as UpgradeService;
use data\service\Config;
use data\extend\database;
/**
 * 升级
 */
class Upgrade
{

    /**
     * 要处理的文件夹路径
     * @var unknown
     */
    protected  $deal_file_dir_array=array();
    /**
     * 要处理的文件路径
     * @var unknown
     */
    protected  $deal_file_array=array();
    /**
     * 补丁下载路径
     * @var unknown
     */
    protected  $download_url="";
    /**
     * 补丁编号
     * @var unknown
     */
    protected  $patch_release="";
    /**
     * 下载文件存放路径
     * @var unknown
     */
    protected  $download_file_path="";
    /**
     * 压缩包存放路径
     * @var unknown
     */
    protected  $download_zip_path="";
    
    /**
     * 压缩包解压路径
     * @var unknown
     */
    protected  $download_update_file="";
    
    /**
     * 文件备份路径
     * @var unknown
     */
    protected  $download_back_file="";
    /**
     * 检索文件的开始路径
     * @var unknown
     */
    protected  $file_start_path="";
    /**
     * 数据库的文件路径
     * @var unknown
     */
    protected  $sql_file_path="";
    
    /**
     * 升级结果编号
     * @var unknown
     */
    public $upgrade_code=0;
    /**
     * 升级结果的信息
     * @var unknown
     */
    public $upgrade_message="";
    /**
     * 版本号
     * @var unknown
     */
    public $version_no="";
    /**
    * 构造初始化
    */
    function __construct($patch_release, $user_name,$password){
        $version_no=0;
        $download_url="";
        $upgrade_service=new UpgradeService();
        $patch_result=$upgrade_service->getVersionPatchDetail($patch_release, $user_name, $password);
        if(!empty($patch_result) && $patch_result["code"]==0){
            $version_no=$patch_result["data"]["patch_no"];
            $download_url=$patch_result["data"]["patch_download_url"];
        }else{
            $this->upgrade_code=-1;
            $this->upgrade_message="获取补丁信息失败，升级失败!";
        }
        /**
         * 版本号
         */
        $this->version_no=$version_no;
        /**
         * 下载路径
         */
        $this->download_url=$download_url;
        /**
         * 补丁编号
         */
        $this->patch_release=$patch_release;
        /**
         * 升级的补丁文件存放路径
         */
        $this->download_file_path=ROOT_PATH.'download/upgrade/'.'niushop_patch_'.$this->patch_release.'/';
        if (! is_dir($this->download_file_path)) {
            if (! @mkdir($this->download_file_path, 0777, true)) {
                $this->upgrade_code=-1;
                $this->upgrade_message="安装临时目录创建失败，请确认download目录有写入权限！";
            }
        }
    }
    /**
     * 升级版本
     */
    public function niushop_patch_upgrade()
    {
        /**
         * 下载补丁包
         */
        $this->update_file_download();
        /**
         * 解压补丁包
         */
        $this->update_file_unzip();
        /**
         * 处理更新文件
         */
        $this->update_file_deal($this->file_start_path);
        /**
         * 更新文件备份
         */
        $this->update_file_backup();
        /**
         * 覆盖文件
         */
        $this->update_file_cover();
        if($this->upgrade_code!=0){
            //升级失败   代码恢复
            $this->update_file_regain();
        }
        $this->sql_file_path=$this->download_update_file."/niushop_".$this->version_no."_patch.sql";
        /**
         * 导入升级数据库数据
         */
        $this->update_sql_execute();
        
        return array(
          "code"=>$this->upgrade_code,
          "message"=>$this->upgrade_message  
        );
    }

    /**
     * 下载版本
     */
    public function update_file_download()
    {
        if($this->upgrade_code==0){
            try {
                $data = Http::doGet($this->download_url, 20);
                $fileName = explode('/', $this->download_url);
                $fileName = end($fileName);
                $this->download_zip_path = $this->download_file_path.$fileName;
                
                $update_name=str_replace(".zip", "", $fileName);
                $this->download_update_file=$this->download_file_path.$update_name;
                
                if (! @file_put_contents($this->download_zip_path, $data)) {
                    $this->upgrade_code=-1;
                    $this->upgrade_message="下载补丁包失败！下载路径：".$this->download_url;
                }
                
                $this->download_back_file=$this->download_update_file.'_backup/';
                if (! is_dir($this->download_back_file)) {
                    if (! @mkdir($this->download_back_file, 0777, true)) {
                        $this->upgrade_code=-1;
                        $this->upgrade_message="安装临时目录创建失败，请确认download目录有写入权限！";
                    }
                }
            } catch (\Exception $e) {
                $this->upgrade_code=-1;
                $this->upgrade_message="下载补丁包失败!";
            }
        }
    }
    /**
     * 解压版本
     */
    public function update_file_unzip()
    {
        if($this->upgrade_code==0){
            try {
//                 $zip = new Zip();
//                 if (! $zip->decompress($this->download_zip_path, $this->download_file_path)) {
//                     $this->upgrade_code=-1;
//                     $this->upgrade_message="补丁包解压失败!！";
//                 }
                $unzip=new Unzip();
                $result=$unzip->unzip($this->download_zip_path, $this->download_file_path);
                if(!$result){
                    $this->upgrade_code=-1;
                    $this->upgrade_message="补丁包解压失败!！";
                }
                /**
                 * 检索文件开始路径
                 */
                $this->file_start_path=$this->download_update_file."/niushop_b2c/";
            } catch (\Exception $e) {
                $this->upgrade_code=-1;
                $this->upgrade_message="补丁解压失败!";
            }
        }
    }
    /**
     * 检索需要更新的文件
     */
    private function update_file_deal($file_start_path)
    {
        if($this->upgrade_code==0){   
            try {
                if (is_dir($file_start_path)) {
                    if ($dh = opendir($file_start_path)) {
                        while (($file = readdir($dh)) !== false) {
                            if ((is_dir($file_start_path . "/" . $file)) && $file != "." && $file != "..") {
                                // 当前目录问文件夹
                                $this->deal_file_dir_array[]=$file;
                            } else {
                                if ($file != "." && $file != "..") {
                                    // 当前目录为文件
                                    $this->deal_file_array[]=$file;
                                }
                            }
                        }
                        closedir($dh);
                    }
                }
                if(count($this->deal_file_dir_array)>0){
                    $this->get_update_file($file_start_path);
                }
            } catch (\Exception $e) {
                $this->upgrade_code=-1;
                $this->upgrade_message="处理更新文件失败!";
            }
        }
    }
    /**
     * 得到需要更新的文件
     */
    private function get_update_file($file_start_path){
        while (count($this->deal_file_dir_array)>0){
            $length=count($this->deal_file_dir_array);
            for ($i=0;$i<$length; $i++){
                $dir_path=$this->deal_file_dir_array[$i];
                $deal_path=$file_start_path.'/'.$dir_path;
                if (is_dir($deal_path)) {
                    if ($dh = opendir($deal_path)) {
                        while (($file = readdir($dh)) !== false) {
                            if ((is_dir($deal_path . "/" . $file)) && $file != "." && $file != "..") {
                                // 当前目录问文件夹
                                $this->deal_file_dir_array[]=$dir_path."/".$file;
                            } else {
                                if ($file != "." && $file != "..") {
                                    // 当前目录为文件
                                    $this->deal_file_array[]=$dir_path."/".$file;
                                }
                            }
                        }
                        closedir($dh);
                    }
                }
                unset($this->deal_file_dir_array[$i]);
                $length=$length-1;
                $this->deal_file_dir_array=array_values($this->deal_file_dir_array);
            }
        }   
    }
    /**
     * 文件备份
     */
    public function update_file_backup(){
        if($this->upgrade_code==0){
            try {
                foreach ($this->deal_file_array as $file_path){
                    $this->create_backup_file($file_path);
                }
            } catch (\Exception $e) {
                $this->upgrade_code=-1;
                $this->upgrade_message="文件备份失败!";
            }
        }
    }
    /**
     * 创建文件备份的文件夹
     */
    public function create_backup_file($file_path){
        $file_path=str_replace("\\", "/", $file_path);
        $file_str=explode("/", $file_path);
        $from_path=ROOT_PATH.$file_path;
        $to_path="";
        $back_path=$this->download_back_file;
        if(count($file_str)>1){
            for ($i=0; $i<count($file_str); $i++){
                $middle_path=$file_str[$i];
                if($middle_path==end($file_str)){
                    $to_path=$back_path.$middle_path;
                }else{
                    $back_path=$back_path.$middle_path."/";
                    if (! is_dir($back_path)) {
                        @mkdir($back_path, 0777, true);
                    }
                }
            }
        }else{
            $to_path=$back_path.$file_path;
        }
        if (file_exists($from_path)){
            @copy($from_path,$to_path);
        }
    }

    /**
     * 文件覆盖
     */
    public function update_file_cover(){
        if($this->upgrade_code==0){
            try {
                foreach ($this->deal_file_array as $file_path){
                    $from_path=$this->file_start_path.$file_path;
                    $to_path=ROOT_PATH.$file_path;
                    if (file_exists($from_path)){
                        @copy($from_path, $to_path);
                    }
                }
            } catch (\Exception $e) {
                $this->upgrade_code=-1;
                $this->upgrade_message="文件覆盖失败!";
            }
        }
    }
    /**
     * 导入sql文件
     */
    private function update_sql_execute()
    {
        if($this->upgrade_code==0){
            try {
                $sqlfile = $this->sql_file_path;
                if (file_exists($sqlfile)){
                    $sql = file_get_contents($sqlfile);
                    $sql = str_replace("\r", "\n", $sql);
                    $sql = explode(";\n", $sql);
                    if ($sql) {
                        foreach ($sql as $v) {
                            Db::execute($v);
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->upgrade_code=-1;
                $this->upgrade_message="导入数据库数据失败!";
            }
        }
    }
    /**
     * 更新失败 还原文件
     */
    private function update_file_regain(){
        //得到要恢复的文件
        $this->update_file_deal($this->download_back_file);
        foreach ($this->deal_file_array as $file_path){
            $from_path=$this->download_back_file.$file_path;
            $to_path=ROOT_PATH.$file_path;
            if (file_exists($from_path)){
                @copy($from_path,$to_path);
            }
        }
    }
}