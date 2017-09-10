<?php
namespace data\service;

/**
 * 物流
 */
use data\service\BaseService as BaseService;
use data\api\IExpress as IExpress;
use data\model\NsOrderShippingFeeModel as NsOrderShippingFeeModel;
use data\model\NsOrderShippingFeeExtendModel as NsOrderShippingFeeExtendModel;
use data\service\Address as Address;
use data\model\NsOrderExpressCompanyModel;
use data\model\NsShopExpressAddressModel;
use data\model\NsExpressShippingItemsLibraryModel;
use data\model\NsExpressShippingModel;
use data\model\NsExpressShippingItemsModel;

class Express extends BaseService implements IExpress
{

    /*
     * (non-PHPdoc)
     * @see \data\api\IExpress::getShippingFeeList()
     */
    public function getShippingFeeList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $ns_order_shipping_fee = new NsOrderShippingFeeModel();
        $list = $ns_order_shipping_fee->pageQuery($page_index, $page_size, $condition, $order, '*');
        $address = new Address();
        foreach ($list['data'] as $k => $v) {
            $address = new Address();
            $list['data'][$k]['address_list'] = $address->getAddressListById($v['province_id_array'], $v['city_id_array']);
        }
        return $list;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IExpress::addShippingFee()
     */
    public function addShippingFee($co_id, $is_default, $shipping_fee_name, $province_id_array, $city_id_array,$district_id_array, $weight_is_use, $weight_snum, $weight_sprice, $weight_xnum, $weight_xprice, $volume_is_use, $volume_snum, $volume_sprice, $volume_xnum, $volume_xprice, $bynum_is_use, $bynum_snum, $bynum_sprice, $bynum_xnum, $bynum_xprice)
    {
        $order_shipping_fee = new NsOrderShippingFeeModel();
        $order_shipping_fee->startTrans();
        try {
            $data = array(
                'shipping_fee_name' => $shipping_fee_name,
                'co_id' => $co_id,
                'is_default' => $is_default,
                'province_id_array' => $province_id_array,
                'city_id_array' => $city_id_array,
                'district_id_array' => $district_id_array,
                'shop_id' => $this->instance_id,
                'weight_is_use' => $weight_is_use,
                'weight_snum' => $weight_snum,
                'weight_xnum' => $weight_xnum,
                'weight_sprice' => $weight_sprice,
                'weight_xprice' => $weight_xprice,
                'volume_is_use' => $volume_is_use,
                'volume_snum' => $volume_snum,
                'volume_sprice' => $volume_sprice,
                'volume_xnum' => $volume_xnum,
                'volume_xprice' => $volume_xprice,
                'bynum_is_use' => $bynum_is_use,
                'bynum_snum' => $bynum_snum,
                'bynum_sprice' => $bynum_sprice,
                'bynum_xnum' => $bynum_xnum,
                'bynum_xprice' => $bynum_xprice,
                'create_time' => time()
            );
            $order_shipping_fee->save($data);
            $order_shipping_fee->commit();
            return 1;
        } catch (\Exception $e) {
            $order_shipping_fee->rollback();
            return $e->getMessage();
        }
        return - 1;
        
        // TODO Auto-generated method stub
    }

