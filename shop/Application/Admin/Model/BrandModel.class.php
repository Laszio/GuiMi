<?php
namespace Admin\Model;
use Think\Model;

class BrandModel extends Model
{
	protected $_validate = [
		['brand_name', 'require', '品牌名不能为空'],
		['brand_name', '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的品牌名'],
		['brand_name', '', '品牌名已经存在', 0, 'unique', 1],
		['des', 'require', '写点简介再走吧~'],
	];

	/**
	 * [getAll 处理品牌列表页的数据]
	 * @return [array] [返回一个处理后的数据数组]
	 */
	public function getAll()
	{
		//时间搜索
		if (strlen(($_GET['start_time'])) > 0 && strlen(($_GET['end_time'])) > 0) {
			if (I('get.end_time') == date("Y-m-d", time())) {
				$data['update_time'] = ['between', [I('get.start_time'), date('Y-m-d H:i:s', time())]];
			} else {

				$data['update_time'] = ['between', [I('get.start_time').' 00:00:00', I('get.end_time').' 00:00:00']];
			}

		} else if (strlen(($_GET['start_time'])) > 0 && strlen(($_GET['end_time'])) == 0) {
			$data['update_time'] = ['between', [I('get.start_time'), date('Y-m-d H:i:s', time())]];

		}
		
		//品牌名搜索
		if (strlen(trim($_GET['brand_name'])) > 0 && isset($_GET['brand_name'])) {
			$data['brand_name'] = ['like', '%'.I('get.brand_name').'%'];
		}

		$res = $this->field('id,tid,typename,brand_name,logo,des,update_time,status')->where($data)->select();
		return $res;
	}

}
