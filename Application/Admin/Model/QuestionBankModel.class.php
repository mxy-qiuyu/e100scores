<?php
namespace Admin\Model;
use Think\Model;
class QuestionBankModel extends Model{
	//字段验证规则
	protected $_validate = array(
			array('id', 'number', 'id不是数字'),
			array('course_id', 'number', 'course_id不是数字'),
			array('name', 'require', 'name不能为空'),
			array('alias', ALIAS_FORMAT, 'alias不合法'),
			array('publish', TINYINT_FORMAT, 'publish只能0或1'),
			array('amount', 'number', 'amount不是数字')
		);

	//返回指定科目的题库列表，为空时返回null
	public function getBankList($course = NULL){
		$QuestionBank = M('QuestionBank');
		$QuestionBank->field('bank.*, course.name as course, course.alias as course_alias')->table('question_bank bank, course');
		$QuestionBank->order('id desc');
		if ($course === NULL){
			$QuestionBank->where('bank.course_id=course.id');
		}else{
			$QuestionBank->where('bank.course_id=course.id AND course.id="%d"', $course);
		}
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

	/**
	 * 向数据表保存一条记录
	 * @param data: 待保存的记录数组，键值包括: id(可选),course_id,name,alias,publish
	 * @param action: 操作类型，取值可为：add,update
	 */
	public function saveRecord($data, $action){
		$QuestionBank = M('QuestionBank');
		if ($QuestionBank->create($data)){
			if ($action == 'add'){
				$QuestionBank->add();
			}else{
				$QuestionBank->save();
			}
			return true;
		}else{
			return false;
		}
	}

	//返回指定题库别名对应的题库id，如找不到返回NULL
	public function getBankId($alias){
		$QuestionBank = M('QuestionBank');
		$id = $QuestionBank->where('alias="%s"', $alias)->getField('id');
		return $id;
	}	

	//返回与指定题库重名的别名数量
	public function countAlias($id, $alias){
		$QuestionBank = M('QuestionBank');
		$map['id'] = array('neq', $id);
		$map['alias'] = array('eq', $alias);
		$total = $QuestionBank->where($map)->count();
		return $total;
	}

	//设置指定题库的发布状态
	public function setPublish($id, $publish){
		$QuestionBank = M('QuestionBank');
		if ($publish != 0 && $publish != 1){
			return false;
		}
		$QuestionBank->publish = $publish;
		$result = $QuestionBank->field('publish')->where('id="%d"', $id)->save();
		return $result;
	}

	//删除指定的题库
	public function deleteBank($id){
		$QuestionBank = M('QuestionBank');
		$Question = D('Question');
		if (!is_numeric($id)){
			return false;
		}
		$result = $Question->deleteQuestionsByBank($id);
		if ($result !== false){
			$result = $QuestionBank->delete($id);
		}
		return $result;
	}

	//更新指定题库的题量信息
	public function updateQuestionAmount($id){
		$QuestionBank = M('QuestionBank');
		$Question = M('Question');
		if (!is_numeric($id)){
			return false;
		}
		$amount = $Question->where('bank_id="%d"', $id)->count();
		if ($amount === false){
			return false;
		}
		$data['amount'] = $amount;
		if ($QuestionBank->field('amount')->where('id="%d"', $id)->save($data) !== false){
			return $amount;
		}else{
			return false;
		}
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