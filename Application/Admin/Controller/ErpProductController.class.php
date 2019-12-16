<?php

// +----------------------------------------------------------------------
// | FileName:   ProductController.php
// +----------------------------------------------------------------------
// | Dscription:
// +----------------------------------------------------------------------
// | Date:  2017/8/28 11:00
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------
namespace  Admin\Controller;

class ErpProductController extends AdminController
{

	protected function _initialize()
	{
		parent::_initialize(); // TODO: Change the autogenerated stub
	}

    /**
     * @desc 列表数据接口
     *
     */
	public function itemClsList(){
        $itemCls=D('erpitemclass')->itemClass();
        print_r($itemCls);
    }

    /**
     * @desc erp成本列表
     *
     */
    public function erpProductList(){
        if(IS_AJAX){
            $request=I('get.');
            $page=$request['page']?$request['page']:1;
            $pageSize=$request['pageSize']?$request['pageSize']:C('PAGE_PAGESIZE');
            $where=[];
            if($request['fitemno']) $where['FItemNo']=['like',"%$request[fitemno]%"];
            $list=D('Erpproduct')->erpProductList($where,$page,$pageSize);
            die(json_encode($list));
        }
    }











}