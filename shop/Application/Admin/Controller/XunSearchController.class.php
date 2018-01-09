<?php
namespace Admin\Controller;

use \Think\Controller;

class XunSearchController extends Controller
{
	/**
	 * 添加数据库索引  --讯搜
	 */
	public function index()
	{
		vendor('XunSearch.lib.XS');
		try {
			$xs = new \XS('goods'); // 创建 XS 对象，项目名称为：demo
			$index = $xs->index; 
            $fix = C( "DB_PREFIX" );
            $goods = $fix.'goods'; 
            $des = $fix.'goods_des'; 
            $detail = $fix.'goods_property'; 

            $gg = M("Goods");
            $goodsInfo = $gg->field("$goods.id,$goods.gname,$des.des,$des.season,$des.style")
                ->join("LEFT JOIN $des on $goods.id = $des.gid")
                ->select();

			foreach ($goodsInfo as $k => $v) {
				echo '<pre>';
				    print_r($v);
				echo '</pre>';
				$doc = new \XSDocument($v);
				var_dump($index->update($doc));
			}

		} catch (XSException $e) {
			echo '出错鸟';
		}
	}

	/**
	 * 查数据
	 */
	public function getIndex() 
	{
		vendor('XunSearch.lib.XS');
		try {
			$xs = new \XS('goods');
			//管理搜索的对象
			$search = $xs->search;
			$index = $xs->index; 
			$res = $search->search('T恤');
			dump($res);
		} catch (XSException $e) {
			echo '出错鸟';
		}
	}

	public function clearIndex() 
	{
		vendor('XunSearch.lib.XS');
		try {
			$xs = new \XS('goods');
			//管理搜索的对象
			$index = $xs->index; 
			$search = $xs->search;
			$index->clean();
		} catch (XSException $e) {
			echo '出错鸟';
		}
	}
}
