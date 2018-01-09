<?php
namespace Home\Controller;
use Think\Controller;

class HistoryController extends PublicController
{
	/**
	 * [goodsHistory 打开商品详情页调用此方法，自动记录浏览历史]
	 * @param  [int] $id [商品id]
	 */
	public function goodsHistory($id)
	{
		$goods = M('Goods');
		$data['status'] = 2;
		$data['delsta'] = 1;
		$goodsInfo = $goods->field('id, gname, price, pic1, discount')->where($data)->find($id);
		$goodsInfo['save_time'] = time();
		$uid = $_SESSION['userinfo']['id'];
		$history = unserialize($_COOKIE['history'.$uid]);

		if ($history == '') {
			//一个商品都没浏览
			$current[0] = $goodsInfo;
			cookie('history'.$uid, serialize($current), ['expire'=>3600*24*30, 'path'=>'/']);
		} else {
			//判断这个商品ID是否存在于COOKIE的商品ID
			foreach ($history as $v) {
				$ids[] = $v['id'];
			}

			//不存在COOKIE 才存进cookie
			if (!in_array($id, $ids)) {
				//得到要加入数组的新下标
				$index = count($history);

				//只记录8条记录
				if ($index > 7) {
					$arr = array_reverse($history);
					array_pop($arr);
					$history = array_reverse($arr);
					$history[7] = $goodsInfo;
					cookie('history'.$uid, serialize($history), ['expire'=>3600*24*30, 'path'=>'/']);
				} else {
					$history[$index] = $goodsInfo;
					cookie('history'.$uid, serialize($history), ['expire'=>3600*24*30, 'path'=>'/']);
					
				}	
			}
			
		}
		
	}

	/**
	 * [index 展示我的足迹]
	 */
	public function index()
	{
		// setcookie('history4', '', time()-1, '/');
		// dump($_COOKIE);exit;
		if (empty($_SESSION['userinfo']) || empty($_SESSION['ip'])) {
			$this->redirect('Login/index');
		}

		$uid = $_SESSION['userinfo']['id'];
		$history = unserialize($_COOKIE['history'.$uid]);
		$history = array_reverse($history);

		foreach ($history as $k=>$v) {
			$history[$k]['save_time'] = date('Y-m-d', $v['save_time']);
		}
		$this->assign('history', $history);
		$this->display();
	}

	/**
	 * [ajaxDelHistory 删除单条足迹]
	 */
	public function ajaxDelHistory()
	{
		if (!isset($_GET['k'])) {
			$this->ajaxReturn('非法');
		}
		$uid = $_SESSION['userinfo']['id'];
		$history = unserialize($_COOKIE['history'.$uid]);
		$history = array_reverse($history);
		// dump($history);exit;
		
		$res = array_splice($history, I('get.k'), 1);
		$res = array_reverse($history);

		cookie('history'.$uid, serialize($res), ['expire'=>3600*24*30, 'path'=>'/']);

		$this->ajaxReturn(1);
	}

}