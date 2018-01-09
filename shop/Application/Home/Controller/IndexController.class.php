<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends PublicController 
{
    public function index()
    {

    	$indexModel = D('Index');
        $arr = $indexModel ->index();
        $hotGoodsInfo = $indexModel->getHotGoods();

        $this->firstBar();
        $this->assign($arr);
     	$this->assign('hotGoodsInfo', $hotGoodsInfo);
     	$this->display();

    }

    public function bar ($id)
    {
    	$map['path'] = ['like','%,'.$id.',%'];
    	$map['attr'] = [3];
		$arr = M('type')
                ->where($map)
                ->cache(true)
                ->order('id')
                ->getField('id,id,name');

		$this->ajaxReturn($arr);

    }
}