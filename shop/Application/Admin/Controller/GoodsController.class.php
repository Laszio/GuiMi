<?php
namespace Admin\Controller;
use Think\Controller;
class GoodsController extends Controller {

    /**
     * 展示后台商品首页
     */
    public function index()
    {
        // 搜素条件拼接
        $str = 'delsta = 1';
        $ord = 'id desc';
        if (!empty(I('get.status'))) {
            $str .= " and status = '".I('get.status')."'";
        }
        if (!empty(I('get.gname'))) {
            $str .= " and gname like '%".I('get.gname')."%'";
        }
        if (!empty(I('get.min'))) {
            $str .= " and price >=".I("get.min");
        }
        if (!empty(I('get.max'))) {
            $str .= " and price <=".I("get.max");
        }
        if (!empty(I('get.flname'))) {
            $str .= " and tid =".I("get.flname");
        }
        if (!empty(I('get.ord'))) {
            switch (I('get.ord')) {
                case 1:
                    $ord = 'clicknum desc';
                    break;
                case 2:
                    $ord = 'clicknum';
                    break;
                case 3:
                    $ord = 'buynum desc';
                    break;
                case 4:
                    $ord = 'buynum';
                    break;
                default:
                    $ord = 'id desc';
                    break;
            }
        }
        //保存分页搜索条件
        if (!empty(I('get.ac'))) {// 判断是否是标识符。
            $str = $_COOKIE['search'];
        }
        // 记录每一次的搜素条件
        setCookie('search', $str, 0, '/');
        $good = D('Goods');

        $count = $good->order($ord)->where($str)->count();
        $Page  = new \Think\Page($count,5);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $goods = $good->order($ord)->where($str)->limit($Page->firstRow.','.$Page->listRows)->getGoods();

        $show  = $Page->show();
        if (!empty(I('get.gname'))) {
            $this->assign('ggname', I('get.gname'));
        }
        $types = $good->getTypes();
        // 分配数据
        $this->assign('types', $types);
        $this->assign('count', $count);
        $this->assign('str', $str);
        $this->assign('page',$show);
        $this->assign('goods', $goods);
        $this->display();
    }

    /**
     * 添加商品，以及展示添加页面
     */
    public function add()
    {
    	if (IS_POST) {
            $goods = D('Goods');
             //自动验证商品名、价格、折扣
            if (!$goods->create()) {
                $this->error($goods->getError());
                exit;
            }

            $data =  I('post.');

            // 执行文件上传
            $info = $this->fileUpload('/Goods/');
            if($info['status'] == 'error') {// 上传错误提示错误信息        
                $this->error($info['errorMsg']);    
                exit;
            }else{// 上传成功        
                $a = 1;
                foreach ($info as $k => $v) {
                    $data['pic'.$a] = $v['savepath'].$v['savename'];
                    $a++;
                }
            }

            // 执行添加数据操作
            if ($goods->add($data)) {
                $this->success('添加成功', U('Goods/add',['marke'=>1]));
            } else {
                $this->error('添加失败');
            }
    		exit;
    	} else {
            $goods = D('Goods');
            $types = $goods->getTypes();
            $brand = $goods->getBrand();
            $this->assign('brand', $brand);
            $this->assign('types', $types);
        	$this->display();
        }
    }

    /**
     * Ajax做商品信息假删除
     * @return [type] [description]
     */
    public function del($id) 
    {
        if (IS_AJAX) {
            $goods = M('Goods');
            $id = I('post.id');
            $data['delsta'] = 2;
            if ($goods->where('id='.$id)->save($data)) {//改变状态假删除
                $this->ajaxReturn('1');
            } else {
                $this->ajaxReturn('0');
            }
        }
    }

    /**
     * ajax做批量商品信息假删除
     * @return [type] [description]
     */
    public function delAll() 
    {
        if (IS_AJAX) {
            $goods = M('Goods');
            $id = $_GET['id'];
            $data['delsta'] = 2;
            if ($goods->where("id in($id)")->save($data)) {
                $this->ajaxReturn(1);
            } else {
                $this->ajaxReturn('0');
            }
        }
    }

