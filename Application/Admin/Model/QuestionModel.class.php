<?php
namespace Admin\Model;
use Think\Model;
class QuestionModel extends Model
{
	//字段验证规则
	protected $_validate = array(
			array('id', 'number', 'id不是数字'),
			array('bank_id', 'number', 'bank_id不是数字'),
			array('number', 'number', 'number不是数字'),
			array('title', 'require', 'name不能为空'),
			array('type', 'number', 'type不是数字'),
			array('point', 'number', 'point不是数字')
		);

	//返回指定题库下的题目列表,为空时返回NULL
	public function getQuestionList($bankId){
		$Question = M('Question');
		if (!is_numeric($bankId)){
			return false;
		}
		$list = $Question->where('bank_id=%d', $bankId)->order('number')->select();
		foreach ($list as &$q) {
			$q['options'] = unserialize($q['options']);
			$q['key'] = $this->getQuestionKey($q['options']);
		}
		return $list;
	}

	//返回一条题目记录的详细信息，为空时返回NULL
	public function getQuestionInfo($id){
		$Question = M('Question');
		$result = $Question->where('id=%d', $id)->find();
		if ($result != null){
			$result['options'] = unserialize($result['options']);
			$result['key'] = $this->getQuestionKey($result['options']);
		}
		return $result;
	}

	/**
	 * 向数据表保存一条记录
	 * @param data: 待保存的记录数组，键值包括: id(可选),bank_id,number,title,options(arr),point(可选),analysis(可选)
	 * @param action: 操作类型，取值可为：add,update
	 */
	public function saveRecord($data, $action){
		$Question = M('Question');
		$QuestionBank = D('QuestionBank');
		$data['options'] = serialize($data['options']);
		if ($Question->create($data)){
			if ($action == 'add'){
				$Question->add();
			}else{
				$Question->save();
			}
			$result = $QuestionBank->updateQuestionAmount($data['bank_id']);
			return $result;
		}else{
			return false;
		}
	}

	//删除指定的题目
	public function deleteQuestion($id){
		$Question = M('Question');
		$QuestionBank = D('QuestionBank');
		if (!is_numeric($id)){
			return false;
		}
		$bankId = $Question->where('id="%d"', $id)->getField('bank_id');
		$result = $Question->delete($id);
		$QuestionBank->updateQuestionAmount($bankId);
		return $result;
	}

	// 删除指定题库下的所有题目
	public function deleteQuestionsByBank($bank_id){
		$Question = M('Question');
		if (!is_numeric($bank_id)){
			return false;
		}
		$result = $Question->where('bank_id = %d', $bank_id)->delete();
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

	/**
	* 返回题库下指定题号的题目数量
	* @param id: 需排除掉的自身id
	* @param bank_id: 待查找的题库
	* @param number: 待查找的题号 
	*/
	public function countNumber($id, $bank_id, $number){
		$Question = M('Question');
		$map['id'] = array('neq', $id);
		$map['bank_id'] = array('eq', $bank_id);
		$map['number'] = array('eq', $number);
		$total = $Question->where($map)->count();
		return $total;
	}

	// 检查指定题库的所有题号是否序列化（题号是否为1~n）
	public function isConsecutive($bankId){
		$Question = M('Question');
		$total = $Question->where('bank_id = "%s"', $bankId)->count();
		$map['bank_id'] = array('eq', $bankId);
		$map['number'] = array('egt', 1);
		$map['number'] = array('elt', $total);
		$count = $Question->where($map)->count();
		if ($count == $total){
			return true;
		}else{
			return false;
		}
	}
}
?>