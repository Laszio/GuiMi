<?php
namespace Home\Controller;
use Think\Controller;

class  AddressController extends CommonController 
{
    public function index(){
        if (IS_POST) {
            $address = D('Address');
            if (!$address->create()) { // 验证失败
                $this->error($address->getError());
                exit;
            }
            $data = $_POST;
            unset($data['sf']);
            unset($data['city']);
            $data['uid'] = $_SESSION['userinfo']['id'];
            $data['area'] = $address->getAddressInfo();
            if ($address->add($data)) {
                if (!empty(cookie('ComeFromOrderUrl'))) {
                    $url = cookie('ComeFromOrderUrl');
                    cookie('ComeFromOrderUrl',null);
                    error('添加成功','Cart/index');
                    exit;
                }
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
            exit;
        }
        if ($_SERVER['HTTP_REFERER'] == 'http://120.78.175.126/Home/Order/index.html') {
            cookie('ComeFromOrderUrl',123);
        }
        $areas = M('Areas');
        $address = M('Address');
        $addressList = $address->order('state desc')->where('uid = '.$_SESSION['userinfo']['id'])->select();
        $list = $areas->where("`parent_id` = 1")->select();
        $this->assign('addressList', $addressList);
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * ajax获取当前地址信息
     * @return [type] [description]
     */
    public function getAddressInfo() 
    {
        if (IS_POST && !empty($_POST['id'])) {

            $address = M('Address');
            $addres = $address->field('id, name, address, code, phone')->where('id = '.$_POST['id'])->find();
            if ($addres) {
                $this->ajaxReturn($addres);
                exit;
            } 
            $this->ajaxReturn(0);
            
        }
    }

    /**
     * 做地址修改
     * @return [type] [description]
     */
    public function edit() 
    {
        if (IS_POST) {
            $address = D('Address');
            if (empty($_POST['city']) || empty($_POST['area'])) {
                unset($_POST['sf']);
                unset($_POST['city']);
                unset($_POST['area']);
            }
            if (!$address->create()) { // 验证失败
                $this->error($address->getError());
                exit;
            }
            $data = $_POST;
            if (!empty($data['city']) || !empty($data['sf'])) {
                unset($data['sf']);
                unset($data['city']);
                $data['area'] = $address->getAddressInfo();
            }
            if ($address->save($data)) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
            exit;
        }
    }

    /**
     * 做地址删除
     */
    public function del($id) 
    {
        $address = D('Address');
        if ($address->where('id='.$_GET['id'])->delete()) {
            $this->ajaxReturn('ok');exit;
        } else {
            $this->ajaxReturn(0);
        }

    }

    /**
     * 修改默认地址
     */
    public function setDefault($id) 
    {
        $address = D('Address');
        $data['state'] = 1;
        $address->where('state = 2')->save($data);
        if ($address->where('id = '.$_GET['id'])->save(['state'=>2])) {
            echo $address->_sql();
            // $this->ajaxReturn('ok');exit;
        } else {
            $this->ajaxReturn(0);
        }

    }
}