    /**
     * 做商品的修改
     * @param  int $id 商品的id
     */
    public function edit($id) 
    {
        if (IS_AJAX) {
            $goods = D('Goods');
            if (!$goods->create()) {
                exit($goods->getError());
            }
            if ($goods->save($data)) {
                echo 1;
                exit;
                $this->success('修改成功');
            } else {
                
                echo $goods->_sql();exit;
            }

        } else {
            $goods = D('Goods');
            $good = $goods->getInfo();
            $types = $goods->getTypes();
            $brand = $goods->getBrand();
            $this->assign('brand', $brand);
            $this->assign('types', $types);
            $this->assign('good', $good);
            $this->display();
        }
    }

    /**
     * 做图片修改
     * @param  int $id 商品id
     */
    public function editPic($id) 
    {
        if (IS_POST) {
            $goods = M('Goods');
            $data = I('post.');
            $key = $_POST['key'];

             // 执行文件上传
            $info = $this->fileUpload('/GoodsPic/');
            if($info['status'] == 'error') {// 上传错误提示错误信息        
                $this->error($info['errorMsg']);       
                exit;
            }
            
            $info = $info[$key];
            $data[$key] = $info['savepath'].$info['savename'];
            unset($data['key']);
            if ($goods->where('id='.$_POST['id'])->save($data)) {
                $this->success('修改成功', U('Goods/index'));
                // exit;
            } else {
                $this->error('添加失败');
            }
            exit;
        }
        $goods = M('Goods');
        $good = $goods->where('id='.I('get.id'))->getField('id,pic'.I('get.marke'));
        $this->assign('good',$good);
        $this->display();
    }

    /**
     * 做商品属性修改以及展示修改页面
     * @param  int $id 商品id
     */
    public function editProperty($id) 
    {
        if (IS_POST) {
            $property = D('GoodsProperty');
            $data = $property->create();
            if ($data) {
                if ($_FILES['colorpic']['error'] !=4) {
                    $info = $this->fileUpload('/ProppertyImage/');
                    if ($info['status'] == 'error') {//文件上传不通过
                        $this->error($info['errorMsg']);
                    } else {// 添加上传后图片的数据
                        $data['colorpic'] = $info['colorpic']['savepath'].$info['colorpic']['savename'];
                    }
                }
                $res = $property->where('id='.$id)->save($data);
                if ($res) {
                    $this->success('添加成功',U('Goods/detail', ['id'=>I('get.gid')]));
                    exit;
                } else {
                    // echo $property->_sql();
                    $this->error('添加失败');
                    exit;
                }
            }
            exit;
        } else{
            $property = M('GoodsProperty');
            $goods = M('goods');
            $proInfo = $property->field('id,gid,size,color,store')->where('id='.$id)->find();
            $good = $goods->field('gname')->where('id='.$proInfo['gid'])->find();
            $this->assign('good', $good);
            $this->assign('proInfo', $proInfo);
            $this->display();
            
        }
    }

    /**
     * 添加商品描述以及展示添加页面
     */
    public function addDes($id) 
    {
        if (IS_POST) {
            if (I('post.des')) {
                if(!preg_match('/^[%°，,:：;。"0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', $_POST['des'])){ 
                    $this->error('描述请输入3~18位的字母、数字、下划线或者中文,文字中间不能包含空格哦');
                    exit;
                }
            }
            if (I('post.season')) {
                if(!preg_match('/^[%°,,0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', $_POST['season'])){ 
                    $this->error('非法字符');
                    exit;
                }
            }
            if (I('post.style')) {
                if(!preg_match('/^[%°,。.，： 0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', $_POST['style'])){ 
                    $this->error('非法字符');
                    exit;
                }
            }

            // 判断模特试穿图片是否大于4张
            if (count($_FILES['dress']['name']) >= 5) {
                $this->error('只可以上传四张模特试穿图');
                exit;
            }

            $up = new \Think\Upload();// 实例化上传类    
            $up->maxSize   =  3145728 ;// 设置附件上传大小    
            $up->exts      =  array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型    
            $up->savePath  =  '/DesImage/'; // 设置附件上传目录    // 上传单个文件
            $up->autoSub   =  false;     

            $info1   =   $up->uploadOne($_FILES['guide']);    
            if(!$info1) {// 上传错误提示错误信息    
                $this->error($up->getError());
            }else{// 上传成功 获取上传文件信息         
                unset($_FILES['guide']);    
                $data['guide'] = $info1['savepath'].$info1['savename'];
            }

            $info2   =   $up->upload();    
            if(!$info2) {// 上传错误提示错误信息  
                $this->error($up->getError());
            }else{// 上传成功 获取上传文件信息 
                $a = 1;
                foreach ($info2 as $k => $v) {
                    $data['dress'.$a] = $v['savepath'].$v['savename'];
                    $a++;
                }
            }

            // 准备数据
            $data['gid'] = $_POST['gid'];
            $data['season'] = $_POST['season'];
            $data['style'] = $_POST['style'];
            $data['des'] = $_POST['des'];

            $des = M('GoodsDes');
            $lastId = $des->add($data);
            if ($lastId) {
                vendor('XunSearch.lib.XS');
                $goods = M('Goods');
                $goodsInfo = $goods->field('gname')->where('id = '.$_POST['gid'])->find();
                $goodsInfo['season'] = $data['season'];
                $goodsInfo['id'] = $data['gid'];
                $goodsInfo['des'] = $data['des'];
                $goodsInfo['style'] = $data['style'];
                $xs = new \XS('goods'); // 创建 XS 对象，项目名称为：demo
                $index = $xs->index; 
                $doc = new \XSDocument($goodsInfo);
                $index->update($doc);
                $this->success('添加成功', U('Goods/detail', ['id'=>$data['gid']]));
            } else {
                $this->error('添加失败');
            }
            exit;
        }
        $des = M('GoodsDes');
        $goods = M('Goods');
        $desInfo = $des->field('id,gid,des,guide,,season,style,dress1,dress2,dress3,dress4')->where('id='.$id)->find();
        $good = $goods->field('gname')->where('id='.$id)->find();
        $this->assign('good', $good);
        $this->assign('desInfo', $desInfo);
        $this->display();
    }

