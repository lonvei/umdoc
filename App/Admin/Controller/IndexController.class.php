<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function _initialize(){
        $userid = session('userid');
        if(empty($userid)) $this->redirect('Login/index');
    }
    
    public function index(){
        //分类
        $cate = M("Category")->order("displayorder desc,cate_id asc")->select();
        $this->assign('cate',$cate);


        $map = array();
        // if(session("groupid")!=1) $map['user_id'] = session("userid");
        $count = M("Document")->where($map)->count();

        $limit=20;
        $page=new \Think\Page($count,$limit);

        $list = M("Document")->field(array("user_id","content"),true)->where($map)->order("id desc")->limit($page->firstRow.','.$page->listRows)->select();
        $list_json = json_encode($list);

        $this->assign('count',$count);
        $this->assign('list',$list_json);
        $this->display();
    }

    public function ajax_list(){
        $map = array();
        // if(session("groupid")!=1) $map['user_id'] = session("userid");
        if(I("get.cate_id")) $map['cate_id'] = I("get.cate_id");
        if(I("get.key")) $map['title'] = array("LIKE","%".I("get.key")."%");
        $count = M("Document")->where($map)->count();
        $limit=20;
        $page=new \Think\Page($count,$limit);
        $list = M("Document")->field(array("user_id","content"),true)->where($map)->order("id desc")->limit($page->firstRow.','.$page->listRows)->select();
        $this->ajaxReturn($list);
    }
	
    public function update(){
        if(!IS_POST) $this->error("操作失败");
        
        $data = I("post.");
        //var_dump($data);
        
        $data['content'] = stripslashes($data['content']);
        $u = M("Document");
        $u->create($data);
        if($data['id']>0){
            $u->update_time=time();
            $res = $u->save();
            if($res){
                $res_data = array("status"=>1,"info"=>"保存成功");
            }else{
                $res_data = array("status"=>0,"info"=>"保存失败");
            }
        }else{
            $u->create_time = $u->update_time = time();
            $u->status=1;
            $u->user_id=session("userid");
            $res = $u->add();
            if($res){
                $res_data = array("status"=>1,"info"=>"添加成功","data"=>array("id"=>$res,"title"=>$data['title']));
            }else{
                $res_data = array("status"=>0,"info"=>"添加失败","data"=>array("id"=>$res));
            }
        }
        $this->ajaxReturn($res_data);
        //$this->success("操作成功");
    }
	
	public function docedit($id=0){
		$data = M("Document")->where("id=$id")->find();
		$this->assign($data);
		$this->display();
	}
	
    public function details($id){
        $data = M("Document")->where("id=$id")->find();
        $data['content']=htmlspecialchars_decode($data['content']);
        $this->ajaxReturn($data);
    }

    public function delete($id){
        $res = M("Document")->where("id=$id")->delete();
        if($res){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }

    public function cate_update(){
        $data = I("post.");
        $u = M("category");
        $u->create($data);
        if(empty($data['cate_id'])){
            $u->status = 1;
            $res = $u->add();
        }else{
            $res = $u->save();
        }
        if($res){
            $this->success("保存成功");
        }else{
            $this->error("保存失败");
        }
    }
	
	public function Upload(){
		$upload = new \Think\Upload();// 实例化上传类
		$upload->maxSize   =     3145728 ;// 设置附件上传大小
		$upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
		$upload->rootPath  =     './Uploads/'; // 设置附件上传根目录
		$upload->autoSub   =	true;
		$upload->subName  =     array('date','Ymd'); // 设置附件上传（子）目录
		$upload->saveName  =    array('uniqid','');
		// 上传文件 
		//$info   =   $upload->uploadOne($_FILES['wangEditorH5File']);
		$info   =   $upload->upload();
		//die(var_dump($info));
		if(!$info) {
			echo "error|服务器端错误";
		}else{
			foreach($info as $v){
				echo "/Uploads/".$v['savepath'].$v['savename'];
				die();
			}
		}
	}
}