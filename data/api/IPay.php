<?php
namespace data\api;

interface IPay
{
    public function doPay($params);

    public function doPayResultCallBack($params);
}

