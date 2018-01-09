<?php
namespace Home\Model;
use Think\Model;

class CommentModel extends Model
{

	protected $_validate = [
		['level', 'require', '评论等级不能为空'],
		['gid', 'require', '非法操作'],
		['odetail_id', 'require', '非法操作'],
		['content', 'require', '评论内容不能为空'],
		['content', '/^[a-zA-Z0-9_\x{4e00}-\x{9fa5}]{3,100}$/u', '请输入3到100个数字、字母、下划线、或中文'],
	];

    public function _initialize()
    {
        S([
            'type'=>'redis',
            'host'=>'localhost',
            'port'=>'6379',
            'expire'=>'30'
        ]);
    }

	/**
	 * [getOrderDetail 获取所要评价的商品的详情]
	 * @param  [int] $odetail_id [订单详情表的id]
	 * @return [array]             [存储商品对应某条订单详情的信息]
	 */
	public function getOrderDetail($odetail_id)
	{
		$order_detail = M('order_detail');
		$fix = C("DB_PREFIX");
		$data[$fix.'order_detail.status'] = 1;

		//订单表的状态必须为4
		$data[$fix.'order.status'] = 4;
		$data[$fix.'order_detail.id'] = $odetail_id;
		$data[$fix.'order.uid'] = $_SESSION['userinfo']['id'];
		$res = $order_detail->field($fix.'order_detail.id, gid,propid, oid, num, '.$fix.'order_detail.price, total, '.$fix.'order_detail.gname, color, size, '.$fix.'order_detail.addtime, pic1, '.$fix.'order.uid')->where($data)
					->join('LEFT JOIN __GOODS__ on __ORDER_DETAIL__.gid = __GOODS__.id')
					->join('LEFT JOIN __ORDER__ on __ORDER_DETAIL__.oid = __ORDER__.id')
					->select();

		return $res;
	}

	/**
     * [fileUpload 公共的一个文件上传方法]
     * @param  [string] $path [设置文件上传目录]
     * @return [array]       [返回一个具体信息后的数组]
     */
    public function fileUpload($maxSize=3145728, $exts= array('jpg', 'gif', 'png', 'jpeg'))
    {
        $up = new \Think\Upload();// 实例化上传类 
        $up->maxSize   =  $maxSize;// 设置附件上传大小   3M 
        $up->exts      =  $exts;// 设置附件上传类型    
        $up->savePath  =  '/Comment/'; // 设置附件上传目录    // 上传文件    
        $up->autoSub   =  false;
        $info = $up->upload();
        if ($info) {
            return $info;
        } else {
            return ['status'=>'error', 'errorMsg'=>$up->getError()];
        }
    }

    /**
     * [allComment 处理查看商品评价的数据]
     * @param  [int] $gid [商品ID]
     * @return [array]      [处理后的商品评价信息]
     */
    public function allComment($gid)
    {
    	$fix = C("DB_PREFIX");
    	$data[$fix.'comment.status'] = 2;
    	$data[$fix.'comment.gid'] = $gid;
    	$res = $this->field($fix.'comment.id, '.$fix.'comment.uid, '.$fix.'comment.gid,odetail_id,content,level,'.$fix.'comment.addtime,cimg1,cimg2,cimg3, size,color, username, user_img, qq_openid, nikname')
    				->where($data)
    				->join('LEFT JOIN __ORDER_DETAIL__ on __COMMENT__.odetail_id = __ORDER_DETAIL__.id')
    				->join('LEFT JOIN __USER__ on __COMMENT__.uid = __USER__.id')
    				->join('LEFT JOIN __USER_DETAIL__ on __COMMENT__.uid = __USER_DETAIL__.uid')
    				->cache(true, 30, 'redis')->select();


    	$level = ['', '好评', '中评', '差评'];
    	$level_img = ['', '/CommentLogo/good.png', '/CommentLogo/middle.png', '/CommentLogo/bad.png'];
    	foreach ($res as $k=>$v) {
    		$res[$k]['level'] = $level[$v['level']];
    		$res[$k]['level_img'] = $level_img[$v['level']];

            if (isset($v['cimg1'])) {
               $res[$k]['img1'] = '<img style="width:50px;height:50px;" src="'.__ROOT__.'/Uploads'.$v['cimg1'].'" alt="">';
            } else {
                 $res[$k]['img1'] = '';
            }

            if (isset($v['cimg2'])) {
                $res[$k]['img2'] = '<img style="width:50px;height:50px;" src="'.__ROOT__.'/Uploads'.$v['cimg2'].'" alt="">';
            } else {
                $res[$k]['img2'] = '';
            }

            if (isset($v['cimg3'])) {
                $res[$k]['img3'] = '<img style="width:50px;height:50px;" src="'.__ROOT__.'/Uploads'.$v['cimg3'].'" alt="">';;
            } else {
                 $res[$k]['img3'] = '';
            }

            if (isset($v['qq_openid'])) {
                //qq用户
                $res[$k]['user_img'] = $v['user_img'];
            } else {
                //其他用户
                $res[$k]['user_img'] = __ROOT__.'/Uploads'.$v['user_img'];
            }
    	}
    	return $res;

    }

}
