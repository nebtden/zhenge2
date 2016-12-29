<?php
namespace Common\Model;
use Common\Model\CommonModel;

class MembersModel extends CommonModel{
	
    public function createMembers($open_id,$info){
        $menber_info = $this->where(array('open_id'=>$open_id))->find();
        $data=array();

        $data['name']  =$info['name'];
        $data['address']  =$info['address'];
        $data['email']  =$info['email'];
        $data['telephone']  =$info['telephone'];
        if(!$menber_info){
            //创建用户
            $data['open_id'] = $open_id;
            $res= $this->create($data);

        }else{
            //更新用户
            $this->where(array('open_id'=>$open_id))->update($data);
        }

    }
	
}




