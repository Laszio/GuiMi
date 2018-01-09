<?php
namespace Admin\Model;
use \Think\Model;

class GoodsModel extends Model
{
	protected $_validate = [
		['gname', '/^[- a-zA-Z0-9\/_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的商品名名'],
		['price', '/^([0-9]+)(.[0-9]{1,2})$/u', '请输入100.00这样格式的商品价格'],
		['discount', '/^(100|[1-9][0-9]|[0-9])$/u', '输入10的,折扣以10%形式打折'],
		['status', '/^[1-3]$/u', '非法操作'],
	];

	/**
	 * 对分类进行处理
	 * @return array 被处理完的类型数组
	 */
	public function getTypes() 
	{
		$types = M('Type');
		$types = $types->order('concat(path,id)')->where('status = 1')->getField('id,name,path,attr', true);
		foreach ($types as $k => $v) {
			$dis = ($v['attr'] < 3) ? 'disabled' :'';
			$types[$k]['dis'] = $dis;
			if ($v['attr'] !=1 ) {
				$str = str_repeat('　', $v['attr']);
				$types[$k]['name'] = $str.$v['name'];
			}
		}
		return  $types;
	}

	/**
	 * 获取品牌
	 * @return [type] [description]
	 */
	public function getBrand() 
	{
		$brands = M('Brand');
		$brand = $brands->field('id,brand_name')->where('status = 1')->select();
		return $brand;
	}

	/**
	 * 对商品管理首页进行数据处理，对搜索条件进行筛选
	 * @return array 处理完的结果
	 */
	public function getGoods() 
	{
		
        
		$goods = $this->getField('id,pic1,pic2,pic3,pic4,pic5,gname,tid,bid,price,discount,buynum,clicknum,status,addtime', true);
		$type = M('Type');
		$brands = M('Brand');
		$arr = [1=>'新添加', '在售', '下架'];
		foreach ($goods as $k => $v) {
			$brand = $brands->field('brand_name')->where('id='.$v['bid'])->find();
			$tname = $type->where('id='.$v['tid'])->getField("name",true);
			$goods[$k]['tname'] = $tname[0];
			$goods[$k]['bid'] = $brand[brand_name];
			$goods[$k]['status'] = $arr[$v['status']];
			$goods[$k]['price'] = (($v['price'] * $v['discount']) /100).'.00';

		}
		return $goods;
	}

	/**
	 * 对商品信息数据进行处理
	 * @return [type] [description]
	 */
	public function getInfo() 
	{
		$goods = $this->where('id='.I('get.id'))->getField('id,bid,gname,status,tid,price,discount,score');
		$goods = $goods[I('get.id')];
		return $goods;
	}
}