<?php

// +----------------------------------------------------------------------
// | FileName:   HomeController.class.php
// +----------------------------------------------------------------------
// | Dscription:   前台基类控制器
// +----------------------------------------------------------------------
// | Date:  2017/7/31 13:32
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------

namespace  Home\Controller;

use Home\Controller\HomeController;
use THink\Controller;
use Home\Controller\ProductController;

class BasketController extends HomeController
{
	public $basket_id;
	
	protected function _initialize()
	{
		parent::_initialize(); // TODO: Change the autogenerated stub
	}

	/*
	 * 购物车条目
	 */
	public function basketDetail($is_return=0){
	    $request=I('get.');
        $pageSize= $request['pageSize']?$request['pageSize']:C('PAGE_PAGESIZE');
        if( !isset( $request['pageSize'] ) ){
        	$request['pageSize'] = $pageSize;
		}
		$user_id=session('userId')?session('userId'):'';//是否有user_id为判断是否登录

		if($user_id) $basket_id=$this->loginBasketId($user_id);//本次购物车id
		else $basket_id=$this->notLoginBasketId();//未登录购物车id

        if(session('userId')){//购物车商品是否可按样品结算更新
            $parentId=0;
            if(session('userType')==20){
                $parent=M('user_son')->where(['user_id'=>$user_id])->find();
                $parentId=$parent['p_id'];
            }

            $where_detail=[
                'dbd.basket_id'=>$basket_id,
                'dbd.status'=>['in',[0,1]],
                'dupe.max_num>=dbd.num',
                'dupe.step'=>['gt',0],
                'dupe.uid'=>$parentId?:$user_id,
            ];
            $sampleId_arr=$sampleId_sample=[];
            $sampleId_result=$sampleId_result2=$sampleId_result3=$sampleId_result4='';
            M('basket_detail')->startTrans();

            $sampleId2=M('basket_detail_sample')->field('dbd.pid')->alias('dbd')->join('left join dx_user_product_example as dupe on dbd.pid=dupe.pid')->where($where_detail)->select();
            $sampleId2_arr=$sampleId2_sample=[];
            if(!$sampleId2){
                $basket_detail_sample=M('basket_detail_sample')->where(['basket_id'=>$basket_id])->select();
                if($basket_detail_sample){
                    foreach($basket_detail_sample as $k=>&$v){
                        unset($v['id']);
                    }
                    $sampleId_result3=M('basket_detail')->field('basket_id,type,pid,num')->addAll($basket_detail_sample);
                }
                $sampleId_result4=M('basket_detail_sample')->where(['basket_id'=>$basket_id])->delete();
            }else{
                foreach($sampleId2 as $k=>$v){
                    $sampleId2_arr[]=$v['pid'];
                    $sampleId2_sample[]=[
                        'basket_id'=>$basket_id,
                        'pid'=>$v['pid'],
                        'num'=>$v['num'],
                        'type'=>1,
                    ];
                }
                $sampleId_sample_last=M('basket_detail_sample')->field("basket_id,pid,num,type")->where(['basket_id'=>$basket_id,'pid'=>['not in',$sampleId2_arr]])->select();
                $sampleId_result3=M('basket_detail_sample')->where(['basket_id'=>$basket_id,'pid'=>['not in',$sampleId2_arr]])->delete();
                if($sampleId_sample_last){
                    foreach ($sampleId_sample_last as $sample_k=>$sample_last){
                        $sampleId_exit=M('basket_detail')->field("basket_id,pid,num,type")->where(['basket_id'=>$basket_id,'pid'=>$sample_last['pid']])->find();
                        if($sampleId_exit){
                            unset($sampleId_sample_last[$sample_k]);
                        }
                    }
                    if($sampleId_sample_last){
                        $sampleId_result4=M('basket_detail')->where(['pid'=>['not in',$sampleId_arr]])->addAll($sampleId_sample_last);
                    }
//                    $sampleId_result4=M('basket_detail')->where(['pid'=>['not in',$sampleId_arr]])->addAll($sampleId_sample_last);
//                    echo M()->getLastSql();
                }
            }

            $sampleId=M('basket_detail')->field('dbd.pid,dbd.num')->alias('dbd')->join('left join dx_user_product_example as dupe on dbd.pid=dupe.pid')->where($where_detail)->select();
            if($sampleId){
                foreach($sampleId as $k=>$v){
                    $sampleId_arr[]=$v['pid'];
                    $sampleId_sample[]=[
                        'basket_id'=>$basket_id,
                        'pid'=>$v['pid'],
                        'num'=>$v['num'],
                        'type'=>1,
                    ];
                }
                $sampleId_result=M('basket_detail')->where(['basket_id'=>$basket_id,'pid'=>['in',$sampleId_arr]])->delete();
                $sampleId_result2=M('basket_detail_sample')->addAll($sampleId_sample);
            }
            if($sampleId_result===false||$sampleId_result2===false||$sampleId_result3===false||$sampleId_result4===false){
                M('basket_detail')->rollback();
            }else{
                M('basket_detail')->commit();
            }
        }

		$where=['basket_id'=>$basket_id,'status'=>['in',[0,1]]];

        $count = M('basket_detail')->where($where)->count();
        $count += M('basket_detail_sample')->where($where)->count();
        $Page = new \Think\Page($count, $pageSize);// 实例化分页类 传入总记录数和每页显示的记录

        $basket_detail_limit='';
        if(!IS_AJAX) $basket_detail_limit=$Page->firstRow . ',' . $Page->listRows;
        if($is_return) $basket_detail=D('basket_detail')->where($where)->order('update_at desc')->select();
        else $basket_detail=D('basket_detail')->where($where)->order('update_at desc')->limit($basket_detail_limit)->select();

        //样品
        $basket_detail_example=D('basket_detail_sample')->where($where)->order('update_at desc')->limit($basket_detail_limit)->select();
        $example_arr=[];
        if($basket_detail_example){
            foreach($basket_detail_example as $k=>$v){
                $example_arr[$v['pid']]=$v['num'];
            }
        }

		$field_pid=$basket_num=[];
		$basket_detail_time=[];
		$productId_arr=[];

		foreach($basket_detail as $k=>$v){
			$field_pid[$k]['id']=$v['pid'];
			$basket_num[$v['pid']]['num']=$v['num'];
			$basket_num[$v['pid']]['update_at']=$v['update_at'];
            $basket_detail_time[]=$productId_arr[]=$v['pid'];
		}

        foreach($basket_detail_example as $k=>$v){
		    if(!in_array($v['pid'],$basket_detail_time)) $basket_detail_time[]=$productId_arr[]=$v['pid'];
        }

        $producResult=$this->productList(['id'=>['in',$productId_arr]]);
        if($producResult['error']!=0){//清空购物车
            $delete_result=M('basket_detail')->where(['basket_id'=>$basket_id])->delete();
            $delete_result_sample=M('basket_detail_sample')->where(['basket_id'=>$basket_id])->delete();
        }else{
            if($producResult['data']['count']['count']!=count($productId_arr)){//删除已删除产品id的购物车商品
                foreach($productId_arr as $k=>$v){
                    if(!M('product')->where(['id'=>$v])->find()){
                        M('basket_detail')->where(['basket_id'=>$basket_id,'pid'=>$v])->delete();
                    }
                }
            }
        }
        $product_list=($producResult['error']==0)?$producResult['data']['list']:'';

		$product_list_result=[];
		foreach($product_list as $k=>$v){
			$product_list[$k]['basket_num']=$basket_num[$v['id']]['num'];
			$product_list[$k]['update_at']=$basket_num[$v['id']]['update_at'];
			$product_list_result[$v['id']]=$product_list[$k];
		}
		$basket_detail=$pId_arr=[];
		foreach($basket_detail_time as $v){
			$basket_detail[]=$product_list_result[$v];
		}

		if(!$is_return){
            $show = $Page->show();// 分页显示输出
            if ($count <= $pageSize) $show=null;
            $this->assign('page',$show);
        }
		if(session('userId')){
            $list=$this->customerProductPrice(session('userId'),$basket_detail_time);
            foreach($basket_detail as $k=>$v){
                if($list['error']==0){
                    $basket_detail[$k]['bargainInfo']=$list['data']['list'][$v['id']];
                }
                $basket_detail[$k]['sample']=0;
                if((int)$v['basket_num']===0) unset($basket_detail[$k]);
                if(isset($example_arr[$v['id']])&&$example_arr[$v['id']]){
                    $one_example=$v;
                    $one_example['basket_num']=$example_arr[$v['id']];
                    $one_example['sample']=1;
                    $basket_detail[]=$one_example;
                }
            }
        }

        if(IS_AJAX){
            sort($basket_detail);
            die(json_encode($basket_detail));
        }

		if($is_return) return count($basket_detail);
        $this->assign('isLogin',$user_id?true:false);
		$this->assign('basket_detail',$basket_detail);
        $this->assign('request',$request);
		$this->display();
	}

