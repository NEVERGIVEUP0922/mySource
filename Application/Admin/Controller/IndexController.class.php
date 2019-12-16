<?php

// +----------------------------------------------------------------------
// | FileName:   IndexController.class.php
// +----------------------------------------------------------------------
// | Dscription: 后台首页控制器
// +----------------------------------------------------------------------
// | Date:  2017/8/6 14:30
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------

namespace  Admin\Controller;
use Admin\Controller\AdminController;
use Boris\Config;

class IndexController extends AdminController
{
	protected function _initialize()
	{
		parent::_initialize(); // TODO: Change the autogenerated stub
	}

	public function index()
	{
//        de( session() );
//	    echo hash_string('123456');
//	    exit();
	    //防止页面无法加载
//		if( $_SERVER['REQUEST_URI'] != '/admin.php/Index/login.html' ){
//			redirect(U('Admin/Index/index'));
//		}
        $this->assign('user_id',session('adminId'));
		if(!is_adminLogin()){
			redirect(U('Admin/Index/login'));
			exit();
		}
		$this->display();
	}

	public function getMenu()
	{
//		$menuFile = APP_PATH.'/Admin/Conf/menu.php';
//		$menu = include($menuFile);
		$menu = session('adminMenu');
		exit(json_encode($menu));
	}

	public function test()
	{
		$this->display();
	}
	
	public function login()
	{
		if(is_adminLogin()){
			redirect(U('Admin/Index/index'));
			exit();
		}
		if ( IS_POST || IS_AJAX ) {
			$user_name = I('username');
			$user_pass = I('password');
			//检查账号是否被锁定
			if( $this->isUserNameLock($user_name) ){
				$this->ajaxReturnStatus(1003,'您的账号已被锁定!请15分钟后登陆');
			}else{
				//$this->loginFalseCheck($user_name);
			}
			$re = M('user','sys_')->where(['nickname'=>$user_name])->find();
			if($re){
				if( hash_check($user_pass,$re['password']) ){
					$this->addAdminUserToSession($re['uid']);
                    $key = 'loginFalse:Admin'.$user_name;
                    S($key,null);
//
//
					$this->ajaxReturnStatus(0000, '登录成功');
				}else{
					$this->loginFalseCheck($user_name);
				}
			}else{
				$this->ajaxReturnStatus(1001,'用户名不存在');
			}
		}else{
			layout(false);
			$this->display();
		}
	}
	
	public function logout()
	{
        session('adminId', null);
        session('adminInfo', null);
        session('adminFunctions', null);
        session('adminMenu', null);
		S($this->ssid,null);
		if($_COOKIE['adminId']) unset($_COOKIE['adminId']);
		if($_COOKIE['adminInfo']) unset($_COOKIE['adminInfo']);
		if($_COOKIE['adminFunctions']) unset($_COOKIE['adminFunctions']);
        if($_COOKIE['adminMenu']) unset($_COOKIE['adminMenu']);
		$this->redirect('index/login/',['token'=>C('ADMIN_TOKEN')]);
	}

	/**
	 * @desc 检查指定账号是否被锁定
	 * @param  string   $userName  用户名
	 *
	 * @return  锁定返回true 正常返回false
	 */
	protected function isUserNameLock($userName)
	{
		$lockKey = 'LoginLock:Admin'.$userName;
		$lockData = S( $lockKey );
		if( $lockData ){
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * @desc 登录错误限制
	 */
	protected function loginFalseCheck( $userName )
	{
		//$redis = $this->connectRedis();
		//获取是否配置检查错误限制
		$status = C('Home_LOGIN_FALSE_CHECK');
		//为零时 ,代表不检查登录错误限制
		if( $status == 0 ){
			return true;
			exit();
		}elseif( $status == 1 ){
			//读取配置
			//最大错误次数
			$maxNum = C('Home_LOGIN_FALSE_MAX');
			//锁定有效时长
			$time = C('Home_LOGIN_FALSE_TIME');//默认900秒
			//定义锁定键 键名
			$lockKey = 'LoginLock:Admin'.$userName;
//			S( $lockKey, null );
			$lockData = S( $lockKey );
			//$lockData = $redis->hGetAll( $lockKey );
			if( $lockData ){
				//获取锁定剩余有效时间
				//$cle = $redis->ttl( $lockKey );
				$oldTime = S($lockKey);
				$nowTime = time();
				$cle = ($oldTime + $time )- $nowTime;
				if( $cle < 60 ){
					$this->ajaxReturnStatus( 1200, '您的账号已锁定,请您'. $cle .'秒后再次尝试登录' );
				}else{
					$m = ceil( $cle/60 );
					$this->ajaxReturnStatus( 1200, '您的账号已锁定,请您'. $m .'分钟后再登录' );
				}
			}
			//读取登录错误次数
			$key = 'loginFalse:Admin'.$userName;
			//$data = $redis->hGetAll( $key );
			$num = S($key);
			if( empty( $num ) ){
				//$redis->hIncrBy( $key, 'num', 1 ); //错误次数加1
				S( $key, 1 );
				$this->ajaxReturnStatus(1200, '密码错误!请重新输入');
			}else{
				//$redis->hIncrBy( $key, 'num', 1 ); //错误次数加1
				S( $key, $num + 1 );
				//$newNum = $redis->hGet( $key, 'num' );
				$newNum = S($key);
				if( $newNum > $maxNum ) { //达到次数限制时
					//删除错误次数键 设置账号锁定键 并设置有效期
					//$redis->delete( $key );
					S($key, null);
					//$redis->hSet($lockKey, 'time', time());
					//$redis->expire( $lockKey, $time );
					S($lockKey,time(),300);
					$m = ceil( $time/60 );
					$this->ajaxReturnStatus(1200, '密码输入错误已超过' . $maxNum . '次!<br>账号暂时锁定,请您' . $m . '分钟后再登录');
				}else {
					$this->ajaxReturnStatus(1200, '密码错误!请重新输入!您还有' . ($maxNum - $newNum+1) . '次机会');
				}
			}
		}
	}

    /**
     * @desc 系统错误
     * $type=404,noPower
     */
	public function error(){
	    $this->assign('type','');
	    $this->display();
    }
}