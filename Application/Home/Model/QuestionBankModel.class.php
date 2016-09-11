<?php
namespace Home\Model;
use Think\Model;
class QuestionBankModel extends Model{
	//返回指定科目已发布的题库列表，为空时返回null
	public function getBankList($course){
		$QuestionBank = M('QuestionBank');
		$QuestionBank->field('bank.*, course.name as course, course.alias as course_alias')->table('question_bank bank, course');
		$QuestionBank->where('bank.course_id=course.id AND bank.publish=1 AND course.id="%d"', $course);
		$QuestionBank->order('priority desc,id');
		$list = $QuestionBank->select();
		return $list;
	}

	//返回一条题库记录的详细信息，为空时返回NULL
	public function getBankInfo($id){
		$QuestionBank = M('QuestionBank');
		$QuestionBank->field('bank.*, course.name as course, course.alias as course_alias')->table('question_bank bank, course')->where('bank.course_id=course.id AND bank.id="%s"', $id);
		$list = $QuestionBank->find();
		return $list;
	}

	//返回指定题库别名对应的题库id，如找不到返回NULL
	public function getBankId($alias){
		$QuestionBank = M('QuestionBank');
		$id = $QuestionBank->where('alias="%s" AND publish=1', $alias)->getField('id');
		return $id;
	}	

	// 返回指定题库的题量信息
	public function getQuestionAmount($id){
		$QuestionBank = M('QuestionBank');
		if (!is_numeric($id)){
			return false;
		}
		$amount = $QuestionBank->where('id="%d"', $id)->getField('amount');
		if ($amount === false){
			return false;
		}else{
			return $amount;
		}
	}
}
?>