	/*
	 * 登录成功更新用户购物车
	 */
	public function loginBasket($user_id=''){
		if(!$user_id) die('参数错误');

		$notLoginBasketId=$this->notLoginBasketId();//未登录notlogin_basket_id
		$loginBasketId=$this->loginBasketId($user_id);//用户前一次的basket_id

		$basket_detail=D('basket_detail');
		$where=['basket_id'=>['in',[$notLoginBasketId,$loginBasketId]]];
		$basket_items=$basket_detail->where($where)->select();
		$basket_items_change=[];
		foreach($basket_items as $k=>$v){
			$basket_items_change[$v['pid']]+=$v['num'];
		}
		try{
			D('basket_detail')->startTrans();
			$newBasketId=$this->newBasketId();
			if($user_basket=D('basket')->where(['user_id'=>$user_id])->find())//更新basket_id
			D('basket')->save(['id'=>$user_basket['id'],'user_id'=>$user_id,'basket_id'=>$newBasketId]);
			else D('basket')->add(['user_id'=>$user_id,'basket_id'=>$newBasketId]);
			//删除旧购物车
			$basket_detail_delete=D('basket_detail');
			$where_delete=$where;
			$basket_detail_delete->where($where_delete)->delete();
			//保存新购物车
			$basket_detail_addAll=[];
			foreach($basket_items_change as $pid=>$num){
				$basket_detail_addAll[]=['pid'=>$pid,'num'=>$num,'basket_id'=>$newBasketId];
			}
			$basket_detail_change=D('basket_detail');
			$basket_detail_change->addAll($basket_detail_addAll);
			M('basket_detail_sample')->where(['basket_id'=>$loginBasketId])->save(['basket_id'=>$newBasketId]);//样品购物车
			D('basket_detail')->commit();
			return 1;
		}catch(Exception $e){
			D('basket_detail')->rollback();
			return 0;
		}
	}

