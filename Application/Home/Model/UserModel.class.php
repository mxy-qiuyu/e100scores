<?php
namespace Home\Model;
use Think\Model;
class UserModel extends Model{
	//返回用户的详细信息，为空时返回NULL
	public function hasRegister($id){
		$User = M('User');
		$record = $User->where('id="%s"', $id)->find();
		if ($record != null){
			return true;
		}else{
			return $record;
		}
	}

	// 添加新用户
	public function addUser($openId){
		$User = M('User');
		$data['id'] = $openId;
		$data['register_time'] = date("Y-m-d H:i:s");
		$User->add($data);
	}
}
?>