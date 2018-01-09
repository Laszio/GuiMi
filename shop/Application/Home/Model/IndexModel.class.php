<?php
namespace Home\Model;

use Think\Model;

class IndexModel extends Model
{

	/**
	 * 处理首页信息
	 * @Author   ryan
	 * @DateTime 2017-11-28
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
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
		//图片公共区域
		$imgpath = __ROOT__.'/Uploads';
		//轮播
		$arr['turnpic'] = $this->table($prefix.'turnpic')
								->where('status=1')
								->field('gid,img')
								->order('addtime desc')
								->cache(true,$time,'redis')
								->select();

		foreach($arr['turnpic'] as $k=>$v){
			$arr['turnpic'][$k]['img'] = $imgpath.$v['img'];
			$arr['turnpic'][$k]['url'] = U('goods/index',['id'=>$v['gid']]);
		}

		//商品促销(低价推荐，热门商品，，本周新品，折扣爆款)
		$arr['lowerPrice'] = U('List/index',['order'=>'price desc']);
		$arr['hotGoods'] = U('List/index',['order'=>'buynum asc']);
		$arr['newGoods'] = U('List/index',['order'=>'addtime asc']);
		$arr['discount'] = U('List/index',['order'=>'discount desc']);

		//品牌盛宴
		$arr['shopBrand'] = $this->table($prefix.'brand')
								->where('status=1')
								->field('logo,brand_name brand,des,id')
								->cache(true,$time,'redis')
								->limit(18)
								->select();
		foreach ($arr['shopBrand'] as $k=>$v) {
			$arr['shopBrand'][$k]['logo'] = $imgpath.$v['logo'];
			$arr['shopBrand'][$k]['url'] = U('List/index',['brandMarke'=>$v['id']]);
		}

		//新品男装（男毛衣\男衬衫\polo衫\男裤）

		//类别
		$map['path'] = array('like','%,77,%');
		//去除热销
		$map['id'] = ['neq','109'];
		$tid = $this->table($prefix.'type')
					->cache(true,$time,'redis')
					->where($map)
					->getField('id',true);

		//通过tid去查找品牌		
		$arr['man_brand'] = $this->table($prefix.'brand')
							->join($prefix.'goods'.' on '.$prefix.'goods.bid='.$prefix.'brand.id')
							->where([$prefix.'goods.status'=>2,$prefix.'goods.tid' => ['in',$tid],'discount < 100','delsta'=>1])
							->field('logo,bid')
							->limit(6)
							->distinct(true)
							->cache(true,$time,'redis')
							->select();
		//循环遍历
		
		foreach($arr['man_brand'] as $k=>$v) {
			$arr['man_brand'][$k]['pic1'] = $imgpath.$v['logo'];
			$arr['man_brand'][$k]['url'] = U('List/index',['brandMarke'=>$v['bid']]);

		}

		//商品信息
		$arr['man'] = $this->table($prefix.'goods')
								->where(['status'=>2,'tid' => ['in',$tid],'discount < 100','delsta'=>1])
								->field('gname,discount,price,pic1,id')
								->cache(true,$time,'redis')
								->limit(9)
								->order('addtime')
								->select();

		foreach ($arr['man'] as $k=>$v) {
			//服装
			$arr['man'][$k]['pic1'] = $imgpath.$v['pic1'];
			$arr['man'][$k]['url'] = U('Goods/index',['id'=>$v['id']]);
			$arr['man'][$k]['price'] = '原价:'.$v['price'].'现价'.($v['price']*$v['discount'] /100);

		}

		//新品女装（女毛衣\女衬衫\裙子\女裤）
		$map = [];
		//类别
		$map['path'] = array('like','%,16,%');
		$tid = $this->table($prefix.'type')
					->cache(true,$time,'redis')
					->where($map)
					->getField('id',true);

		//通过tid去查找品牌		
		$arr['woman_brand'] = $this->table($prefix.'brand')
							->join($prefix.'goods'.' on '.$prefix.'goods.bid='.$prefix.'brand.id')
							->where([$prefix.'goods.status'=>2,$prefix.'goods.tid' => ['in',$tid],'discount < 100','delsta'=>1])
							->field('logo,bid')
							->limit(6)
							->distinct(true)
							->cache(true,$time,'redis')
							->select();

		//循环遍历
		foreach($arr['woman_brand'] as $k=>$v) {
			$arr['woman_brand'][$k]['pic1'] = $imgpath.$v['logo'];
			$arr['woman_brand'][$k]['url'] = U('List/index',['brandMarke'=>$v['bid']]);

		}

		//商品信息
		$arr['woman'] = $this->table($prefix.'goods')
								->where(['status'=>2,'tid' => ['in',$tid],'discount < 100','delsta'=>1,])
								->field('gname,discount,price,pic1,id')
								->cache(true,$time,'redis')
								->limit(9)
								->order('addtime')
								->select();

		foreach ($arr['woman'] as $k=>$v) {
			//服装
			$arr['woman'][$k]['pic1'] = $imgpath.$v['pic1'];
			$arr['woman'][$k]['url'] = U('Goods/index',['id'=>$v['id']]);
			$arr['woman'][$k]['price'] = '原价:'.$v['price'].'现价'.$v['price']*$v['discount']/100;

		}

		//男鞋（板鞋\运动鞋\帆布鞋\皮鞋）
		//类别
		$map['path'] = array('like','%,78,%');
		$tid = $this->table($prefix.'type')	
					->where($map)
					->cache(true,$time,'redis')
					->getField('id',true);
		// echo '<pre>';
		// 	print_r($tid);
		// echo '</pre>';

		//通过tid去查找品牌		
		$arr['manshoe_brand'] = $this->table($prefix.'brand')
							->join($prefix.'goods'.' on '.$prefix.'goods.bid='.$prefix.'brand.id')
							->where([$prefix.'goods.status'=>2,$prefix.'goods.tid' => ['in',$tid],'discount < 100','delsta'=>1])
							->field('logo,bid')
							->limit(6)
							->distinct(true)
							->cache(true,$time,'redis')
							->select();
		
		//循环遍历
		foreach($arr['manshoe_brand'] as $k=>$v) {
			$arr['manshoe_brand'][$k]['pic1'] = $imgpath.$v['logo'];
			$arr['manshoe_brand'][$k]['url'] = U('List/index',['brandMarke'=>$v['bid']]);

		}
		
		$arr['manshoe'] = $this->table($prefix.'goods')
								->where(['status'=>2,'delsta'=>1,'tid' => ['in',$tid],'discount < 100','delsta'=>1] )
								->field('gname,discount,price,pic1,id')
								->cache(true,$time,'redis')
								->limit(9)
								->order('addtime')
								->select();
		foreach ($arr['manshoe'] as $k=>$v) {
			//鞋
			$arr['manshoe'][$k]['pic1'] = $imgpath.$v['pic1'];
			$arr['manshoe'][$k]['url'] = U('Goods/index',['id'=>$v['id']]);
			$arr['manshoe'][$k]['price'] = '原价:'.$v['price'].'现价'.$v['price']*$v['discount'] / 100;

		}

		//女鞋（板鞋\运动鞋\帆布鞋\高跟鞋）
		//类别
		$map['path'] = array('like','%,100,%');
		$tid = $this->table($prefix.'type')->where($map)->getField('id',true);
		//通过tid去查找品牌		
		$arr['woshoe_brand'] = $this->table($prefix.'brand')
							->join($prefix.'goods'.' on '.$prefix.'goods.bid='.$prefix.'brand.id')
							->where([$prefix.'goods.status'=>1,$prefix.'goods.tid' => ['in',$tid],'discount < 100','delsta'=>1])
							->field('brand_name,bid')
							->limit(6)
							->select();
		//循环遍历
		foreach($arr['woshoe_brand'] as $k=>$v) {

			$arr['woshoe_brand'][$k]['url'] = U('List/index',['brandMarke'=>$v['bid']]);

		}
		
		$arr['woshoe'] = $this->table($prefix.'goods')
								->where(['status'=>2,'tid' => ['in',$tid],'discount < 100']  )
								->field('gname,discount,price,pic1')
								->cache(true,$time,'redis')
								->limit(9)
								->order('addtime')
								->select();
		foreach ($arr['woshoe'] as $k=>$v) {
			//鞋
			$arr['woshoe'][$k]['pic1'] = $imgpath.$v['pic1'];
			$arr['woshoe'][$k]['url'] = U('Goods/index',['id'=>$v['id']]);
			$arr['woshoe'][$k]['price'] = '原价:'.$v['price'].'现价'.$v['price']*$v['discount'] / 100;

		}


		//广告
		$arr['advertise'] = $this->table($prefix.'advertise')
								->where('status=1')
								->field('toUrl,img,showtime')
								->cache(true,$time,'redis')
								->find();

		
		$arr['advertise']['img'] = $imgpath.$arr['advertise']['img'];
		$arr['advertise']['toUrl'] = '//'.$arr['advertise']['toUrl'];
		return $arr;
	}

	/**
	 * 获取抢购信息列表
	 * @Author   ryan
	 * @DateTime 2017-12-05
	 * @email    931035553@qq.com
	 * @return   [type]           [description]
	 */
	public function getHotGoods() 
	{
		$goodsModel = M('Goods');

		$goodsInfo = $goodsModel->field('id,pic1')->where('tid = 116 and delsta = 1 and status != 3')->limit(6)->select();
		return $goodsInfo;
	}
}