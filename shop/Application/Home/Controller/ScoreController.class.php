<?php
namespace Home\Controller;
use Think\Controller;

class ScoreController extends PublicController
{
	/**
	 * [index 展示个人积分首页]
	 */
	public function index()
	{
		if (IS_GET) {
			if (empty($_SESSION['userinfo']) || empty($_SESSION['ip'])) {
				$this->redirect('Login/index');
			}

			$user = M('User');
			$data['status'] = 1;
			$score = $user->field('score,today_qd')->where($data)->find($_SESSION['userinfo']['id']);
			if (!$score) {
				error('非法操作');
				exit;
			}
			
			if ($score['score'] > 0 && $score['score'] < 200) {
				$score['vip_img'] = '<img style="wdith:20px;height:20px;" src="'.__ROOT__.'/Uploads/qiandao/VIP-1.png">';
			} else if ($score['score'] >= 200 && $score['score'] < 500) {
				$score['vip_img'] = '<img style="wdith:20px;height:20px;" src="'.__ROOT__.'/Uploads/qiandao/VIP-2.png">';
			} else if ($score['score'] >= 500) {
				$score['vip_img'] = '<img style="wdith:20px;height:20px;" src="'.__ROOT__.'/Uploads/qiandao/VIP-3.png">';
			}
			
			$this->assign('score', $score);
			$this->display();
		}
	}

	/**
	 * [ajaxSetInc ajax修改签到积分]
	 */
	public function ajaxSetInc()
	{
		if (IS_AJAX) {
			if (empty($_SESSION['userinfo']) || empty($_SESSION['ip'])) {
				$this->redirect('Login/index');
			}

			$user = M('User');
			$data['id'] = $_SESSION['userinfo']['id'];
			$data['today_qd'] = 1;
			$res = $user->where('id='.$_SESSION['userinfo']['id'])->setInc('score', 10);
			if ($res) {
				$result = $user->save($data);
				if ($result) {
					$score = $user->where('id='.$_SESSION['userinfo']['id'])->getField('score');
					if ($score) {
						$this->ajaxReturn(['status'=>1, 'score'=>$score]);
					}
				}
				
			}
		} else {
			return false;
		}
	}

	/**
	 * [ajaxSaveSta 定时器发送ajax重置]
	 */
	public function ajaxSaveSta()
	{
		if (IS_AJAX) {
			$user = M('User');
			$data['id'] = $_SESSION['userinfo']['id'];
			$data['today_qd'] = 0;
			$res = $user->save($data);
			if ($res) {
				$this->ajaxReturn(1);
			}
		} else {
			return false;
		}
	}
}