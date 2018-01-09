<?php
namespace Home\Controller;
use Think\Controller;

class CommentController extends PublicController
{
	/**
	 * [sendComment 发表评论]
	 * @param  string $odetail_id [所要评论的商品对应的订单详情]
	 */
	public function sendComment($odetail_id='')
	{
		if (IS_GET) {
			if (empty($odetail_id)) {
				error('非法操作');
				exit;
			}

			if (empty($_SESSION['userinfo']) || empty($_SESSION['ip'])) {
				$this->redirect('Login/index');
				exit;
			}
			
			$comment = D('Comment');
			$res = $comment->getOrderDetail($odetail_id);
			
			if (empty($res)) {
				$this->redirect('User/index');
			}

			$this->assign('comment', $res[0]);
			$this->display(); 
			
		} else if (IS_POST) {
			$comment = D('Comment');
			$info = $comment->fileUpload();
			if ($info['status'] == 'error') {
				if ($info['errorMsg'] != '没有文件被上传！') {
					error($info['errorMsg']);
					exit;			
				}
			} 

			if (count($info) > 3) {
				error('最多只能上传三张图片');
				exit;
			}
			
			
			$data = $comment->create();
			if ($data) {
				if ($info && $info['errorMsg'] != '没有文件被上传！') {
					foreach ($info as $v) {
						$arr[] = $v['savepath'].$v['savename'];
					}
					$data['cimg1'] = $arr[0];
					$data['cimg2'] = $arr[1];
					$data['cimg3'] = $arr[2];		
				}


				$data['uid'] = $_SESSION['userinfo']['id'];

				$lastInsertId = $comment->add($data);
				if ($lastInsertId) {
					$res = M('User')->where('id='.$_SESSION['userinfo']['id'])->setInc('score', 100);
					if ($res) {
						$orderRes = M('OrderDetail')->where('id='.$odetail_id)->save(['status'=>3]);
						if ($orderRes) {
							
							success('发表成功', 'User/index');
							exit;
						} else {
							error('订单详情修改失败');
							exit;
						}
						
					} else {
						error('添加积分失败');
						exit;
					}
				} else {
					error('发表失败');
					exit;
				}


			} else {
				error($comment->getError());
				exit;
			}
			
		}
	}


	/**
	 * [seeComment 查看某个商品的评价]
	 * @param  [int] $gid [商品id]
	 * @return [array]      [存储评价的具体信息]
	 */
	public function seeComment($gid)
	{
		$comment = D('Comment');
		$data['gid'] = $gid;
		$count =$comment->where($data)->count();
		$page = new \Think\Page($count, 2);
		$page->setConfig('prev', '上一页');
		$page->setConfig('next', '下一页');
		$btn = $page->show();
		$commentInfo = $comment->limit($page->firstRow, $page->listRows)->allComment($gid);
		$commentInfo['btn'] = $btn;
		return $commentInfo;
	}
}


