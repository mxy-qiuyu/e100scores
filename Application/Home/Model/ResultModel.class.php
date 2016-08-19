<?php
namespace Home\Model;
use Think\Model;
class ResultModel extends Model{
	//通过主键获取答题数据，查找不到时返回null
	public function getRecordById($id){
		$Result = M('Result');
		$record = $Result->where('id="%d" AND type=0', $id)->find();
		$record['answer'] = unserialize($record['answer']);
		return $record;
	}

	//通过用户及题库id获取答题数据，查找不到时返回null
	public function getRecordBySearch($userId, $bankId){
		$Result = M('Result');
		$record = $Result->where("user_id='%s' AND bank_id='%d' AND type=0", $userId, $bankId)->find();
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
            $data['type'] = 0;
		}else{
			$data['id'] = $record['id'];
		}
		$data['answer'] = serialize($data['answer']);
		$data['update_time'] = date("Y-m-d H:i:s");
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

	public function deleteRecord($userId, $bankId){
		$Result = M('Result');
		$result = $Result->where("user_id='%s' AND bank_id='%d'AND type=0", $userId, $bankId)->delete();
		return $result;
	}

    //通过主键获取答题数据，查找不到时返回null
    public function getRecordByIdFavorite($favoriteId){
        $Result = M('Result');
        $record = $Result->where('id="%d" AND type=1', $favoriteId)->find();
        $record['answer'] = unserialize($record['answer']);
        return $record;
    }

    //通过用户及题库id获取答题数据，查找不到时返回null
    public function getRecordBySearchForFavorite($userId, $favoriteId){
        $Result = M('Result');
        $record = $Result->where("user_id='%s' AND bank_id='%d' AND type=1", $userId, $favoriteId)->find();
        if ($record != null){
            $record['answer'] = unserialize($record['answer']);
        }
        return $record;
    }

    //保存用户答题数据
    public function saveRecordForFavorite($userId, $favoriteId, $data){
        $Result = M('Result');
        $record = $this->getRecordBySearchForFavorite($userId, $favoriteId);
        if ($record == null){
            $data['user_id'] = $userId;
            $data['bank_id'] = $favoriteId;
            $data['type'] = 1;
        }else{
            $data['id'] = $record['id'];
        }
        $data['answer'] = serialize($data['answer']);
        $data['update_time'] = date("Y-m-d H:i:s");
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

    public function getLastNumForFavorite($userId,$favoriteId){
        $record=$this->getRecordBySearchForFavorite($userId, $favoriteId);
        if($record=NULL){
            return 0;
        }else{
            return $record['completed'];
        }
    }

    public function getAnswerByNumForFavorite($userId,$favoriteId,$number){
        $record=$this->getRecordBySearchForFavorite($userId, $favoriteId);
        if ($record==NULL && sizeof($record)<$number){//结果不为空且结果中有存储该题号的答案
            return $record['answer'][$number-1];
        }else{
            return NULL;
        }
    }

    //清除答题记录
    public function deleteRecordForFavorite($userId, $favoriteId){
        $Result = M('Result');
        $result = $Result->where("user_id='%s' AND bank_id='%d'AND type=1", $userId, $favoriteId)->delete();
        return $result;
    }
}
?>