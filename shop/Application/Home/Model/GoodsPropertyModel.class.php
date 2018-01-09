<?php
namespace Home\Model;
use Think\Model;

class GoodsPropertyModel extends Model
{
	
	public function editPro($gid) 
	{
        $pro = $this->field('color,colorpic,size')->where('gid='.$gid)->select();
        $data['size'][] = $pro[0]['size'];  
        foreach ($pro as $k => $v) {
            if ($data['size'][0] != $v['size']) {
                $data['size'][] = $v['size'];
            }
            unset($pro[$k]['size']);
        }
        $arr = [];
        foreach ($pro as $k => $v) {
            $res = deep_in_array($v['color'], $arr);
            if (!$res) {
                $arr[$k] = $v;
            }
             
        }
        $data['size'] = array_unique($data['size']);
        $arr['size'] = $data['size'];
        return $arr;
    }

}