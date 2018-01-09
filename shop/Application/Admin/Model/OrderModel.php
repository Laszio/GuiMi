<?php
namespace Admin\Model;
use \Think\Model;

class OrderModel extends Model
{
	protected $_validate = [
        ['code', '/^\d{6}$/', '有效的邮箱是6位数哦~'],
        ['phone', '/^(1[358]\d|147|17[789])\d{8}$/', '无效的电话号码', 2],
        ['address', '/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、中文组成的地址'],
        ['getman', '/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、中文组成的地址'],
        ['content', '/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、中文组成的地址'],
	];

	/**
	 * 对退货信息处理
	 * @return array 处理完的数据
	 */
	public function getComeBack() 
	{
        $comebackModel = M('Comeback');
        $comebackInfo = $comebackModel->select();
        $isget = [1=>'已收到', '未收到'];
        $reason = [1=>'七天无理由退货', '我不想要了', '退运费', '颜色/尺寸/参数不符', '商品瑕疵', '质量问题', '少件/漏发', '未按约定时间发货', '收到商品时有划痕或破损'];
        $status = [1=>'未审核'， '已审核']
        foreach ($comebackInfo as $k => $v) {
        	$comebackInfo[$k]['isget'] = $isget[$v['isget']];
        	$comebackInfo[$k]['reason'] = $isget[$v['reason']];
        	$comebackInfo[$k]['status'] = $isget[$v['status']];
        }

        return $comebackInfo;
	}
}