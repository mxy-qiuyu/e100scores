<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends HomeCommonController {
	public function index(){
		$Course = D('Course');
		$data['course'] = $Course->getCourseList();
		$this->assign($data);
		$this->display();
	}

	public function bankList(){
		$course = I('get.course', '', ALIAS_FORMAT);
		if ($course == ''){
			$this->error('非法访问');
		}
		$Course = D('Course');
		$courseId = $Course->getCourseId($course);
		if ($courseId == null){
			$this->error('找不到指定科目');
		}
		$courseInfo = $Course->getCourseInfo($courseId);
		$data['course'] = $courseInfo['name'];
		$QuestionBank = D('QuestionBank');
		$Result = D('Result');
		$userId = session('userid');
		$data['list'] = $QuestionBank->getBankList($courseId);
		foreach ($data['list'] as &$value) {
			$progress = $Result->getRecordBySearch($userId, $value['id']);
            if ($progress == null){
            	$value['progress'] = 0;
            }else{
                $value['progress'] = $progress['completed'];
            }
		}
		//dump($data);
		$this->assign($data);
		$this->display();
	}
}
?>