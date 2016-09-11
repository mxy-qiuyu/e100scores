<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends HomeCommonController {
	// 首页：科目导航
	public function index(){
		$Course = D('Course');
		$data['course'] = $Course->getCourseList();
		$this->assign($data);
		$this->display();
	}

	// 题库列表页
	public function bankList(){
		// 检查数据合法性
		$course = I('get.course', '', ALIAS_FORMAT);
		if ($course == ''){
			$this->error('非法访问');
		}
		$Course = D('Course');
		$courseId = $Course->getCourseId($course);
		if ($courseId == null){
			$this->error('找不到指定科目');
		}

		// 获取页面渲染信息
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
                $value['update_time'] = $progress['update_time'];
            }
		}
		$this->assign($data);
		$this->display();
	}
}
?>