    /**
     * 修改运费模板
     * 创建时间：2017年8月12日 09:26:23
     * {@inheritDoc}
     * @see \data\api\IExpress::updateShippingFee()
     */
    public function updateShippingFee($shipping_fee_id, $is_default, $shipping_fee_name, $province_id_array, $city_id_array,$district_id_array, $weight_is_use, $weight_snum, $weight_sprice, $weight_xnum, $weight_xprice, $volume_is_use, $volume_snum, $volume_sprice, $volume_xnum, $volume_xprice, $bynum_is_use, $bynum_snum, $bynum_sprice, $bynum_xnum, $bynum_xprice)
    {
        $order_shipping_fee = new NsOrderShippingFeeModel();
        $order_shipping_fee_ext = new NsOrderShippingFeeExtendModel();
        $order_shipping_fee->startTrans();
        try {
            $data = array(
                'shipping_fee_name' => $shipping_fee_name,
                'is_default' => $is_default,
                'province_id_array' => $province_id_array,
                'city_id_array' => $city_id_array,
                'district_id_array' => $district_id_array,
                'shop_id' => $this->instance_id,
                'weight_is_use' => $weight_is_use,
                'weight_snum' => $weight_snum,
                'weight_xnum' => $weight_xnum,
                'weight_sprice' => $weight_sprice,
                'weight_xprice' => $weight_xprice,
                'volume_is_use' => $volume_is_use,
                'volume_snum' => $volume_snum,
                'volume_sprice' => $volume_sprice,
                'volume_xnum' => $volume_xnum,
                'volume_xprice' => $volume_xprice,
                'bynum_is_use' => $bynum_is_use,
                'bynum_snum' => $bynum_snum,
                'bynum_sprice' => $bynum_sprice,
                'bynum_xnum' => $bynum_xnum,
                'bynum_xprice' => $bynum_xprice,
                'update_time' => time()
            );
            $order_shipping_fee->save($data, [
                'shipping_fee_id' => $shipping_fee_id
            ]);
            $order_shipping_fee->commit();
            return 1;
        } catch (\Exception $e) {
            $order_shipping_fee->rollback();
            return $e->getMessage();
        }
        return - 1;
        // TODO Auto-generated method stub
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IExpress::shippingFeeDetail()
     */
    public function shippingFeeDetail($shipping_fee_id)
    {
        $order_shipping_fee = new NsOrderShippingFeeModel();
        $order_shipping_fee_info = $order_shipping_fee->get($shipping_fee_id);
        $address = new Address();
        $province = $address->getProvinceList();
        $city = $address->getCityList();
        $address_name = "";
        $province_array = explode(",", $order_shipping_fee_info["province_id_array"]);
        $city_array = explode(",", $order_shipping_fee_info["city_id_array"]);
        foreach ($province_array as $e) {
            foreach ($province as $p) {
                if ($e == $p["province_id"]) {
                    $address_name = $address_name . $p["province_name"] . ",";
                }
            }
        }
        foreach ($city_array as $c) {
            foreach ($city as $z) {
                if ($c == $z["city_id"]) {
                    // $address_name = $address_name . $z["city_name"] . ",";
                }
            }
        }
        $address_name = substr($address_name, 0, strlen($address_name) - 1);
        $order_shipping_fee_info["address_name"] = $address_name;
        return $order_shipping_fee_info;
    }

    /*
     * (non-PHPdoc)
     * @see \data\api\IExpress::shippingFeeDelete()
     */
    public function shippingFeeDelete($shipping_fee_id)
    {
        $order_shipping_fee = new NsOrderShippingFeeModel();
        $condition = array(
            'shop_id' => $this->instance_id,
            'shipping_fee_id' => array(
                array(
                    "in",
                    $shipping_fee_id
                )
            )
        );
        $order_shipping_return = $order_shipping_fee->destroy($condition);
        if ($order_shipping_return > 0) {
            return 1;
        } else {
            return - 1;
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IExpress::shippingFeeQuery()
     */
    public function shippingFeeQuery($where = "", $fields = "*")
    {
        $order_shipping_fee = new NsOrderShippingFeeModel();
        return $order_shipping_fee->getQuery($where, $fields, '');
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IExpress::getExpressCompanyList()
     */
    public function getExpressCompanyList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $ns_express_company = new NsOrderExpressCompanyModel();
        $list = $ns_express_company->pageQuery($page_index, $page_size, $condition, $order, '*');
        return $list;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IExpress::addExpressCompany()
     */
    public function addExpressCompany($shop_id, $company_name, $express_logo, $express_no, $is_enabled, $image, $phone, $orders, $is_default)
    {
        $ns_express_company = new NsOrderExpressCompanyModel();
        $ns_express_company->startTrans();
        try {
            if ($is_default == 1) {
                $this->defaultExpressCompany();
            }
            $data = array(
                'shop_id' => $shop_id,
                'company_name' => $company_name,
                'express_logo' => $express_logo,
                'express_no' => $express_no,
                'is_enabled' => $is_enabled,
                'image' => $image,
                'phone' => $phone,
                'orders' => $orders,
                'is_default' => $is_default
            );
            $ns_express_company->save($data);
            $co_id = $ns_express_company->co_id;
            $sid = $this->addExpressShipping($shop_id, $company_name, $co_id);
            $this->addExpressShippingItems($sid, $shop_id);
            $ns_express_company->commit();
            return $ns_express_company->co_id;
        } catch (\Exception $e) {
            $ns_express_company->rollback();
            return $e->getCode();
        }
    }

    /**
     * 把别的改为未默认,把当前设置为默认
     */
    public function defaultExpressCompany()
    {
        $ns_express_company = new NsOrderExpressCompanyModel();
        $data = array(
            'is_default' => 0
        );
        $ns_express_company->save($data, [
            'shop_id' => $this->instance_id
        ]);
    }

    /**
     * 添加物流的模板库
     *
     * @param unknown $shop_id            
     * @param unknown $company_name            
     * @param unknown $co_id            
     */
    private function addExpressShipping($shop_id, $company_name, $co_id)
    {
        $express_model = new NsExpressShippingModel();
        $data = array(
            "shop_id" => $shop_id,
            "template_name" => $company_name,
            "co_id" => $co_id,
            "size_type" => 1,
            "width" => 0,
            "height" => 0,
            "image" => ""
        );
        $express_model->save($data);
        return $express_model->sid;
    }

    /**
     * 添加大印项
     *
     * @param unknown $sid            
     * @param unknown $shop_id            
     */
    private function addExpressShippingItems($sid, $shop_id)
    {
        $library_model = new NsExpressShippingItemsLibraryModel();
        $library_list = $library_model->getQuery([
            "shop_id" => $shop_id,
            "is_enabled" => 1
        ], "*", "");
        $x_length = 10;
        $y_length = 11;
        foreach ($library_list as $library_obj) {
            $item_model = new NsExpressShippingItemsModel();
            $data = array(
                "sid" => $sid,
                "field_name" => $library_obj["field_name"],
                "field_display_name" => $library_obj["field_display_name"],
                "is_print" => 1,
                "x" => $x_length,
                "y" => $y_length
            );
            $y_length = $y_length + 25;
            $item_model->save($data);
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IExpress::updateExpressCompany()
     */
    public function updateExpressCompany($co_id, $shopId, $company_name, $express_logo, $express_no, $is_enabled, $image, $phone, $orders, $is_default)
    {
        $ns_express_company = new NsOrderExpressCompanyModel();
        if ($is_default == 1) {
            $this->defaultExpressCompany();
        }
        $data = array(
            'shop_id' => $shopId,
            'company_name' => $company_name,
            'express_logo' => $express_logo,
            'express_no' => $express_no,
            'is_enabled' => $is_enabled,
            'image' => $image,
            'phone' => $phone,
            'orders' => $orders,
            'is_default' => $is_default
        );
        $res = $ns_express_company->save($data, [
            'co_id' => $co_id
        ]);
        return $res;
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IExpress::expressCompanyDetail()
     */
    public function expressCompanyDetail($co_id)
    {
        $ns_express_company = new NsOrderExpressCompanyModel();
        return $ns_express_company->get($co_id);
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IExpress::expressCompanyDelete()
     */
    public function expressCompanyDelete($co_id)
    {
        $ns_express_company = new NsOrderExpressCompanyModel();
        $conditon = array(
            'shop_id' => $this->instance_id,
            'co_id' => array(
                in,
                $co_id
            )
        );
        $ns_express_company_return = $ns_express_company->destroy($conditon);
        if ($ns_express_company_return > 0) {
            return 1;
        } else {
            return - 1;
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \data\api\IExpress::expressCompanyQuery()
     */
    public function expressCompanyQuery($where = "", $field = "*")
    {
        $ns_express_company = new NsOrderExpressCompanyModel();
        return $ns_express_company->where($where)
            ->field($field)
            ->select();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IExpress::addShopExpressAddress()
     */
    public function addShopExpressAddress($contact, $mobile, $phone, $company_name, $province, $city, $district, $zipcode, $address)
    {
        $shop_express_address = new NsShopExpressAddressModel();
        $data_consigner = array(
            'is_consigner' => 0,
            'is_receiver' => 0
        );
        $shop_express_address->save($data_consigner, [
            'shop_id' => $this->instance_id
        ]);
        $shop_express_address = new NsShopExpressAddressModel();
        $data = array(
            'shop_id' => $this->instance_id,
            'contact' => $contact,
            'mobile' => $mobile,
            'phone' => $phone,
            'company_name' => $company_name,
            'province' => $province,
            'city' => $city,
            'district' => $district,
            'zipcode' => $zipcode,
            'address' => $address,
            'is_consigner' => 1,
            'is_receiver' => 1,
            'create_date' => time()
        );
        $retval = $shop_express_address->save($data);
        $express_address_id = $shop_express_address->express_address_id;
        return $express_address_id;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IExpress::updateShopExpressAddress()
     */
    public function updateShopExpressAddress($express_address_id, $contact, $mobile, $phone, $company_name, $province, $city, $district, $zipcode, $address)
    {
        $shop_express_address = new NsShopExpressAddressModel();
        $data = array(
            'contact' => $contact,
            'mobile' => $mobile,
            'phone' => $phone,
            'company_name' => $company_name,
            'province' => $province,
            'city' => $city,
            'district' => $district,
            'zipcode' => $zipcode,
            'address' => $address,
            'modify_date' => time()
        );
        $retval = $shop_express_address->save($data, [
            'express_address_id' => $express_address_id
        ]);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IExpress::modifyShopExpressAddressConsigner()
     */
    public function modifyShopExpressAddressConsigner($express_address_id, $is_consigner)
    {
        $shop_express_address = new NsShopExpressAddressModel();
        $shop_express_address->save([
            'is_consigner' => 0
        ], [
            'shop_id' => $this->instance_id
        ]);
        $retval = $shop_express_address->save([
            'is_consigner' => 1
        ], [
            'express_address_id' => $express_address_id
        ]);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IExpress::modifyShopExpressAddressReceiver()
     */
    public function modifyShopExpressAddressReceiver($express_address_id, $is_receiver)
    {
        $shop_express_address = new NsShopExpressAddressModel();
        $shop_express_address->save([
            'is_receiver' => 0
        ], [
            'shop_id' => $this->instance_id
        ]);
        $retval = $shop_express_address->save([
            'is_receiver' => 1
        ], [
            'express_address_id' => $express_address_id
        ]);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IExpress::getShopExpressAddressList()
     */
    public function getShopExpressAddressList($page_index = 1, $page_size = 0, $condition = '', $order = '')
    {
        $shop_express_address = new NsShopExpressAddressModel();
        $list = $shop_express_address->pageQuery($page_index, $page_size, $condition, $order, '*');
        if (! empty($list['data'])) {
            $address = new Address();
            foreach ($list['data'] as $k => $v) {
                
                $address_info = $address->getAddress($v['province'], $v['city'], $v['district']);
                $list['data'][$k]['address_info'] = $address_info;
            }
        }
        return $list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IExpress::getDefaultShopExpressAddress()
     */
    public function getDefaultShopExpressAddress($shop_id)
    {
        $shop_express_address = new NsShopExpressAddressModel();
        $data = $shop_express_address->getInfo([
            'shop_id' => $shop_id,
            'is_receiver' => 1
        ], '*');
        if (! empty($data)) {
            $address = new Address();
            $address_info = $address->getAddress($data['province'], $data['city'], $data['district']);
            $data['address_info'] = $address_info;
        }
        return $data;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IExpress::deleteShopExpressAddress()
     */
    public function deleteShopExpressAddress($express_address_id_array)
    {
        $shop_express_address = new NsShopExpressAddressModel();
        $shop_id = $this->instance_id;
        $condition = array(
            'shop_id' => $shop_id,
            'express_address_id' => $express_address_id_array
        );
        $retval = $shop_express_address->destroy($condition);
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IExpress::selectShopExpressAddressInfo()
     */
    public function selectShopExpressAddressInfo($express_address_id)
    {
        $shop_express_address = new NsShopExpressAddressModel();
        $retval = $shop_express_address->getInfo([
            'express_address_id' => $express_address_id
        ], '*');
        return $retval;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IExpress::getExpressShippingItemsLibrary()
     */
    public function getExpressShippingItemsLibrary($shop_id)
    {
        $express_model = new NsExpressShippingItemsLibraryModel();
        $item_list = $express_model->getQuery([
            "shop_id" => $shop_id
        ], "*", "");
        return $item_list;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IExpress::getExpressShipping()
     */
    public function getExpressShipping($co_id)
    {
        $express_model = new NsExpressShippingModel();
        $express_obj = $express_model->getInfo([
            "co_id" => $co_id
        ], "*");
        return $express_obj;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \data\api\IExpress::getExpressShippingItems()
     */
    public function getExpressShippingItems($sid)
    {
        $express_model = new NsExpressShippingItemsModel();
        $item_list = $express_model->getQuery([
            "sid" => $sid
        ], "*", "");
        return $item_list;
    }

    /**
     * 修改物流模板的打印项的位置
     * (non-PHPdoc)
     *
     * @see \data\api\IExpress::updateExpressShippingItem()
     */
    public function updateExpressShippingItem($sid, $itemsArray)
    {
        $items_str = explode(";", $itemsArray);
        foreach ($items_str as $item_obj) {
            $item_obj_str = explode(",", $item_obj);
            $data = array(
                "field_display_name" => $item_obj_str[1],
                "is_print" => $item_obj_str[2],
                "x" => $item_obj_str[3],
                "y" => $item_obj_str[4]
            );
            $field_name = $item_obj_str[0];
            $express_item_model = new NsExpressShippingItemsModel();
            $express_item_model->save($data, [
                "sid" => $sid,
                "field_name" => $field_name
            ]);
        }
    }

    /**
     * 更新物流模板的信息, 以及打印的信息
     *
     * @param unknown $template_id            
     * @param unknown $width            
     * @param unknown $height            
     * @param unknown $imgUrl            
     */
    public function updateExpressShipping($template_id, $width, $height, $imgUrl, $itemsArray)
    {
        $express_model = new NsExpressShippingModel();
        $express_model->startTrans();
        try {
            $data = array(
                "width" => $width,
                "height" => $height,
                "image" => $imgUrl
            );
            $result = $express_model->save($data, [
                "sid" => $template_id
            ]);
            $this->updateExpressShippingItem($template_id, $itemsArray);
            $express_model->commit();
            return 1;
        } catch (\Exception $e) {
            $express_model->rollback();
            return $e->getCode();
        }
    }

    /**
     *
     * 根据物流公司id查询是否有默认地区
     *
     * {@inheritdoc}
     *
     * @see \data\api\IExpress::isHasExpressCompanyDefaultTemplate()
     */
    public function isHasExpressCompanyDefaultTemplate($co_id)
    {
        $ns_order_shipping_fee = new NsOrderShippingFeeModel();
        $list = $ns_order_shipping_fee->getQuery([
            'co_id' => $co_id
        ], 'is_default', '');
        $is_default = 1; // 是否有默认地区 1,可以添加默认地区：0，不可以添加默认地区
        foreach ($list as $v) {
            if ($v['is_default']) {
                $is_default = 0;
                break;
            }
        }
        return $is_default;
    }

    /**
     *
     * 获取物流公司的省市id组，排除默认地区、以及当前编辑的运费模板省市id组
     * 创建时间：2017年8月11日 16:27:53
     * 修改时间：
     * 王永杰
     *
     * {@inheritdoc}
     *
     * @see \data\api\IExpress::getExpressCompanyProvincesAndCitiesById()
     */
    public function getExpressCompanyProvincesAndCitiesById($co_id, $current_province_id_array, $current_city_id_array, $current_district_id_array)
    {
        $curr_province_id_array = []; // 省id组
        $curr_city_id_array = []; // 市id组
        $curr_district_id_array = []; // 区县id组
                                      
        // 编辑运费模板时的省id组排除
        if (! empty($current_province_id_array)) {
            if (! strstr($current_province_id_array, ',')) {
                array_push($curr_province_id_array, $current_province_id_array);
            } else {
                $curr_province_id_array = explode(',', $current_province_id_array);
            }
        }
        
        // 编辑运费模板时的市id组排除
        if (! empty($current_city_id_array)) {
            if (! strstr($current_city_id_array, ',')) {
                array_push($curr_city_id_array, $current_city_id_array);
            } else {
                $curr_city_id_array = explode(',', $current_city_id_array);
            }
        }
        
        // 编辑运费模板时的区县id组排除
        if (! empty($current_district_id_array)) {
            if (! strstr($current_district_id_array, ',')) {
                array_push($curr_district_id_array, $current_district_id_array);
            } else {
                $curr_district_id_array = explode(',', $current_district_id_array);
            }
        }
        
        $ns_order_shipping_fee = new NsOrderShippingFeeModel();
        $list = $ns_order_shipping_fee->getQuery([
            'co_id' => $co_id,
            'is_default' => 0
        ], 'province_id_array,city_id_array,district_id_array', '');
        
        // 1.把当前公司的所有省市id进行组拼
        $province_id_array = [];
        $city_id_array = [];
        $district_id_array = [];
        
        $res_list['province_id_array'] = [];
        $res_list['city_id_array'] = [];
        $res_list['district_id_array'] = [];
        
        foreach ($list as $k => $v) {
            
            if (! strstr($v['province_id_array'], ',')) {
                array_push($province_id_array, $v['province_id_array']);
            } else {
                $temp_province_array = explode(",", $v['province_id_array']);
                foreach ($temp_province_array as $temp_province_id) {
                    array_push($province_id_array, $temp_province_id);
                }
            }
            
            if (! strstr($v['city_id_array'], ',')) {
                array_push($city_id_array, $v['city_id_array']);
            } else {
                $temp_city_array = explode(",", $v['city_id_array']);
                foreach ($temp_city_array as $temp_city_id) {
                    array_push($city_id_array, $temp_city_id);
                }
            }
            
            if (! strstr($v['district_id_array'], ',')) {
                array_push($district_id_array, $v['district_id_array']);
            } else {
                $temp_district_array = explode(",", $v['district_id_array']);
                foreach ($temp_district_array as $temp_district_id) {
                    array_push($district_id_array, $temp_district_id);
                }
            }
        }
        
        // 2.排除当前编辑用到的省id组
        if (count($province_id_array)) {
            foreach ($province_id_array as $province_id) {
                $flag = true;
                foreach ($curr_province_id_array as $temp_province_id) {
                    
                    if ($province_id == $temp_province_id) {
                        $flag = false;
                    }
                }
                if ($flag) {
                    array_push($res_list['province_id_array'], $province_id);
                }
            }
        }
        
        // 3.排除当前编辑用到的市id组
        if (count($city_id_array)) {
            foreach ($city_id_array as $city_id) {
                $flag = true;
                foreach ($curr_city_id_array as $temp_city_id) {
                    
                    if ($city_id == $temp_city_id) {
                        $flag = false;
                    }
                }
                if ($flag) {
                    array_push($res_list['city_id_array'], $city_id);
                }
            }
        }
        
        // 4.排除当前编辑用到的区县id组
        if (count($district_id_array)) {
            foreach ($district_id_array as $district_id) {
                $flag = true;
                foreach ($curr_district_id_array as $temp_district_id) {
                    
                    if ($district_id == $temp_district_id) {
                        $flag = false;
                    }
                }
                if ($flag) {
                    array_push($res_list['district_id_array'], $district_id);
                }
            }
        }
        
        return $res_list;
    }
}