<?php
namespace app\admin\controller;

use data\service\Upgrade as UpgradeService;
use app\admin\controller\BaseController;
use data\extend\upgrade\Upgrade as UpgradeExtend;
use data\service\Config;
use data\extend\database;

/**
 * 升级
 * 
 * @author Administrator
 */
class Upgrade extends BaseController
{

    public $backup_code = 0;

    public $backup_message = "数据库备份成功!";

    public function __construct()
    {
        parent::__construct();
    }

    public function onlineUpdate()
    {
        $upgrade = new UpgradeService();
        $back = request()->get('back', '');
        // 查询是否有授权信息
        $devolution_message = $upgrade->getVersionDevolution();
        $user_name = "";
        $password = "";
        if (! empty($devolution_message) && count($devolution_message) > 0) {
            $user_name = $devolution_message[0]['devolution_username'];
            $password = $devolution_message[0]['devolution_password'];
            $result = $upgrade->getUserDevolution($user_name, $password);
            $res = json_decode($result);
            $devolution_info = json_decode(json_encode($res), true);
            $this->assign('devolution_user_name', $user_name);
            $this->assign('result', $devolution_info);
        }
        
        // 绑定账号
        if (request()->isAjax()) {
            $user_name = request()->post('user_name', '');
            $password = request()->post('password', '');
            $result = $upgrade->getUserDevolution($user_name, $password);
            $res = json_decode($result);
            if (! empty($res)) {
                $revel = json_decode(json_encode($res), true);
                if ($revel['code'] == 0) {
                    $upgrade->addVersionDevolution($user_name, $password);
                }
            } else {
                $revel = array(
                    "code" => - 1,
                    "message" => "用户未授权!"
                );
            }
            return $revel;
        }
        
        // 判断是否授权，授权跳转升级页面
        if (! empty($devolution_info) && $devolution_info['code'] == 0 && $back != - 1) {
            // $devolution_version=$devolution_info['data']['devolution_version'];
            // $devolution_code = $devolution_info['data']['devolution_code'];
            // $this->assign('devolution_code', $devolution_code);
            $upgrade->getVersionPatchList($user_name, $password);
            $this->assign('devolution_user_name', $user_name);
            $this->assign('devolution_password', $password);
            return view($this->style . "Upgrade/onlineUpdateList");
        }
        //服务器端版本
        $latestVersionRes = $upgrade->getLatestVersion();
        $latestVersion = json_decode($latestVersionRes,true);
        $this->assign("latestVersion",$latestVersion);
        return view($this->style . "Upgrade/onlineUpdate");
    }
    
    /**
     * 授权信息
     */
    public function devolutioninfo(){
        $upgrade = new UpgradeService();
        // 查询是否有授权信息
        $devolution_message = $upgrade->getVersionDevolution();
        $user_name = "";
        $password = "";
        if (! empty($devolution_message) && count($devolution_message) > 0) {
            $user_name = $devolution_message[0]['devolution_username'];
            $password = $devolution_message[0]['devolution_password'];
            $result = $upgrade->getUserDevolution($user_name, $password);
            $res = json_decode($result);
            $devolution_info = json_decode(json_encode($res), true);
            $this->assign('devolution_user_name', $user_name);
            $this->assign('result', $devolution_info);
        }
        $this->assign('devolution_user_name', $user_name);
        $this->assign('devolution_password', $password);
        //服务器端版本
        $latestVersionRes = $upgrade->getLatestVersion();
        $latestVersion = json_decode($latestVersionRes,true);
        $this->assign("latestVersion",$latestVersion);
        return view($this->style . "Upgrade/onlineUpdate");
    }
    
    /**
     * 更新列表页面
     */
    public function onlineUpdateList()
    {
        // 如果授权，进入更新页面
        $upgrade = new UpgradeService();
        
        if (request()->isAjax()) {
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = request()->post('search_text', '');
            $condition['is_up'] = $search_text;
            
            if ($search_text == "-1") {
                $revel = $upgrade->getProductPatchList($page_index, $page_size, '', $order = '');
            } else {
                $revel = $upgrade->getProductPatchList($page_index, $page_size, $condition, $order = '');
            }
            return $revel;
        }
        
        return view($this->style . "Upgrade/onlineUpdateList");
    }

    /**
     * 判断当前升级的版本的，补丁编号是否是最小，并且未升级
     * 
     * @return unknown
     */
    public function getProductPatch()
    {
        if (request()->isAjax()) {
            $upgrade = new UpgradeService();
            // $patch_id = request()->post('patch_id', '');
            $patch_type = request()->post('patch_type', '');
            $is_up = request()->post('is_up', '');
            $patch_release = request()->post('patch_release', '');
            
            $revel = $upgrade->getProductPatch($patch_type, $is_up, $patch_release);
            return $revel;
        }
    }