	/*
	 * 购物车更新商品
	 * 操作basket_detail
	 *params=[ num----0为删除商品，其它为更新
	 *			pid ]
	 */
	public function basketAddProduct(){
		if(IS_AJAX){
			$data=I('post.');
			$user_id=session('userId')?session('userId'):'';//是否有user_id为判断是否登录

			if($user_id) $basket_id=$this->loginBasketId($user_id);//用户的basket_id
			else $basket_id=$this->notLoginBasketId();//未登录购物车id

            $whereExample=[
                'uid'=>$user_id,
                'pid'=>['in',$data['pid']],
            ];
            $productExample=D('product')->customerProductExampleList($whereExample);
            if ($data['num']>0 && $productExample['error'] === 0 && isset($productExample['data']['list'][$data['pid']]) &&(int)$productExample['data']['list'][$data['pid']]['step']>0 && (int)$productExample['data']['list'][$data['pid']]['max_num'] >= (int)$data['num']) {//按样品结算
                $sampleM=M('basket_detail_sample');
                $sampleM->startTrans();

                $sample_result1=$sampleM->where(['pid'=>$data['pid'],'basket_id'=>$basket_id])->delete();
                $sample_result2=$sampleM->add(['pid'=>$data['pid'],'basket_id'=>$basket_id,'type'=>1,'num'=>$data['num']]);
                $sample_result3=M('basket_detail')->where(['pid'=>$data['pid'],'basket_id'=>$basket_id])->delete();
                if($sample_result1===false||!$sample_result2||$sample_result3===false){
                    $sampleM->rollback();
                    $sample_return=['error'=>1,'msg'=>'样品添加失败'];
                }else{
                    $sampleM->commit();
                    $sample_return=['error'=>0,'msg'=>'success'];
                }
                die(json_encode($sample_return));
            }

            $num_is_add=isset($data['num_is_add'])?$data['num_is_add']:1;//购物车是添加数量还是更新数量

			$basket_detail=M('basket_detail');
			$basket_detail->basket_id=$basket_id;
			$basket_detail->pid=$data['pid'];
			if($data['num']){
				//查看购物车是否有这款商品
				$where=['basket_id'=>$basket_id,'pid'=>$data['pid']];
				$basket_item=M('basket_detail')->where($where)->find();
				$product=M('product')->where(['id'=>$data['pid']])->find();
				if($basket_item){//更新数量
					$basket_detail->id=$basket_item['id'];
					$num__=($num_is_add==1)?$data['num']+$basket_item['num']:$data['num'];
					$basket_detail_data=[
					    'status'=>0,
                        'num'=>$num__,
                        'id'=>$basket_item['id'],
                        'p_num_detail'=>($num_is_add==1)?$basket_item['p_num_detail']."$data[num],":"$data[num],",
                    ];
					if(($basket_detail->data($basket_detail_data)->save())!==false){
					    $return=['error'=>0,'msg'=>'更新成功'];
                    } else{
					    $return=['error'=>1,'msg'=>'更新失败'];
                    }
				}else{//新增商品
					$basket_detail->num=$data['num'];
                    $basket_detail->p_num_detail="$data[num],";
					if($basket_detail->add()) $return=['error'=>0,'msg'=>'添加成功'];
					else $return=['error'=>1,'msg'=>'添加失败'];
				}
			}else{//删除商品
                if($data['pid']){
                    $goods_result=M('basket_detail')->where(['basket_id'=>$basket_id,'pid'=>['in',$data['pid']]])->delete();
                }
                if($data['sample_id']){
                    $sample_result=M('basket_detail_sample')->where(['pid'=>['in',$data['sample_id']],'basket_id'=>$basket_id,'type'=>1])->delete();
                }
                if($goods_result===false||$sample_result===false)$return=['error'=>1,'msg'=>'删除失败'];
				else $return=['error'=>0,'msg'=>'删除成功'];
			}
			die(json_encode($return));
		}
		$this->display();
	}

