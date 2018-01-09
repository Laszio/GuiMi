<?php
namespace Home\Controller;
use Think\Controller;

class ListController extends PublicController
{
	public function _initialize()
	{
		$this->HeaderCart();
		S([
			'type'=>'redis',
			'host'=>'localhost',
			'port'=>'6379',
			'expire'=>'40'
		]);
	}

	/**
	 * [index 展示商品列表页]
	 */
	public function index()
	{
		$this->firstBar();
		$list = D('List');

		//调用一个where搜索条件的方法  $_GET超全局数组，去到model层也能得到
		$map = $list->search();

		//调用一个order搜索条件的方法
		$order = $list->orderSearch();

		//有tid的页面
		if (isset($_GET['tid'])) {
			$tid = I('get.tid');
			//查看传递过来tid的自己的分类信息
			$typeInfo = $list->slefType($tid);

			//获取该分类下所有商品信息
			$typeName = $typeInfo['name'];
			$path = $typeInfo['path'];

			//这是通过tid传递的分页总条数，传个marke标记说明
			$count = $list->order($order)->where($map)->getAll($path, $tid, 'true');
			$page = new \Think\Page($count, 32);
			$page->setConfig('prev', '<<上一页');
			$page->setConfig('next', '下一页>>');
			$btn = $page->show();

			$goodsInfo = $list->cache(true, 30, 'redis')->order($order)
							->where($map)
							->limit($page->firstRow.','.$page->listRows)
							->getAll($path, $tid);

		} else if (isset($_GET['marke']) && strlen(trim($_GET['marke'])) > 0) {

			//xunsearch搜索
			Vendor('XunSearch.lib.XS');
			$xs= new \XS('goods');
			// dump($_GET);exit;
			$search = $xs->search;
			$arr = explode(' ', str_replace('+', ' ', trim(I('get.marke'))));

			foreach ($arr as $v) {
				if ($search->setQuery($v)->search()) {
					foreach ($search->setQuery($v)->search() as $val) {
						//通过中文搜索的得到得商品
						$idsTrue[] = $val->id;
					}
					$searchNameTrue[] = $v;
				} else { //非中文纠错
					if ($search->getCorrectedQuery($v)) { //获取纠错后的商品信息，结果是个索引数组，得到一个纠错后的一维数组
						
						$correctArr[] = $search->getCorrectedQuery($v)[0];
					} else { // 
						error('亲，暂无商品哦~~');
						exit;
					}
				}
				
			}

			if (!empty($idsTrue) && empty($correctArr)) {//如果是中文搜索得到，非纠错返回的进入这区间
				//全部都是中文搜索的
				$ids = array_unique($idsTrue);   // id 去重

			} else if (empty($idsTrue) && !empty($correctArr)) {
				//全部都是拼音或英文搜索
				foreach ($correctArr as $v) {
					foreach ($search->setQuery($v)->search() as $val) {
						$ids[] = $val->id;
					}
				}
				$ids = array_unique($ids);
				$correctArr = array_unique($correctArr);
				$this->assign('corrected', $correctArr);
			} else if (isset($idsTrue) && isset($correctArr) ) { 
				//中文和英文和拼音混合搜索的
				foreach ($correctArr as $v) {
					foreach ($search->setQuery($v)->search() as $val) {
						$ids[] = $val->id;
					}
				}
				$ids = array_merge($idsTrue, $ids);
				$ids = array_unique($ids);
				$searchName = array_merge($searchNameTrue, $correctArr);
				$searchName = array_unique($searchName);
				$this->assign('searchName', $searchName);
			}	

			//这是讯搜的分页总条数，也传个标记说明想要总条数
			$count = $list->order($order)->where($map)->xunSearchGoods($ids, 'true');
			$page = new \Think\Page($count, 32);
			$page->setConfig('prev', '<<上一页');
			$page->setConfig('next', '下一页>>');
			$btn = $page->show();

			$goodsInfo = $list->cache(true, 30, 'redis')
							->order($order)
							->where($map)
							->limit($page->firstRow.','.$page->listRows)
							->xunSearchGoods($ids);

		} else if (isset($_GET['brandMarke'])) {
			//第三区间是通过点击品牌进来的
			$bid = I('brandMarke');
			$count = $list->order($order)->where($map)->brandMarke($bid, 'true');
			$page = new \Think\Page($count, 32);
			$page->setConfig('prev', '<<上一页');
			$page->setConfig('next', '下一页>>');
			$btn = $page->show();

			$goodsInfo = $list->cache(true, 30, 'redis')
							->order($order)
							->where($map)
							->limit($page->firstRow.','.$page->listRows)
							->brandMarke($bid);
		} else if (empty($_GET['marke'])) {
			error('请搜索商品哦~');
			exit;
		}



		if ($goodsInfo) {

			//获取搜索出来的所有商品的所有品牌的信息
			$brandAll = $list->getBrand($goodsInfo);
			if (isset($_GET['bid']) && strlen(trim($_GET['bid'])) > 0) {
				foreach ($brandAll as $v) {
					if ($v['id'] == I('get.bid')) {
						$brandName = $v['brand_name'];
					}
				}
			}

			// 根据首次选中的商品搜索条件， 进行动态替换， 具体效果 看搜索前后对比

			//查出所有商品的对应的颜色
			$color = $list->getColor($goodsInfo);
			//查出所有商品的style
			$style = $list->getStyle($goodsInfo);

			//查出所有商品的码数
			$size = $list->getSize($goodsInfo);

		} else {
			error('亲，暂无商品哦~');
			exit;
		}

		//商品热销
		$host = $list->hostSale();

		if (!empty($typeName)) $this->assign('typeName', $typeName);
		if (!empty($brandName)) $this->assign('brandName', $brandName);

		// 最后可能商品有重复，去重,, 二维数组去重 很重要!!!!
		$arr = [];
		$result = [];
		foreach ($goodsInfo as $k=>$v) {
			if (!in_array($v['id'], $arr)) {
				$arr[] = $v['id'];
				$result[] = $v;
			}
		}
		$this->assign('list', $result);
		$this->assign('brand', $brandAll);
		$this->assign('btn', $btn);
		$this->assign('color', $color);
		$this->assign('style', $style);
		$this->assign('size', $size);
		$this->assign('host', $host);
		$this->display();
		
	}	

	/**
	 * [ajaxFindBrand 悬浮在品牌上的ajax]
	 * @return [array] [所有的品牌信息]
	 */
	public function ajaxFindBrand()
	{
		if (IS_AJAX) {
			$table = C("DB_PREFIX").'goods';
			$brand = M('brand');
			$goods = M('goods');
			$data[$table.'.status'] = 2;
			$data['delsta'] = 1;
			$ids = $goods->where($data)
						->join('LEFT JOIN __BRAND__ on  __GOODS__.bid = __BRAND__.id')
						->getField('bid', true);
			if ($ids) {
				$map['status'] = 1;
				$map['id'] = ['in', $ids]; 
				$brandInfo = $brand->field('id, brand_name, logo')->where($map)->select();
				$this->ajaxReturn($brandInfo);
			}	
		} else {
			error('非法操作');
			exit;
		}
	}



}