    /**
     * 做商品属性修改以及展示页面
     * @param  int $id 商品ID
     */
    public function editDes($id) 
    {
        if (IS_POST) {
            if (I('post.des')) {
                if(!preg_match('/^[%°0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', $_POST['des'])){ 
                    $this->error('请输入3~18位的字母、数字、下划线或者中文,文字中间不能包含空格哦');
                    exit;
                }
            } else {
                unset($_POST['des']);
            }
            if (I('post.season')) {
                if(!preg_match('/^[%°,0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', $_POST['season'])){ 
                    $this->error('非法字符');
                    exit;
                }
            } else {
                unset($_POST['season']);
            }
            if (I('post.style')) {
                if(!preg_match('/^[%°,0-9a-zA-Z_\x{4e00}-\x{9fa5}]+$/u', $_POST['style'])){ 
                    $this->error('非法字符');
                    exit;
                }
            } else {
                unset($_POST['style']);
            }

            $des = M('GoodsDes');
            if ($des->save($_POST)) {
                    vendor('XunSearch.lib.XS');
                    $goods = M('Goods');
                    $goodsInfo = $goods->field('gname')->where('id = '.$_POST['gid'])->find();
                    $goodsInfo['season'] = $_POST['season'];
                    $goodsInfo['id'] = $_POST['gid'];
                    $goodsInfo['des'] = $_POST['des'];
                    $goodsInfo['style'] = $_POST['style'];
                    $xs = new \XS('goods'); // 创建 XS 对象，项目名称为：demo
                    $index = $xs->index; 
                    $doc = new \XSDocument($goodsInfo);
                    $index->update($doc);
                    exit;
                    $this->success('添加成功',U('Goods/detail', ['id'=>$_POST['gid']]));
                    exit;
                } else {
                    $this->error('添加失败或没有任何修改');
                    exit;
                }

            exit;
        }


        $des = M('GoodsDes');
        $goods = M('Goods');
        $desInfo = $des->field('id,gid,des,season,style')->where('gid='.$id)->find();
        $good = $goods->field('gname')->where('id='.$id)->find();
        $this->assign('good', $good);
        $this->assign('desInfo', $desInfo);
        $this->display();
    }

    /**
     * 做商品描述的尺寸指南图片替换
     * @param  int $id 商品描述表的id
     */
    public function editGuide($id) 
    {
        if (IS_POST) {
            $des = M('GoodsDes');

             // 执行文件上传
            $info = $this->fileUpload('/guide/');
            if($info['status'] == 'error') {// 上传错误提示错误信息        
                $this->error($info['errorMsg']);    
                exit;
            }

            // 准备数据
            $data['guide'] = $info['guide']['savepath'].$info['guide']['savename'];
            if ($des->where('id='.$_POST['id'])->save($data)) {
                $this->success('修改成功', U('Goods/detail?id='.$_POST['gid']));
            } else {
                echo $des->_sql();exit;
                $this->error('添加失败');
            }
            exit;
        }

        // 准备数据
        $goodsDes = M('GoodsDes');
        $guide = $goodsDes->field('id,guide')->where('id='.I('get.id'))->find();
        $this->assign('guide',$guide);
        $this->display();
    }

