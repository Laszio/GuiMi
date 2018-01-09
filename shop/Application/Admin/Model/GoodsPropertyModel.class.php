<?php
namespace Admin\Model;
use Think\Model;

class GoodsPropertyModel extends Model
{
	protected $_validate = [
		['size', 'require', '尺寸不能为空'],
		// ['size', '/^(S|M|L|XL|XXL|XXXL)$/i', '请输入S,M,L,XL,XXL,XXXL之类的码数', 3],
		['store', 'require', '库存不能为空'],
		['store', '/^\d+$/u', '请输入数字' ],
		['color', 'require', '颜色不能为空'],
		['color', '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的颜色名'],
	];

	public function index()
	{

	}

	/**
	 * 获取详情页的属性数据
	 * @return [type] [description]
	 */
	public function getPro() 
	{
		$property = $this->field('id,gid,color,colorpic,size,store')->where('gid='.I('get.id'))->select();
		return $property;
	}
}