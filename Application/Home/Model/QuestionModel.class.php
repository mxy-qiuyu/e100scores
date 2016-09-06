<?php
namespace Home\Model;
use Think\Model;
class QuestionModel extends Model
{
	//返回指定题库下的题目列表,为空时返回NULL
	public function getQuestionList($bankId,$userId){
		$Question = M('Question');
        $UserQuestion = D('UserQuestion');
		if (!is_numeric($bankId)){
			return false;
		}
		$list = $Question->where('bank_id=%d', $bankId)->order('number')->select();
		foreach ($list as &$q) {
			$q['options'] = unserialize($q['options']);
			$q['key'] = $this->getQuestionKey($q['options']);
            //这里放入的是用户对题目的自定义设置，目前只有‘笔记’
            $user= $UserQuestion->getNote($userId,$q['id']);
            $q['note']=$user['note'];
            $q['note_update_time']=$user['note_update_time'];
		}
		return $list;
	}

	//返回一条题目记录的详细信息，为空时返回NULL
	public function getQuestionInfoById($id,$userId){
		$Question = M('Question');
        $UserQuestion = D('UserQuestion');
		$result = $Question->where('id=%d', $id)->find();
		if ($result != null){
			$result['options'] = unserialize($result['options']);
			$result['key'] = $this->getQuestionKey($result['options']);
            //这里放入的是用户对题目的自定义设置，目前只有‘笔记’
            $user= $UserQuestion->getNote($userId,$result['id']);
            $result['note']=$user['note'];
            $result['note_update_time']=$user['note_update_time'];
		}
		return $result;
	}
	public function getQuestionInfoByNum($bankId, $number,$userId){
		$Question = M('Question');
        $UserQuestion = D('UserQuestion');
		$result = $Question->where('bank_id=%d AND number=%d', $bankId, $number)->find();
		if ($result != null){
			$result['options'] = unserialize($result['options']);
			$result['key'] = $this->getQuestionKey($result['options']);
            //这里放入的是用户对题目的自定义设置，目前只有‘笔记’
            $user= $UserQuestion->getNote($userId,$result['id']);
            $result['note']=$user['note'];
            $result['note_update_time']=$user['note_update_time'];
		}
		return $result;		
	}

	/**
	* 根据选项信息返回正确答案字符串
	* @param options：选项格式：array(array('option'=>str,'correct'=>tinyint))
	*/
	public function getQuestionKey($options){
		$result = '';
		foreach($options as $i=>$option){
			if($option['correct'] == 1){
				$result .= alphaID($i + 1);
			}
		}
		return $result;
	}
}
?>