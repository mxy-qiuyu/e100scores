<?php
namespace Home\Model;
use Think\Model;
class ResultModel extends Model{
	//通过主键获取答题数据，查找不到时返回null
	public function getRecordById($id){
		$Result = M('Result');
		$record = $Result->where('id="%d"', $id)->find();
		$record['answer'] = unserialize($record['answer']);
		return $record;
	}

	//通过用户及题库id获取答题数据，查找不到时返回null
	public function getRecordBySearch($userId, $bankId){
		$Result = M('Result');
		$record = $Result->where("user_id='%s' AND bank_id='%d'", $userId, $bankId)->find();
		if ($record != null){
			$record['answer'] = unserialize($record['answer']);
		}
		return $record;
	}

	//保存用户答题数据
	public function saveRecord($userId, $bankId, $data){
		$Result = M('Result');
		$record = $this->getRecordBySearch($userId, $bankId);
		if ($record == null){
			$data['user_id'] = $userId;
			$data['bank_id'] = $bankId;
		}else{
			$data['id'] = $record['id'];
		}
		$data['answer'] = serialize($data['answer']);
		if (!isset($data['update_time'])){
			$data['update_time'] = date("Y-m-d H:i:s");			
		}
		if ($Result->create($data)){
			if ($record == null){
				$Result->add();
			}else{
				$Result->save();
			}
			return true;
		}else{
			return false;
		}
	}

	// 删除用户指定题库的答题记录
	public function deleteRecord($userId, $bankId){
		$Result = M('Result');
		$result = $Result->where("user_id='%s' AND bank_id='%d'", $userId, $bankId)->delete();
		return $result;
	}
}
?>