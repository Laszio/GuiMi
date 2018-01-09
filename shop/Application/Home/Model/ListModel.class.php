<?php
namespace Home\Model;
use Think\Model;

class ListModel extends Model
{

	protected $tableName = 'goods';

	public function _initialize()
	{
		S([
			'type'=>'redis',
			'host'=>'localhost',
			'port'=>'6379',
			'expire'=>'40'
		]);
	}

	/**
	 * [getAll 获取分类下的所有商品信息]
	 * @param  [string] $path [自己的path]
	 * @param  [int] $tid  [分类id]
	 * @param  [string] $countMarke  [统计总条数的切换标记]
	 * @return [array]       [存储所有商品的信息或者存储对应总条数]
	 */
	public function getAll($path, $tid, $countMarke="")
	{
		//根据get.tid的类别条件
		$res = $this->getTypes($path, $tid);
		if ($res) {
			//拿到所有的子类ID
			foreach ($res as $k=>$v) {
				$typeIds[] = $v['id'];
			}
			$data['tid'] = ['in', $typeIds];
			
		} else {
			//无子类
			$data['tid'] = I('get.tid');
		}

		$data['status'] = 2;
		$data['delsta'] = 1;

		$table = C("DB_PREFIX").'goods';
		if (empty($countMarke)) {
			
			$list = $this->field(''.$table.'.id,tid,bid,gname, discount, price, pic1, buynum, clicknum, '.$table.'.addtime, season, style, color, size')
						->join('LEFT JOIN __GOODS_DES__ on __GOODS__.id = __GOODS_DES__.gid')
						->join('LEFT JOIN __GOODS_PROPERTY__ on __GOODS__.id = __GOODS_PROPERTY__.gid')
						->where($data)->select();
		} else {
			$list = $this->join('LEFT JOIN __GOODS_DES__ on __GOODS__.id = __GOODS_DES__.gid')
						->join('LEFT JOIN __GOODS_PROPERTY__ on __GOODS__.id = __GOODS_PROPERTY__.gid')
						->where($data)->count();
		}
		return $list;
	}

	/**
	 * [getTypes 获取一个父类下的所有子类的信息]
	 * @param  [string] $path [父类path]
	 * @param  [int] $tid  [父类ID]
	 * @return [array]       [存储所有子类的信息的数组]
	 */
	public function getTypes($path, $tid)
	{
		$type = M('Type');
		$map['status'] = 1;
		$map['path'] = ['like', $path.$tid.',%'];
		$res = $type->field('id, name,path,attr')->where($map)->select();
		return $res;	

	}

	/**
	 * [getBrand 根据某个分类下的所有商品信息查所有品牌信息]
	 * @param  [array] $goodsInfo [存储商品信息的数组]
	 * @return [array]           [存储品牌的具体信息]
	 */
	public function getBrand($goodsInfo)
	{

		$brand = M('Brand');
		foreach ($goodsInfo as $v) {
			$brandIds[] = $v['bid'];
		}
		$brandIds = array_unique($brandIds);



		$data['id'] = ['in', $brandIds];
		$data['status'] = 1;
		$brandAll = $brand->field('id, brand_name, logo')->where($data)->select();
		return $brandAll;
	}

	/**
	 * [slefType 获取自己的分类的具体信息]
	 * @param  [int] $tid [分类id]
	 * @return [array]      [存储某条分类的具体信息的数组]
	 */
	public function slefType($tid)
	{
		$type = M('Type');
		// //查该分类具体信息
		$data['id'] = $tid;
		$data['status'] = 1;
		$typeInfo = $type->field('id,name,path')->where($data)->find();
		if (!$typeInfo) {
			error('非法操作');
			exit;
		}
		return $typeInfo;
	}

	/**
	 * [search 商品搜索条件]
	 * @return [array] [存储商品搜索条件的数组]
	 */
	public function search()
	{
		if (isset($_GET['bid']) && strlen(trim($_GET['bid'])) > 0) {
			$map['bid'] = I('get.bid');
		}

		if (isset($_GET['price']) && strlen(trim($_GET['price'])) > 0) {

			if (preg_match('/-/', $_GET['price'])) {
				$arr = explode('-', $_GET['price']);
				$map['price'] = ['between', [$arr[0], $arr[1]]];
			} else if (preg_match('/大于/', $_GET['price'])) {
				$arr = explode('大于', $_GET['price']);
				$map['price'] = ['gt', $arr[1]];
			}
		}

		if (isset($_GET['season']) && strlen(trim($_GET['season'])) > 0) {
			$str = I('get.season');
			$time = substr($str, 0, 4);
			$arr = explode($time, $str);
			
			$season = $arr[1];
			$map['season'] = $season;
			$table = C("DB_PREFIX").'goods';
			$map[''.$table.'.addtime'] = ['like', '%'.$time.'%'];
		}

		if (isset($_GET['color']) && strlen(trim($_GET['color'])) > 0) {
			$map['color'] = ['like', '%'.I('get.color').'%'];
		}

		if (isset($_GET['style']) && strlen(trim($_GET['style'])) > 0) {
			$map['style'] = ['like', '%'.I('get.style').'%'];
		}
		if (isset($_GET['size']) && strlen(trim($_GET['size'])) > 0) {
			$map['size'] = I('get.size');
		}
		
		if (isset($_GET['pricemin']) && strlen(trim($_GET['pricemin'])) > 0) {

			if (isset($_GET['pricemin']) && strlen(trim($_GET['pricemax'])) > 0) {
				
				$map['price'] = ['between', [$_GET['pricemin'], $_GET['pricemax']]];
			} else if (isset($_GET['pricemin']) && empty($_GET['priemax'])) {
				$map['price'] = ['egt', $_GET['pricemin']];
			}
			
		}

		if (isset($_GET['pricemax']) && strlen(trim($_GET['pricemax'])) > 0) {
			if (isset($_GET['pricemax']) && empty($_GET['pricemin'])) {
				$map['price'] = ['elt', $_GET['pricemax']];
			}
		}
		$map['status'] = 2;
		$map['delsta'] = 1;
		return $map;
	}

