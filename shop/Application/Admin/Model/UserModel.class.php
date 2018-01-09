<?php
namespace Admin\Model;
use Think\Model;

class UserModel extends Model
{
	/**
	 * [getAll 对会员列表的数据处理]
	 * @return [array] [返回一个处理后的数组]
	 */
	public function getAll()
	{
		// $data['status'] = 1;
		
		//时间搜索
		if (strlen(($_GET['start_time'])) > 0 && strlen(($_GET['end_time'])) > 0) {
			if (I('get.end_time') == date("Y-m-d", time())) {
				$data['addtime'] = ['between', [I('get.start_time'), date('Y-m-d H:i:s', time())]];
			} else {

				$data['addtime'] = ['between', [I('get.start_time').' 00:00:00', I('get.end_time').' 00:00:00']];
			}

		} else if (strlen(($_GET['start_time'])) > 0 && strlen(($_GET['end_time'])) == 0) {
			$data['addtime'] = ['between', [I('get.start_time'), date('Y-m-d H:i:s', time())]];

		}
		
		//用户信息搜索
		if (strlen(trim($_GET['info'])) > 0 && isset($_GET['info'])) {
			//多条件or
			$data['username|phone|email'] = [I('get.info'), I('get.info'), I('get.info'), "_multi"=>true];
			
		}
		
		$list = $this->field('shop_user.id,username,phone,email,status,addtime,score,user_img,sex,birthday,address,address2, real_name')->join('LEFT JOIN __USER_DETAIL__ on __USER__.id = __USER_DETAIL__.uid')->where($data)->select();
		
		/*数据处理*/
		$sex = ['', '男', '女', '保密'];
	
		foreach ($list as $k=>$v) {
			$list[$k]['sex'] = $sex[$v['sex']];
			$list[$k]['long_address'] = $v['address'].'　'.$v['address2'];
		}
		
		return $list;
	}
}