<?php
// +----------------------------------------------------------------------
// | test [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.zzstudio.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Byron Sampson <xiaobo.sun@gzzstudio.net>
// +----------------------------------------------------------------------
namespace addons\wxtemplatemsg\controller;
use addons\wxtemplatemsg\Wxtemplatemsg as baseWxtemplatemsg;
use data\extend\WchatOauth;

class Wxtemplatemsg extends baseWxtemplatemsg
{
     /**
      * 获取模板id
      */
     public function getTemplateId(){
         $condition['template_id'] = '';
         $list = \think\Db::table("$this->table")->where($condition)->select();
         if(!empty($list)){
             foreach ($list as $k => $v){
                 $template_id = $this->getTemplateIdByTemplateNo($v['template_no']);
                 if($template_id){
                     \think\Db::table("$this->table")->where('id', $v['id'])->update(['template_id' => $template_id]);
                 }
             }
         }
         return AjaxReturn(1);
     }
     /**
      * 设置模板消息是否启用
      */
     public function changeIsEnable(){
         $id = request()->post('id', 0);
         $is_enable = request()->post('is_enable', 0);
         $res = \think\Db::table("$this->table")->where(['id' => $id])->update(['is_enable' => $is_enable]);
         return AjaxReturn($res);
     }
     
     /**
      * 根据模板编号 获取 模板id
      */
     protected function getTemplateIdByTemplateNo($template_no){
         $wchat = new WchatOauth();
         $json = $wchat->templateID($template_no);
         $array = json_decode($json, true);
         $template_id = '';
         if($array){
             $template_id = $array['template_id'];
         }
         return $template_id;
     }
     
}