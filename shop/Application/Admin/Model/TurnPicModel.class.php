<?php
namespace Admin\Model;

use Think\Model;

class TurnPicModel extends Model
{
	protected $tableName = 'Turnpic';


	/**
	 * 添加友链
	 * @Author   ryan
	 * @DateTime 2017-11-21
	 * @email    931035553@qq.com
	 */
	public function addTurnPic()
	{	

		//判断状态是否正确
		$res = in_array(I('post.status'),[1,2]);
		if ( !$res ) {
			error('状态有误，请重新选择');
			exit;
		}

		if (I('post.showtime') < 0 ) {
			error('展示时间必须大于0');
			exit;
		}
		//判断显示的轮播图是否超过4个
		$res = $this->where('status=1')->count();
		if ( $res >= 4 && I('post.status') == 1 ) {
			error('现显示广告已达到4个，请选择不显示或修改其他广告的显示状态');
			exit;
		}
		$arr = explode('--',I('post.good'));
		$map['id'] = $arr[0];
		$map['gname']  = $arr[1];
		$map['tid'] =I('post.types');

		//判断tid和gid是否正确
		$res = $this->table($this->tablePrefix.'goods')->where($map)->count();
		if (!$res) {
			error('请选择现有产品');
			exit;
		}
		$map['gid'] = $map['id'];
		unset($map['id']);
		//上传文件
		//实例化上传类
		$config = array(
            'maxSize' => 3145728,    
            'rootPath' => './Uploads/',  
            'savePath' => '/turnpic/',  
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
		
		$map['status'] =I('post.status');
		$map['img'] = $res['savepath'].$res['savename'];

		$row = $this ->add($map);
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
	public function editTurnPic()
	{	


		//判断状态是否正确
		$res = in_array(I('post.status'),[1,2]);
		if ( !$res ) {
			error('状态有误,请重新选择');
			exit;
		}

		if (I('post.showtime') < 0 ) {
			error('展示时间必须大于0');
			exit;
		}
		$arr = explode('--',I('post.good'));

		$map['id'] = $arr[0];
		$map['gname']  = $arr[1];
		$map['tid'] =I('post.types');

		//判断tid和gid是否正确
		if ($map['id'] == 1) {
			error('请选择现有产品');
			exit;
		}

		$res = $this->table($this->tablePrefix.'goods')->where($map)->count();
		if (!$res) {
			error('非法操作');
			exit;
		}

		$map['gid'] = $map['id'];
		$map['id'] = I('post.id');
		//往数据库添加数据
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
			$map['img'] = $res['savename'];
		}

		$row = $this->save($map);
		if ($row) {
			return true;
		} else {
			error('修改失败');
			exit;
		}
	}
}