	/*
	 * 清空购物车
	 *params[delete=clearBasketDetail]
	 */
	public function clearBasketDetail(){
		if(IS_AJAX && I('get.delete')=='clearBasketItems'){
			$user_id=session('userId')?session('userId'):'';//是否有user_id为判断是否登录

			if($user_id) $basket_id=$this->loginBasketId($user_id);//用户的basket_id
			else $basket_id=$this->notLoginBasketId();//未登录购物车id

			if(D('basket_detail')->where(['basket_id'=>$basket_id])->delete())
			$return=['error'=>0,'msg'=>'清除成功'];
			else $return=['error'=>1,'msg'=>'清除失败'];
			die(json_encode($return));
		}
	}

//	/*
//	 * 检查商品是否存在，库存是否充足
//	 */
//	public function fullProductNum($is_return='',$is_pid='',$is_num=''){
//		$data=I('get.');
//		$pid=$data['pid'];
//		$num=$data['num'];
//		if($is_return){
//			$pid=$is_pid;
//			$num=$is_num;
//		}
//		$where=['id'=>$pid];
//		$one_product=D('product')->where($where)->find();
//		if(!$one_product)die(json_encode(['error'=>1,'msg'=>'没有此商品']));
//		if($one_product['is_delivery']==1){
//			$return=['error'=>0,'msg'=>'有货期','data'=>$one_product];
//		} else if( $one_product['store']==0 ){
//			$return=['error'=>1,'msg'=>'库存为0且没有货期','data'=>$one_product];
//		}else if($is_num>$one_product['store']){
//			$return=['error'=>1,'msg'=>'库存不足且没有货期','data'=>$one_product];
//		}else{
//			$return=['error'=>0,'msg'=>'商品充足','data'=>$one_product];
//		}
//		if($is_return) return $return;
//		else die(json_encode($return));
//	}


