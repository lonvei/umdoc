<?php
namespace Home\Controller;
use Think\Controller;
class DocumentController extends Controller {
    public function index(){
        $this->show("优盟帮助");
    }

    public function dlist(){
        //分类
        $catelist = M("Category")->select();
        $this->assign("catelist",$catelist);

        $map = array();
        $id = I("get.id");
        if(!empty($id)) $map["cate_id"] = $id;
        $count = M("Document")->where($map)->count();

        $limit=20;
        $page=new \Think\Page($count,$limit);
        //$page -> setConfig('prev','<i class="left arrow icon"></i>');
        //$page -> setConfig('next','<i class="right arrow icon"></i>');

        $list = M("Document")->where($map)->order("id desc")->limit($page->firstRow.','.$page->listRows)->select();
        $pagebar = $page->show();
        $this->assign("pagebar",$pagebar);

        $this->assign("cate_id",$id);
        $this->assign("list",$list);
        $this->display();
    }

    public function view($id){
        if(empty($id))  $this->show("找不到你要的文章");
        //分类
        $catelist = M("Category")->select();
        $this->assign("catelist",$catelist);


        $data = M("Document")->where("id=$id")->find();
        $this->assign($data);
        $this->display();
    }
}