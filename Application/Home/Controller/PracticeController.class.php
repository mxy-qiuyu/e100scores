<?php
namespace Home\Controller;
use Think\Controller;
class PracticeController extends HomeCommonController {
	public function index(){
		$bankAlias = I('get.bank', '', ALIAS_FORMAT);
		$number = I('get.num', 0, '/^\d+$/');
		$QuestionBank = D('QuestionBank');
		$userId = session('userid');
		$Question = D('Question');
		$Result = D('Result');

		if ($bankAlias == ''){
			$this->error('非法访问');
		}
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$this->error('未找到该题库');
		}

		$bankInfo = $QuestionBank->getBankInfo($bankId);
		if ($number > 0 && $number <= $bankInfo['amount']){
			$data = $Question->getQuestionInfoByNum($bankId, $number,$userId);
			$data['latest'] = 0;
		}else{
			$number = $this->getLatestNum($userId, $bankId);
			if ($number < $bankInfo['amount']){
				$number += 1;
			}else{
				redirect(U('Practice/result', array('bank'=>$bankAlias)));
			}
			$data = $Question->getQuestionInfoByNum($bankId, $number,$userId);
			$data['latest'] = 1;
		}
		$data['number'] = $number;
		$record = $Result->getRecordBySearch($userId, $bankId);
		if ($record != null && sizeof($record) >= $number){
            $data['answer'] = $record['answer'][$number-1];
        }
		$data['bank'] = $bankInfo['name'];
		$data['bank_alias'] = $bankAlias;
        $data['course_id'] = $bankInfo['course_id'];
		$data['amount'] = $bankInfo['amount'];

        dump($data);
        exit;
		$this->assign($data);
		$this->display();
	}

	public function result(){
		$bankAlias = I('get.bank', '', ALIAS_FORMAT);
		$QuestionBank = D('QuestionBank');
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$this->error('未找到该题库');
		}
		$bankInfo = $QuestionBank->getBankInfo($bankId);
		$courseId = $bankInfo['course_id'];
		$Course = D('Course');
		$courseInfo = $Course->getCourseInfo($courseId);
		$data['bank_alias'] = $bankAlias;
		$data['course_alias'] = $courseInfo['alias'];
		$data['amount'] = $bankInfo['amount'];
		$this->assign($data);
		$this->display();		
	}


	private function getLatestNum($userId, $bankId){
		$Result = D('Result');
		$answer = $Result->getRecordBySearch($userId, $bankId);
		if ($answer == null){
			return 0;
		}else{
			return ($answer['completed']);
		}
	}

	public function updateUserData(){
		$bankAlias = I('post.bank', '', ALIAS_FORMAT);
		$userData = I('post.data', '');
		if ($bankAlias == '' || $userData == ''){
			$this->ajaxReturn('error');
			exit;
		}
		$QuestionBank = D('QuestionBank');
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$this->ajaxReturn('error');
			exit;
		}
		$userId = session('userid');
		$userData = json_decode($userData); 
		$data['answer'] = $userData;
		$data['completed'] = sizeof($userData);
		$Result = D('Result');
		$result = $Result->saveRecord($userId, $bankId, $data);
		if ($result){
			$this->ajaxReturn('success');
		}else{
			$this->ajaxReturn('error');
		}
	}

	public function loadUserData(){
		$bankAlias = I('post.bank', '', ALIAS_FORMAT);
		if ($bankAlias == ''){
			$data['status'] = 1;
			$this->ajaxReturn($data);
			exit;
		}
		$QuestionBank = D('QuestionBank');
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$data['status'] = 2;
			$this->ajaxReturn($data);
			exit;
		}
		$userId = session('userid');
		$Result = D('Result');
		$userData = $Result->getRecordBySearch($userId, $bankId);
		if ($userData == null){
			$data['status'] = 3;
			$this->ajaxReturn($data);
			exit();
		}
		$data['status'] = 0;
		$data['data'] = $userData['answer'];
		$this->ajaxReturn($data);
	}

    /*
     * $_POST=array(
     *      'content'       =>笔记内容,
     *      'questionId'    =>题目Id,
     * )
     *
     * ajaxReturn的状态码status:
     *      0:成功
     *      1：接收到的questionId不是数字
     *      2:(数据库)提交失败
     */
    public function updateNote(){
        $userId=session('userid');
        $questionId=I('post.questionId',-1,'/^\d+$/');
        $content=I('post.content');
        if ($questionId==-1){
            $data['status'] = 1;
            $this->ajaxReturn($data);
            exit;
        }
        $UserQuestion = D('UserQuestion');
        $result=$UserQuestion->updateNote($userId,$questionId,$content);
        if(!$result){
            $data['status'] = 2;
            $this->ajaxReturn($data);
            exit;
        }else{
            $data['status'] = 0;
            $this->ajaxReturn($data);
        }
    }

	public function clearUserData(){
		$bankAlias = I('post.bank', '', ALIAS_FORMAT);
		if ($bankAlias == ''){
			$data['status'] = 1;
			$this->ajaxReturn($data);
			exit;
		}
		$QuestionBank = D('QuestionBank');
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$data['status'] = 2;
			$this->ajaxReturn($data);
			exit;
		}
		$userId = session('userid');
		$Result = D('Result');
		$result = $Result->deleteRecord($userId, $bankId);
		if ($result !== false){
			$data['status'] = 0;
			$this->ajaxReturn($data);
		}else{
			$data['status'] = 3;
			$this->ajaxReturn($data);
		}
	}

	public function loadBank(){
		$bankAlias = I('post.bank', '', ALIAS_FORMAT);
        $userId = session('userid');
		if ($bankAlias == ''){
			$data['status'] = 1;
			$this->ajaxReturn($data);
			exit;
		}
		$QuestionBank = D('QuestionBank');
		$bankId = $QuestionBank->getBankId($bankAlias);
		if ($bankId == null){
			$data['status'] = 2;
			$this->ajaxReturn($data);
			exit;
		}
		$Question = D('Question');
		$bankData = $Question->getQuestionList($bankId,$userId);
		if ($bankData == null){
			$data['status'] = 3;
			$this->ajaxReturn($data);
			exit;
		}
		foreach ($bankData as &$value) {
			unset($value['id'],$value['bank_id'],$value['number'],$value['point']);
		}
		$data['status'] = 0;
		$data['data'] = $bankData;
		$this->ajaxReturn($data);		
	}
}