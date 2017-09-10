<?php
namespace app\admin\controller;

/**
 * 用户权限控制器
 */
use data\service\AuthGroup as AuthGroup;

class Auth extends BaseController
{

    private $auth_group;

    public function __construct()
    {
        parent::__construct();
        $this->auth_group = new AuthGroup();
    }

    /**
     * 用户列表
     *
     * @return Ambigous <\think\response\View, \think\response\$this, \think\response\View>
     */
    public function userList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $search_text = isset($_POST['search_text']) ? $_POST['search_text'] : '';
            $condition = array(
                'sur.instance_id' => $this->instance_id,
                'sur.user_name|sua.uid' => array(
                    'like',
                    '%' . $search_text . '%'
                )
            );
            $user_list = $this->user->adminUserList($page_index, $page_size, $condition);
            return $user_list;
        } else {
            return view($this->style . 'Auth/userList');
        }
    }

    /**
     * 用户日志，Hidden
     *
     * @return unknown|\think\response\View
     */
    public function userLoglist()
    {
        if (request()->isAjax()) {
            $page_index = isset($_POST["pageIndex"]) ? $_POST["pageIndex"] : 1;
            $condition = "";
            $list = $this->user->getUserLogList($page_index, PAGESIZE, $condition, "create_time desc");
            return $list;
        } else {
            return view($this->style . "Auth/userLoglist");
        }
    }

    /**
     * 用户组列表
     */
    public function authGroupList()
    {
        if (request()->isAjax()) {
            $page_index = request()->post('page_index', 1);
            $page_size = request()->post('page_size', PAGESIZE);
            $list = $this->auth_group->getSystemUserGroupList($page_index, $page_size, [
                'instance_id' => $this->instance_id
            ]);
            return $list;
        } else {
            $permissionList = $this->website->getInstanceModuleQuery();
            $firstArray = array();
            $p = array();
            for ($i = 0; $i < count($permissionList); $i ++) {
                $per = $permissionList[$i];
                if ($per["pid"] == 0 && $per["module_name"] != null) {
                    $firstArray[] = $per;
                }
            }
            for ($i = 0; $i < count($firstArray); $i ++) {
                $first_per = $firstArray[$i];
                $secondArray = array();
                for ($y = 0; $y < count($permissionList); $y ++) {
                    $childPer = $permissionList[$y];
                    if ($childPer["pid"] == $first_per["module_id"]) {
                        $secondArray[] = $childPer;
                    }
                }
                $first_per['child'] = $secondArray;
                for ($j = 0; $j < count($secondArray); $j ++) {
                    $second_per = $secondArray[$j];
                    $threeArray = array();
                    for ($z = 0; $z < count($permissionList); $z ++) {
                        $three_per = $permissionList[$z];
                        if ($three_per["pid"] == $second_per["module_id"]) {
                            $threeArray[] = $three_per;
                        }
                    }
                    $second_per['child'] = $threeArray;
                }
                $p[] = $first_per;
                $first_per = array();
            }
            $this->assign("list", $p);
            return view($this->style . 'Auth/authGroupList');
        }
    }

    /**
     * 添加或者编辑用户组
     */
    public function addUserGroup()
    {
        $group_id = isset($_POST['roleId']) ? $_POST['roleId'] : 0;
        $module_id_array = $_POST['array'];
        $role_name = $_POST['roleName'];
        if ($group_id != 0) {
            $retval = $this->auth_group->updateSystemUserGroup($group_id, $role_name, 1, false, $module_id_array, '');
        } else {
            $retval = $this->auth_group->addSystemUserGroup(0, $role_name, false, $module_id_array, '');
        }
        return AjaxReturn($retval);
    }

    public function editUserGroup()
    {
        $group_id = isset($_POST['group_id']) ? $_POST['group_id'] : 0;
        return view($this->style . "Auth/addUserGroup");
    }

    /**
     * 添加 后台用户
     */
    public function addUser()
    {
        if (request()->isAjax()) {
            $admin_name = request()->post('admin_name', '');
            $group_id = request()->post('group_id', '');
            $user_password = request()->post('user_password', '123456');
            $desc = request()->post('desc', '');
            $retval = $this->user->addAdminUser($admin_name, $group_id, $user_password, $desc, $this->instance_id);
            return AjaxReturn($retval);
        } else {
            $condition["instance_id"] = $this->instance_id;
            $list = $this->auth_group->getSystemUserGroupAll($condition);
            $this->assign('auth_group', $list);
            return view($this->style . 'Auth/addUser');
        }
    }

    /**
     * 修改后台用户
     */
    public function editUser()
    {
        if (request()->isAjax()) {
            $uid = $_POST['uid'];
            $admin_name = $_POST['admin_name'];
            $group_id = $_POST['group_id'];
            $desc = isset($_POST['desc']) ? $_POST['desc'] : "";
            $retval = $this->user->editAdminUser($uid, $admin_name, $group_id, $desc);
            return AjaxReturn($retval);
        } else {
            $uid = isset($_GET['uid']) ? $_GET['uid'] : 0;
            if ($uid == 0) {
                $this->error("没有获取到用户信息");
            }
            $ua_info = $this->user->getAdminUserInfo("uid = " . $uid, $field = "*");
            $this->assign("ua_info", $ua_info);
            $condition["instance_id"] = $this->instance_id;
            $list = $this->auth_group->getSystemUserGroupAll($condition);
            $this->assign('auth_group', $list);
            return view($this->style . 'Auth/editUser');
        }
    }

    /**
     * 删除系统用户组
     */
    public function deleteSystemUserGroup()
    {
        $group_id = isset($_POST['group_id']) ? $_POST['group_id'] : '';
        $retval = $this->auth_group->deleteSystemUserGroup($group_id);
        return AjaxReturn($retval);
    }

    /**
     * 用户的 锁定
     */
    public function userLock()
    {
        $uid = isset($_POST['uid']) ? $_POST['uid'] : 0;
        if ($uid > 0) {
            $res = $this->user->userLock($uid);
        }
        return AjaxReturn($res);
    }

    /**
     * 解锁
     */
    public function userUnlock()
    {
        $uid = isset($_POST['uid']) ? $_POST['uid'] : 0;
        if ($uid > 0) {
            $res = $this->user->userUnlock($uid);
        }
        return AjaxReturn($res);
    }

    /**
     * 重置密码
     *
     * @return unknown[]
     */
    public function resetUserPassword()
    {
        $uid = isset($_POST['uid']) ? $_POST['uid'] : 0;
        if ($uid > 0) {
            $res = $this->user->resetUserPassword($uid);
        }
        return AjaxReturn($res);
    }

    /**
     * 个人资料页面
     */
    public function userDetail()
    {
        if (request()->isAjax()) {
            $user_headimg = isset($_POST['user_headimg']) ? $_POST['user_headimg'] : '';
            $user_qq = isset($_POST['user_qq']) ? $_POST['user_qq'] : '';
            $res = $this->user->ModifyUserHeadimg($this->uid, $user_headimg);
            $res = $this->user->modifyQQ($this->uid, $user_qq);
            return AjaxReturn(1);
        }
        $_SESSION['bund_pre_url'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $info = $this->user->getUserDetail();
        $this->assign('info', $info);
        return view($this->style . "Auth/userDetail");
    }

    /**
     * 修改会员 用户名
     *
     * @return unknown[]
     */
    public function modifyUserName()
    {
        $user_name = isset($_POST['user_name']) ? $_POST['user_name'] : '';
        $res = $this->user->ModifyUserName($this->uid, $user_name);
        return AjaxReturn($res);
    }

    /**
     * 会员手机号绑定
     */
    public function userTelBind()
    {
        $res = $this->user->userTelBind($this->uid);
        return AjaxReturn($res);
    }

    /**
     * 解除 会员手机号 绑定
     */
    public function removeUserTelBind()
    {
        $res = $this->user->removeUserTelBind($this->uid);
        return AjaxReturn($res);
    }

    /**
     * 修改 会员 手机号
     */
    public function modifyUserTel()
    {
        $user_tel = isset($_POST['user_tel']) ? $_POST['user_tel'] : '';
        $res = $this->user->modifyMobile($this->uid, $user_tel);
        return AjaxReturn($res);
    }

    /**
     * 会员邮箱 绑定
     */
    public function userEmailBind()
    {
        $res = $this->user->userEmailBind($this->uid);
        return AjaxReturn($res);
    }

    /**
     * 解除 会员邮箱 绑定
     */
    public function removeUserEmailBind()
    {
        $res = $this->user->removeUserEmailBind($this->uid);
        return AjaxReturn($res);
    }

    /**
     * 修改 会员 邮箱
     */
    public function modifyUserEmail()
    {
        $user_email = isset($_POST['user_email']) ? $_POST['user_email'] : '';
        $res = $this->user->modifyEmail($this->uid, $user_email);
        return AjaxReturn($res);
    }

    /**
     * 解除 会员 qq 绑定
     *
     * @return unknown
     */
    public function removeUserQQBind()
    {
        $res = $this->user->removeBindQQ();
        return AjaxReturn($res);
    }

    /**
     * 删除 后台会员
     */
    public function deleteAdminUserAjax()
    {
        if (request()->isAjax()) {
            $uid = request()->post("uid", "");
            $res = $this->user->deleteAdminUser($uid);
            return AjaxReturn($res);
        }
    }
}