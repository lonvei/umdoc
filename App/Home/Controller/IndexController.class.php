<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        //分类
        $catelist = M("Category")->order("displayorder desc,cate_id asc")->select();
        $this->assign('catelist',$catelist);

        //$list = M("Document")->field(array("user_id","content"),true)->where($map)->order("id desc")->limit(10)->select();
        //$this->assign('list',$list);

        $this->display();
    }
}