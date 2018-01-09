<?php
namespace Home\Model;
use Think\Model;

class AddressModel extends Model
{
    protected $_validate = [
        ['name', '/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、中文组成的名字'],
        ['sf', '/^[\d]+$/u', '非法参数'],
        ['city', '/^[\d]+$/u', '非法参数'],
        ['code', '/^\d{6}$/', '有效的邮箱是6位数哦~'],
        ['phone', '/^(1[358]\d|147|17[789])\d{8}$/', '无效的电话号码', 2],
        ['address', '/^[0-9a-zA-Z\x{4e00}-\x{9fa5}]+$/u', '请输入由数字、字母、中文组成的地址'],
    ];

    /**
     * 处理表单提交过来的三级联动信息
     * @return array 被处理完的地区信息
     */
    public function getAddressInfo() 
    {
        $areas = M('Areas');
        $areaSF = $areas->field('area_name')->where('id = '.$_POST['sf'])->find();
        $areaCITY = $areas->field('area_name')->where('id = '.$_POST['city'])->find();
        $areaAREA = $areas->field('area_name')->where('id = '.$_POST['area'])->find();
        $areaSF = $areaSF['area_name'];
        $areaCITY = $areaCITY['area_name'];
        $areaAREA = $areaAREA['area_name'];
        unset($_POST['sf']);
        unset($_POST['city']);
        unset($_POST['area']);
        return $areaSF.$areaCITY.$areaAREA;
    }
	
}