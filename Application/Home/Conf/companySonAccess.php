<?php

// +----------------------------------------------------------------------
// | FileName:   orderExcel.php
// +----------------------------------------------------------------------
// | Dscription:订单excel下载配置
// +----------------------------------------------------------------------
// | Date:  2018/6/12 13:45
// +----------------------------------------------------------------------
// | Author: kelly <466395102@qq.com>
// +----------------------------------------------------------------------
return [
    'MASTER_DISABLE'=>[//主帐户不能用子帐户的功能
           'Home\Design\OrderDisableDesign'=>[
               'Home/Order/cancleOrder' => '取消定单',
               'Home/Order/knotOrder' => '取消出贷',
               'Home/Order/comment'=>'订单评价',
               'Home/Knotorder/knotOrderRequest'=>'返差额',
           ]
    ],



];