    /**
     * 商品描述表模特试穿效果图修改以及展示页面
     * @param  int $id 商品描述表的id
     */
    public function editDress($id) 
    {
        if (IS_POST) {
            $des = M('GoodsDes');

            // 执行文件上传
            $info = $this->fileUpload('/dress/');
            if($info['status'] == 'error') {// 上传错误提示错误信息        
                $this->error($info['errorMsg']);    
                exit;
            }

            // 准备数据
            $data['dress'.$_POST['marke']] = $info['dress'.$_POST['marke']]['savepath'].$info['dress'.$_POST['marke']]['savename'];
            if ($des->where('id='.$_POST['id'])->save($data)) {
                $this->success('修改成功', U('Goods/detail?id='.$_POST['gid']));
            } else {
                echo $des->_sql();exit;
                $this->error('添加失败');
            }
            exit;
        }

        // 准备页面需要的数据
        $goodsDes = M('GoodsDes');
        $dress = $goodsDes->field('id,dress'.$_GET['marke'])->where('id='.I('get.id'))->find();
        $pic = $dress['dress'.$_GET['marke']];
        $this->assign('dress',$dress);
        $this->assign('pic',$pic);
        $this->display();
    }

    /**
     * 商品的详情页面（属性和描述）
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function detail($id) 
    {
        $property = D('GoodsProperty');
        $des = M('GoodsDes');
        $proInfo = $property->getPro();
        $desInfo = $des->field('id,gid,des,guide,season,style,dress1,dress2,dress3,dress4')->where('gid='.$id)->find();
        $this->assign('proInfo', $proInfo);
        $this->assign('desInfo', $desInfo);
        $this->display();
    }

     /**
     * [addProperty 添加商品的属性]
     */
    public function addProperty($id) 
    {
        if (IS_POST) {

            $property = D('GoodsProperty');
            $data = $property->create();

            if ($data) {
                $info = $this->fileUpload('/ProppertyImage/');
                if ($info['status'] == 'error') {
                    //文件上传不通过
                    $this->error($info['errorMsg']);
                } else {
                    // 添加上传后图片的数据
                    $data['colorpic'] = $info['colorpic']['savepath'].$info['colorpic']['savename'];
                    $res = $property->add($data);
                    if ($res) {
                        $this->success('添加成功',U('Goods/detail', ['id'=>$_POST['gid']]));
                        exit;
                    } else {
                        $this->error('添加失败');
                        exit;
                    }
                }
                exit;
            } else {
                //数据验证不通过
                $this->error($property->getError());
            }
        } else {
            $goods = M('Goods');
            $good = $goods->where('id='.$_GET['id'])->field('id,gname')->find();
            $this->assign('good', $good);
            $this->display();
        }
    }

        /**
     * [fileUpload 公共的一个文件上传方法]
     * @param  [string] $path [设置文件上传目录]
     * @return [array]       [返回一个具体信息后的数组]
     */
    public function fileUpload($path='/tmp/', $maxSize=3145728, $exts= array('jpg', 'gif', 'png', 'jpeg'))
    {
        $up = new \Think\Upload();// 实例化上传类 
        $up->maxSize   =  $maxSize;// 设置附件上传大小   3M 
        $up->exts      =  $exts;// 设置附件上传类型    
        $up->savePath  =  $path; // 设置附件上传目录    // 上传文件    
        $up->autoSub   =  false;
        $info = $up->upload();
        if ($info) {
            return $info;
        } else {
            return ['status'=>'error', 'errorMsg'=>$up->getError()];
        }
    }

    public function secondKill() 
    {
        
        if (IS_AJAX) {

            $time = strtotime($_POST['time']);
            $now = $time - time();
            S('secondKillTime',null);
            // $make = S('secondKillTime');

            if (!$marke) {
                S('secondKillTime',$time, [
                    'type'=>'memcache',    
                    'host'=>'127.0.0.1',    
                    'port'=>'11211',    
                    'prefix'=>'think',    
                    'expire'=> $now]);
            }
            echo $now;
            echo '<pre>';
                print_r($make);
            echo '</pre>';
            exit;
        }
        $this->display();
    }

}