	/*
	 * 用户新basket_id
	 */
	public function newBasketId($key='daxin'){
		return md5(session_id().uniqid().$key);
	}

	/*
	 * 未登录notlogin_basket_id
	 */
	public function notLoginBasketId(){
		$notlogin_basket_id = cookie('basket')?cookie('basket'):session_id();
		cookie('basket',$notlogin_basket_id,5184000);
		return $notlogin_basket_id;
	}

	/*
	 * 用户的basket_id
	 */
	public function loginBasketId($user_id=''){
		$loginBasketId='';
		$where=['user_id'=>$user_id];
		$basket=D('basket')->where($where)->find();
		if($basket){
			$loginBasketId=$basket['basket_id'];
		}else{
			$newBasketId=$this->newBasketId();
			D('basket')->data(['user_id'=>$user_id,'basket_id'=>$newBasketId])->add();
			$loginBasketId=$newBasketId;
		}
		return $loginBasketId;
	}

	/*
	 * 再次购买，商品添加到购物车
	 */
	public function againBuyBasket(){
        !is_login() && die(json_encode(['error'=>300,'msg'=>'您未登录']));
		$get=I('get.');
		$goods=$get['goods'];
//		$goods=[['pid'=>2,'num'=>12],['pid'=>18013,'num'=>32]];
		$user_id=session('userId');
		$basket=D('basket')->where(['user_id'=>$user_id])->find();
		$pid_arr=$goods_pid=[];
		foreach($goods as $k=>$v){
		    if(in_array($v['pid'],$pid_arr)){
		        unset($goods[$k]);
            }
			$pid_arr[]=$v['pid'];
			$goods_pid[$v['pid']]=$v['num'];
		}
		$basket_id=$basket['basket_id'];
		//取出购物车商品
		$where=['basket_id'=>$basket_id,['pid'=>['in',$pid_arr]]];
		$basket_detail=D('basket_detail')->where($where)->select();
		$basket_list=[];
		foreach($basket_detail as $k=>$v){
			$basket_list[$v['pid']]=$v;
		}
		$add_data=$update_data=[];
		$status=1;
		$error=['error'=>0,'msg'=>'操作成功'];
		foreach($goods as $k=>$v){
			if($basket_list[$v['pid']]){//update
				$update_data['status']=$status;
				$update_data['pid']=$v['pid'];
				$update_data['basket_id']=$basket_id;
				$update_data['num']=(int)$v['num']+(int)$basket_list[$v['pid']]['num']?:0;
				$result=D('basket_detail')->data($update_data)->where(['id'=>$basket_list[$v['pid']]['id']])->save();
				if(!$result) $error=['error'=>1,'msg'=>'操作失败'];
			}else{//insert
				$add_data['status']=$status;
				$add_data['pid']=$v['pid'];
				$add_data['basket_id']=$basket_id;
				$add_data['num']=(int)$v['num'];
				$result=D('basket_detail')->data($add_data)->add();
				if(!$result) $error=['error'=>1,'msg'=>'操作失败'];
			}
		}
		die(json_encode($error));
	}



	


}