    /**
     * 升级补丁
     * 
     * @return unknown
     */
    public function upVersionPatch()
    {
        if (request()->isAjax()) {
            $upgrade_type = request()->post('upgrade_type', '');
            if ($upgrade_type == 1) {
                // 一个一个升级
                $patch_release = request()->post('patch_release', '');
                // $devolution_code = request()->post('devolution_code', '');
                // $devolution_version = request()->post('devolution_version', '');
                $user_name = request()->post('user_name', '');
                $password = request()->post('password', '');
                $upgrade = new UpgradeExtend($patch_release, $user_name, $password);
                $revel = $upgrade->niushop_patch_upgrade();
                if ($revel['code'] == 0) {
                    $upgrade = new UpgradeService();
                    $upgrade->updateVersionPatchState($patch_release);
                }
                return $revel;
            } else {
                // 一键升级
                // $devolution_code = request()->post('devolution_code', '');
                $user_name = request()->post('user_name', '');
                $password = request()->post('password', '');
                $upgrade = new UpgradeService();
                $patch_list = $upgrade->getUpgradePatchList();
                $revel = array(
                    "code" => 0,
                    "message" => "升级成功"
                );
                if (count($patch_list) > 0) {
                    foreach ($patch_list as $patch_obj) {
                        $upgrade = new UpgradeExtend($patch_obj["patch_release"], $user_name, $password);
                        $revel = $upgrade->niushop_patch_upgrade();
                        if ($revel['code'] == 0) {
                            $upgrade = new UpgradeService();
                            $upgrade->updateVersionPatchState($patch_obj["patch_release"]);
                        } else {
                            return $revel;
                        }
                    }
                    return $revel;
                } else {
                    $revel["message"] = "当前没有可升级的补丁!";
                    return $revel;
                }
            }
        }
    }

    /**
     * 数据库备份
     */
    public function database_backup()
    {
        try {
            $web_config = new Config();
            $database_list = $web_config->getDatabaseList();
            $name_array = array();
            foreach ($database_list as $database_obj) {
                $name_array[] = $database_obj["Name"];
            }
            $result = $this->exportDatabase($name_array, "", "");
            $is_end = 0;
            while ($is_end == 0) {
                if (is_array($result)) {
                    $result = $this->exportDatabase('', $result["tab"]["id"], $result["tab"]["start"]);
                } else {
                    $is_end = 1;
                }
            }
        } catch (\Exception $e) {
            $this->backup_code = - 1;
            $this->backup_message = "数据库备份失败!";
        }
        if ($this->backup_code == - 1) {
            unlink("download/database_backup/" . 'backup.lock');
            session('backup_tables', null);
            session('backup_file', null);
            session('backup_config', null);
        }
        
        return array(
            "code" => $this->backup_code,
            "message" => $this->backup_message
        );
    }

    /**
     * * 备份数据
     */
    public function exportDatabase($tables, $id, $start)
    {
        if (! empty($tables) && is_array($tables)) { // 初始化
                                                  // 读取备份配置
            $config = array(
                'path' => "download/database_backup/",
                'part' => 20971520,
                'compress' => 1,
                'level' => 9
            );
            // 检查是否有正在执行的任务
            $lock = "{$config['path']}backup.lock";
            if (is_file($lock)) {
                $this->backup_code = - 1;
                $this->backup_message = "检测到有一个备份任务正在执行，请稍后再试！";
                return;
            } else {
                $mode = intval('0777', 8);
                if (! file_exists($config['path']) || ! is_dir($config['path']))
                    mkdir($config['path'], $mode, true);
                    // 创建锁文件
                file_put_contents($lock, date('Ymd-His', time()));
            }
            // 自动创建备份文件夹
            // 检查备份目录是否可写
            is_writeable($config['path']) || exit('backup_not_exist_success');
            session('backup_config', $config);
            // 生成备份文件信息
            $config = \think\Config::get('database');
            $file = array(
                'name' => $config["database"] . "_" . date('Ymd_His', time())
            );
            
            session('backup_file', $file);
            
            // 缓存要备份的表
            session('backup_tables', $tables);
            
            // 创建备份文件
            include 'data/extend/database.class.php';
            
            $database = new database($file, $config);
            if (false !== $database->create()) {
                $tab = array(
                    'id' => 0,
                    'start' => 0
                );
                $data = array();
                $data['status'] = 1;
                $data['message'] = '初始化成功';
                $data['tables'] = $tables;
                $data['tab'] = $tab;
                return $data;
            } else {
                $this->backup_code = - 1;
                $this->backup_message = "backup_set_error";
                return;
            }
        } elseif (is_numeric($id) && is_numeric($start)) { // 备份数据
            $tables = session('backup_tables');
            // 备份指定表
            $database = new database(session('backup_file'), session('backup_config'));
            $start = $database->backup($tables[$id], $start);
            if (false === $start) { // 出错
                $this->backup_code = - 1;
                $this->backup_message = "备份失败!";
                return;
            } elseif (0 === $start) { // 下一表
                if (isset($tables[++ $id])) {
                    $tab = array(
                        'id' => $id,
                        'table' => $tables[$id],
                        'start' => 0
                    );
                    $data = array();
                    $data['rate'] = 100;
                    $data['status'] = 1;
                    $data['info'] = '备份完成！';
                    $data['tab'] = $tab;
                    return $data;
                } else { // 备份完成，清空缓存
                    unlink("download/database_backup/" . 'backup.lock');
                    session('backup_tables', null);
                    session('backup_file', null);
                    session('backup_config', null);
                    return;
                }
            } else {
                $tab = array(
                    'id' => $id,
                    'table' => $tables[$id],
                    'start' => $start[0]
                );
                $rate = floor(100 * ($start[0] / $start[1]));
                $data = array();
                $data['status'] = 1;
                $data['rate'] = $rate;
                $data['info'] = "正在备份...({$rate}%)";
                $data['tab'] = $tab;
                return $data;
            }
        } else { // 出错
            $this->backup_code = - 1;
            $this->backup_message = "备份失败!";
            return;
        }
    }

    /**
     * 判断是否需要更新
     */
    public function isNeedToUpgrade()
    {
        $upgrade = new UpgradeService();
        $res = $upgrade->devolutionUpdate();
        return json_decode($res, true);
    }
}
