<?php
namespace Home\Model;

use Think\Model;

class PublicModel extends Model
{

	function index ()
	{
		S([
			'type'=>'redis',   
			'host'=>'localhost',   
			'port'=>'6379',      
			'expire'=>60,
		]);

		$time = 60;

		$prefix = $this->tablePrefix;


		//导航栏
		$arr['title'] = $this->table($prefix.'type')
		                ->where('attr<3')
		                ->cache(true,$time,'redis')
		                ->group('pid')
		                ->order('pid')
		                ->getField('pid,group_concat(id) as ids,group_concat(name) as names');
		$ids = explode(',',$arr['title'][0]['ids']);
		$names = explode(',',$arr['title'][0]['names']);
		$first = array_combine($ids,$names);
		foreach($first as $k=>$v) {
			$ids = explode(',',$arr['title'][$k]['ids']);
			$names = explode(',',$arr['title'][$k]['names']);
			$arr['bar'][$k]['name'] = $v;
			$arr['bar'][$k]['child'] = array_combine($ids,$names);
		}


		//图片公共区域
		$imgpath = __ROOT__.'/Uploads';
		//轮播
		$arr['link'] = $this->table($prefix.'link')
								->where('status=1')
								->field('toUrl,img')
								->cache(true,$time,'redis')
								->select();

		foreach($arr['link'] as $k=>$v) {
			$arr['link'][$k]['img'] = $imgpath.$v['img'];
			$arr['link'][$k]['toUrl'] = $arr['link'][$k]['toUrl'];
		}
		return $arr;
	}
}