	/**
	 * [orderSearch 排序的搜索条件处理]
	 * @return [string] [具体得到的排序条件]
	 */
	public function orderSearch()
	{
		$order = '';
		if (isset($_GET['buynumdown'])) {
			$order = 'buynum desc';
		} else if (isset($_GET['pricedown'])) {
			$order = 'price desc';
		} else if (isset($_GET['clickdown'])) {
			$order = 'clicknum desc';
		} else if (isset($_GET['timedown'])) {
			$table = C("DB_PREFIX").'goods';
			$order = $table.'.addtime desc';
		}
		return $order;
	}

	/**
	 * [getStyle 得到所有商品对应风格]
	 * @param  [array] $goodsInfo [商品信息]
	 * @return [array]            [存储对应的商品风格]
	 */
	public function getStyle($goodsInfo)
	{
		foreach ($goodsInfo as $v) {
			$style[] = trim($v['style']);
		}
		$style = array_unique($style);
		return $style;

	}

	/**
	 * [getColor 得到商品对应的颜色]
	 * @param  [array] $goodsInfo [商品信息]
	 * @return [array]            [存储对应的商品颜色]
	 */
	public function getColor($goodsInfo)
	{
		foreach ($goodsInfo as $v) {
			$color[] = trim($v['color']);
		}
		$color = array_unique($color);
		return $color;
	}

	/**
	 * [getSize 得到商品对应的码数]
	 * @param  [array] $goodsInfo [商品信息]
	 * @return [array]            [存储对应的商品码数]
	 */
	public function getSize($goodsInfo)
	{
		foreach ($goodsInfo as $v) {
			$size[] = strtoupper(trim($v['size']));
		}
		$size = array_unique($size);
		return $size;
	}

	/**
	 * [hostSale 商品热销模块]
	 * @return [array] [存储热销榜前五的商品的信息]
	 */
	public function hostSale()
	{
		if (S('hostGoods')) {
			return S('hostGoods');
		} else {
			
			$goods = M('goods');
			$data['status'] = 2;
			$data['delsta'] = 1;
			$host = $goods->field('id, gname, price,pic1, addtime')->order('buynum desc')->where($data)->limit(5)->select();
				
			S('hostGoods', $host, 100);
			return $host;
		}

	}

	/**
	 * [xunSearchGoods 通过讯搜查询商品]
	 * @param  [string] $ids        [存储商品id的集合]
	 * @param  string $countMarke [切换统计总条数的标记]
	 * @return [array]             [存储商品信息或商品总条数]
	 */
	public function xunSearchGoods($ids, $countMarke="")
	{
		$table = C("DB_PREFIX").'goods';
		$data['status'] = 2;
		$data['delsta'] = 1;
		$data[$table.'.id'] = ['in', $ids];
		if (empty($countMarke)) {
			$list = $this->field(''.$table.'.id,tid,bid,gname, discount, price, pic1, buynum, clicknum, '.$table.'.addtime, season, style, color, size')
						->join('LEFT JOIN __GOODS_DES__ on __GOODS__.id = __GOODS_DES__.gid')
						->join('LEFT JOIN __GOODS_PROPERTY__ on __GOODS__.id = __GOODS_PROPERTY__.gid')
						->where($data)->select();		
		} else {
			$list = $this->join('LEFT JOIN __GOODS_DES__ on __GOODS__.id = __GOODS_DES__.gid')
						->join('LEFT JOIN __GOODS_PROPERTY__ on __GOODS__.id = __GOODS_PROPERTY__.gid')
						->where($data)->count();
		}
		return $list;
	}

	/**
	 * [brandMarke 通过点击品牌搜索相应商品]
	 * @param  [int] $bid        [品牌id]
	 * @param  string $countMarke [切换统计总条数的标记]
	 * @return [array]             [存储商品信息或者总条数]
	 */
	public function brandMarke($bid, $countMarke="")
	{

		$table = C("DB_PREFIX").'goods';
		$data['status'] = 2;
		$data['delsta'] = 1;
		$data['bid'] = $bid;

		if (empty($countMarke)) {
			$list = $this->field(''.$table.'.id,tid,bid,gname, discount, price, pic1, buynum, clicknum, '.$table.'.addtime, season, style, color, size')
						->join('LEFT JOIN __GOODS_DES__ on __GOODS__.id = __GOODS_DES__.gid')
						->join('LEFT JOIN __GOODS_PROPERTY__ on __GOODS__.id = __GOODS_PROPERTY__.gid')
						->where($data)->select();
		} else {
			$list = $this->join('LEFT JOIN __GOODS_DES__ on __GOODS__.id = __GOODS_DES__.gid')
						->join('LEFT JOIN __GOODS_PROPERTY__ on __GOODS__.id = __GOODS_PROPERTY__.gid')
						->where($data)->count();
		}
		return $list;
	}	


}

