<?php

// +----------------------------------------------------------------------
// | FileName:   UploadController.class.php
// +----------------------------------------------------------------------
// | Dscription:
// +----------------------------------------------------------------------
// | Date:  2017/8/7 17:37
// +----------------------------------------------------------------------
// | Author: showkw <showkw@163.com>
// +----------------------------------------------------------------------

namespace Home\Controller;

use Home\Controller\HomeController;

class UploadController extends HomeController
{

	public $root =  '';

	protected function _initialize()
	{
		parent::_initialize(); // TODO: Change the autogenerated stub
	}

	public function index()
	{
		$this->display();

	}

	public function uploadifive()
	{
		// 设定上传保存目录
		$uploadRoot = C('UPLOAD_ROOT');

		exit();
		// Set the allowed file extensions
		$fileTypes = array('jpg', 'jpeg', 'gif', 'png'); // Allowed file extensions

		$verifyToken = md5('unique_salt' . $_POST['timestamp']);

		if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
			$tempFile   = $_FILES['Filedata']['tmp_name'];
			$uploadDir  = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;
			$targetFile = $uploadDir . $_FILES['Filedata']['name'];

			// Validate the filetype
			$fileParts = pathinfo($_FILES['Filedata']['name']);
			if (in_array(strtolower($fileParts['extension']), $fileTypes)) {

				// Save the file
				move_uploaded_file($tempFile, $targetFile);
				echo 1;

			} else {

				// The file type wasn't allowed
				echo 'Invalid file type.';

			}
		}
	}

	public function postUpload()
	{
		//获取表单提交的上传文件Input的属性name的值
		$modName = I('modName')?I('modName'):'';
		$date = date('Y-m-d');
		//定义文件存储路径
		$path = $this->root.$modName.'/'.$date;

		//检查目录是否存在
		//若不存在则创建目录
		if( !file_exists( 'uploads/'.$path ) ) {
			mkdir('uploads/'.$path, 0755, true);
		}
		//判断是否为POST请求---文件上传必须为post
		if( $request->isMethod('post') ){
			//获取上传文件
			$file = $request->file($inputName);
			//判断文件是否上传成功
			if($file->isValid()){
				//获取上传文件相关信息
				// 获得文件原名
				$originalName = $file->getClientOriginalName();
				// 获得扩展名
				$ext = $file->getClientOriginalExtension();
				//获得临时文件的绝对路径
				$realPath = $file->getRealPath();
				//获取文件MIME类型
				$type = $file->getClientMimeType();
				//生成新文件名
				$fileName = md5(date('YmdHis').$originalName).'.'.$ext;
				//拼接文件存储路径
				$newPath = $path.'/'.date('Ymd',time()).'/'.$fileName;
				//移动文件
				$bool = Storage::disk('uploads')->put( $newPath, file_get_contents($realPath));
				if( $bool ){
					$res['status'] = 0;
					$res['src'] = '/uploads/'.$newPath;
				}else{
					$res['status'] = 1;
				}
				return json_encode($res);
			}
		}else{
			return '{status: 2 }';
		}
	}


}