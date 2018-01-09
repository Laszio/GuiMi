<?php
namespace Admin\Model;
use Think\Model;

class TypeModel extends Model
{
	protected $_validate = [
		['name', 'require', '分类名不能为空'],
		['name', '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、下划线、中文组成的分类名'],
		['name', '', '分类名已经存在', 0, 'unique', 3],
	];


	/**
	 * [getTypes 处理分页列表的样式]
	 * @return [array] [返回处理后的数组]
	 */
	public function getTypes()
	{
		$types = $this->field('id,name,attr,status,des')->order('concat(path, id)')->select();
		foreach ($types as $k => $v) {
			if ($v['attr'] == 1) {
				$types[$k]['tname'] = '┗━'.$v['name'];
			} else  {
				$types[$k]['tname'] = str_repeat('　', $v['attr']).'┗━━'.$v['name'];
			}
				
		}
		return $types;
	}

	/**
	 * [ajaxCommon 处理AJAX公共响应信息]
	 * @param  [int] $status [响应状态码]
	 * @param  [string] $msg    [提示信息]
	 * @return [array]         [响应回去的数组]
	 */
	public function ajaxCommon($status, $msg)
	{
		return ['status'=>$status, 'msg'=>$msg];
	}
}
