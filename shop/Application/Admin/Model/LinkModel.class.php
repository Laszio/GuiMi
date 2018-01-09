<?php
namespace Admin\Model;

use Think\Model;

class LinkModel extends Model
{
	


	/**
	 * 添加友链
	 * @Author   ryan
	 * @DateTime 2017-11-21
	 * @email    931035553@qq.com
	 */
	public function addLink()
	{	


		//正则表达式判断网址是否正确
		$regex = "/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";

		$res = preg_match($regex,I('post.toUrl'));
		if (!$res) {
			error('网址有误请重新填写');
			exit;
		}
		//判断域名是否正确
		$regex = '/^[\x{4e00}-\x{9fa5}\w]+$/u';
		$res = preg_match($regex,I('post.toUrlname'));
		if ( !$res ) {
			error('域名有误请重新填写');
			exit;
		}
		//判断状态是否正确
		$res = in_array(I('post.status'),[1,2]);
		if ( !$res ) {
			error('状态有误，请重新选择');
			exit;
		}
		//判断显示的轮播图是否超过5个
		$res = $this->where('status=1')->count();
		if ( $res >= 5 && I('post.status') == 1 ) {
			error('现显示广告已达到5个，请选择不显示或修改其他广告的显示状态');
			exit;
		}
		
		if (I('post.showtime') < 0 ) {
			error('展示时间必须大于0');
			exit;
		}
		//上传文件
		//实例化上传类
		$config = array(
            'maxSize' => 3145728,    
            'rootPath' => './Uploads/', 
          	'savePath' => '/link/',
            // 'saveName' => array('uniqid',''),  
            'exts' => array('jpg', 'gif', 'png', 'jpeg'),    
            'autoSub' => false,
            // 'subName' => array('date','Ymd'),
        );
		$upload = new \Think\Upload($config);
		//上传文件
		$res = $upload->uploadOne($_FILES['pic']);
		if ( !$res ) {
			error($upload->getError());
			exit;
		}

		//往数据库添加数据
		$data =I('post.');
		$data['img'] = $res['savepath'].$res['savename'];

		$row = $this ->add($data);
		if ($row) {
			return true;
		} else {
			error('添加失败');
			exit;
		}
	}

	/**
	 * 修改友链
	 * @Author   ryan
	 * @DateTime 2017-11-21
	 * @email    931035553@qq.com
	 */
	public function editLink()
	{	


		//正则表达式判断网址是否正确
		$regex = "/^((https?|ftp|news):\/\/)?([a-z]([a-z0-9\-]*[\.。])+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]))(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&]*)?)?(#[a-z][a-z0-9_]*)?$/";

		$res = preg_match($regex,I('post.toUrl'));
		if (!$res) {
			error('网址有误请重新填写');
			exit;
		}
		//判断域名是否正确
		$regex = '/^[\x{4e00}-\x{9fa5}\w]+$/u';
		$res = preg_match($regex,I('post.toUrlname'));
		if ( !$res ) {
			error('域名有误请重新填写');
			exit;
		}


		if (I('post.showtime') < 0 ) {
			error('展示时间必须大于0');
			exit;
		}
		//判断状态是否正确
		$res = in_array(I('post.status'),[1,2]);
		if ( !$res ) {
			error('状态有误，请重新选择');
			exit;
		}
		//判断显示的轮播图是否超过5个
		$map['id'] = ['neq',I('post.id')]; 
		$map['status'] = 1; 
		$res = $this->where($map)->count();
		if ( $res >= 5 && I('post.status') == 1 ) {
		
			error('现显示广告已达到5个，请选择不显示或修改其他广告的显示状态');
			exit;
		}
		//往数据库添加数据
		$data =I('post.');
		//上传文件
		//实例化上传类

		if (!empty($_FILES['pic']['tmp_name'])) {
			$config = array(
	            'maxSize' => 3145728,    
	            'rootPath' => './Uploads/images/',  
	            // 'saveName' => array('uniqid',''),  
	            'exts' => array('jpg', 'gif', 'png', 'jpeg'),    
	            'autoSub' => false,
	            // 'subName' => array('date','Ymd'),
	        );
			$upload = new \Think\Upload($config);
			//上传文件
			$res = $upload->uploadOne($_FILES['pic']);
			if ( !$res ) {
				error($upload->getError());
				exit;
			}
			//添加数据
			$data['img'] = $res['savename'];
		}

		$row = $this->save($data);

		if ($row) {
			return true;
		} else {
			error('修改失败');
			exit;
		}
	}
}


