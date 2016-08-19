<?php
namespace Home\Model;
use Think\Model;
class QuestionModel extends Model
{

	//返回指定题库下的题目列表,为空时返回NULL
	public function getQuestionList($bankId,$userId){
		$Question = M('Question');
        $QuestionUser = D('UserQuestion');
		if (!is_numeric($bankId)){
			return false;
		}
		$list = $Question->where('bank_id=%d', $bankId)->order('number')->select();
		foreach ($list as &$q) {
			$q['options'] = unserialize($q['options']);
			$q['key'] = $this->getQuestionKey($q['options']);
            //这里放入的是用户对题目的自定义设置，目前只有‘笔记’
            $user= $QuestionUser->getNote($userId,$q['id']);
            $q['note']=$user['note'];
            $q['note_update_time']=$user['note_update_time'];
		}
		return $list;
	}

	//返回一条题目记录的详细信息，为空时返回NULL
	public function getQuestionInfoById($id,$userId){
		$Question = M('Question');
        $QuestionUser = D('UserQuestion');
		$result = $Question->where('id=%d', $id)->find();
		if ($result != null){
            $result['options'] = unserialize($result['options']);
            $result['key'] = $this->getQuestionKey($result['options']);
            //这里放入的是用户对题目的自定义设置，目前只有‘笔记’
            $user= $QuestionUser->getNote($userId,$result['id']);
            $result['note']=$user['note'];
            $result['note_update_time']=$user['note_update_time'];
        }
		return $result;
	}
	public function getQuestionInfoByNum($bankId, $number,$userId){
		$Question = M('Question');
        $QuestionUser = D('UserQuestion');
		$result = $Question->where('bank_id=%d AND number=%d', $bankId, $number)->find();
		if ($result != null){
			$result['options'] = unserialize($result['options']);
			$result['key'] = $this->getQuestionKey($result['options']);
            //这里放入的是用户对题目的自定义设置，目前只有‘笔记’
            $user= $QuestionUser->getNote($userId,$result['id']);
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

    /*
     *
     */
    public function getQuestionAmountForFavorite($courseId,$userId){
        $Question=M('Question');
        $result=$Question
            ->join('question_bank ON question.bank_id=question_bank.id')
            ->join('user_question ON question.id=user_question.question_id')
            ->where('
                user_question.user_id="%s" AND 
                question_bank.course_id="%d" AND 
                question_bank.publish=1 AND 
                user_question.is_collected=1
                ',$userId,$courseId)
            ->count();
        return $result;
    }

    public function getQuestionListForFavorite($courseId,$userId){
        $Question=M('Question');
        $result=$Question
            ->join('question_bank ON question.bank_id=question_bank.id')
            ->join('user_question ON question.id=user_question.question_id')
            ->where('
                user_question.user_id="%s" AND 
                question_bank.course_id="%d" AND 
                question_bank.publish=1 AND 
                user_question.is_collected=1
                ',$userId,$courseId)
            ->field('
                question.*,
                
                user_question.note,
                user_question.note_update_time,
                
                question_bank.name as bank,
                question_bank.alias as bank_alias
            ')
            ->select();
        foreach($result as &$value){
            $value['options'] = unserialize($value['options']);
            $value['key'] = $this->getQuestionKey($value['options']);
        }
        return $result;
    }


    public function getQuestionByNumForFavorite($courseId,$userId,$number){
        $Question=M('Question');
        $result=$Question
            ->join('question_bank ON question.bank_id=question_bank.id')
            ->join('user_question ON question.id=user_question.question_id')
            ->where('
                user_question.user_id="%s" AND 
                question_bank.course_id="%d" AND 
                question_bank.publish=1 AND 
                user_question.is_collected=1 
                ',$userId,$courseId)
            ->field('
                question.*,
                
                user_question.note,
                user_question.note_update_time,
                
                question_bank.name as bank,
                question_bank.alias as bank_alias
            ')
            ->limit($number-1,1)
            ->select();

        $result[0]['options'] = unserialize($result[0]['options']);
        $result[0]['key'] = $this->getQuestionKey($result[0]['options']);
        return $result[0];
    }
}
?>