<?php
namespace data\service;

/**
 * 供货商服务层
 */
use data\api\ISupplier as ISupplier;
use data\model\NsSupplierModel;
use data\model\NsGoodsModel;
use data\model\NsGoodsDeletedModel;
use data\model\BaseModel;

class Supplier extends BaseService implements ISupplier
{
    /**
     * (non-PHPdoc)
     * @see \data\api\ISupplier::getSupplierList()
     */
    public function getSupplierList($page_index = 1, $page_size = 0, $condition = '', $order = '', $field = ''){
        $supplier = new NsSupplierModel();
        return $supplier->pageQuery($page_index, $page_size, $condition, $order, $field);
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\ISupplier::addSupplier()
     */
    public function addSupplier($uid, $supplier_name, $linkman_name, $linkman_tel, $linkman_address, $desc){
        $supplier = new NsSupplierModel();
        $data = array(
            'uid' => $uid,
            'supplier_name' => $supplier_name,
            'linkman_name' => $linkman_name,
            'linkman_tel' => $linkman_tel,
            'linkman_address' => $linkman_address,
            'desc' => $desc
        );
        $supplier->save($data);
        return $supplier->supplier_id;
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\ISupplier::updateSupplier()
     */
    public function updateSupplier($supplier_id, $supplier_name, $linkman_name, $linkman_tel, $linkman_address,  $desc){
        $supplier = new NsSupplierModel();
        $data = array(
            'uid' => $uid,
            'supplier_name' => $supplier_name,
            'linkman_name' => $linkman_name,
            'linkman_tel' => $linkman_tel,
            'linkman_address' => $linkman_address,
            'desc' => $desc
        );
        return $supplier->save($data, ['supplier_id' => $supplier_id]);
    }
    
    /**
     * (non-PHPdoc)
     * @see \data\api\ISupplier::deleteSupplier()
     */
    public function deleteSupplier($supplier_id_array){
        $supplier = new NsSupplierModel();
        if(strstr($supplier_id_array, ',')){
            $new_array = explode(',', $supplier_id_array);
            foreach ($new_array as $k => $v){
                if($this->checkSupplierIsUse($v) <= 0){
                    $res += $supplier->destroy($v);
                }else{
                    $res = -1;
                    break;
                }
            }
        }else{
            if($this->checkSupplierIsUse($supplier_id_array) <= 0){
                $res = $supplier->destroy($supplier_id_array);
            }else{
                $res = -1;
            }
        }
        return $res;
    }
    /**
     * 判断供货商是否使用过
     * @param unknown $supplier_id
     * return int  大于0 使用过   等于0 没使用过
     */
    protected function checkSupplierIsUse($supplier_id){
        $goods = new NsGoodsModel();
        $goods_deleted = new NsGoodsDeletedModel();
        $count = $goods->getCount(['supplier_id' => $supplier_id]);
        $count += $goods_deleted->getCount(['supplier_id' => $supplier_id]);
        return $count;
    }
    /**
     * (non-PHPdoc)
     * @see \data\api\ISupplier::getSupplierInfo()
     */
    public function getSupplierInfo($supplier_id){
        $supplier = new NsSupplierModel();
        return $supplier->get($supplier_id);
    }
}