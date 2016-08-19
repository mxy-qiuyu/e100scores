<?php
namespace Home\Controller;
use Think\Controller;
class FavoriteController extends PracticeController{
    /*
     * $_GET=array(
     *      courseAlias     =>科目别名，
     *      num             =>题目编号
     * )
     */
    public function index(){
        $courseAlias = I('get.courseAlias', '', ALIAS_FORMAT);
        $number = I('get.num', 0, '/^\d+$/');
        $Course = D('Course');
        $Result = D('Result');
        $courseId = $Course->getCourseId($courseAlias);
        $userId = session('userid');
        if ($courseAlias==''){
            $this->error("非法访问");
        }

        $Favorite = D('Favorite');
        $favoriteId=$Favorite->getfavoriteId($courseAlias,$userId);

        //这里尚待讨论，收藏夹还没有建立的时候
        if(!$favoriteId){
            $this->error("收藏夹不存在");
        }

        //加载题目
        $Question = D('Question');
        $amount=$Question->getQuestionAmountForFavorite($courseId,$userId);

        if ($number > 0 && $number <= $amount){
            $data = $Question->getQuestionByNumForFavorite($courseId,$userId,$number);
            $data['latest'] = 0;
        }else{
            $number = $Result->getLastNumForFavorite($userId,$favoriteId);

            if ($number < $amount){
                $number += 1;
            }else{
                redirect(U('Favorite/result'/*, array('bank'=>$bankAlias)*/));
            }
            $data = $Question->getQuestionByNumForFavorite($courseId,$userId,$number);
            $data['latest'] = 1;
        }

        //加载收藏夹信息
        $favoriteInfo = $Favorite->getFavoriteInfo($favoriteId);

        //加载题目答案
        $answer = $Result->getAnswerByNumForFavorite($userId,$favoriteId,$number);

        if ($answer) $data['answer'] = $answer;
        $data['course_id'] = $favoriteInfo['course_id'];
        $data['amount'] = $amount;
        $data['favorite_id'] = $favoriteId;

        $this->assign($data);
        $this->display();

    }

    /**
     * 依照用户ID和科目Id来定位收藏夹
     * $_GET=array(
     *      courseAlias    =>科目表外键，
     * )
     */
    public function loadFavorite(){
        $courseAlias = I('get.courseAlias', '', ALIAS_FORMAT);
        $Course = D('Course');
        $courseId = $Course->getCourseId($courseAlias);
        $userId = session('userid');
        if ($courseAlias==''){
            $this->error("非法访问");
        }
        $Question = D('Question');
        dump($Question->getFavoriteList($courseId,$userId));
    }

    /**
     * 依照用户ID和科目名来创建收藏夹
     * $_GET=array(
     *      course      =>科目英文别名，
     * )
     *
     * 尚需修改
     */
    public function createFavorite(){
        $courseAlias = I('get.course', '', ALIAS_FORMAT);
        if ($courseAlias==''){
            $this->error("非法访问");
        }
        $userId = session('userid');
        $Favorite=D('Favorite');
        $result = $Favorite->createFavorite($userId,$courseAlias);

        //以下尚需修改
        switch($result){
            case 0:
                $this->success('创建成功');
                break;
            case 1:
                $this->error('收藏夹已存在');
                break;
            case 2:
                $this->error('数据格式不当');
                break;
            case 3:
                $this->error('插入失败');
                break;
        }
    }

    /**
     * 依照用户ID和科目名来删除收藏夹
     * $_GET=array(
     *      course      =>科目英文别名，
     * )
     *
     * 尚需修改
     */
    public function dropFavorite(){
        $courseAlias = I('get.course', '', ALIAS_FORMAT);
        if ($courseAlias==''){
            $this->error("非法访问");
        }
        $userId = session('userid');
        $Favorite=D('Favorite');
        $result=$Favorite->dropFavorite($userId,$courseAlias);

        //以下尚需修改
        switch($result){
            case 0:
                $this->success('删除成功');
                break;
            case 1:
                $this->error('收藏夹不存在');
                break;
            case 2:
                $this->error('异常，删除了多个收藏夹');
                break;
            case 3:
                $this->error('失败，没有删除任何收藏夹');
                break;
        }
    }

    /**
     * 依照题库Id、题目编号和用户ID来在收藏夹中添加题目
     * $_POST=array(
     *      bankId      =>题库id，
     *      num         =>题目编号，
     * )
     * 尚需修改
     */
    public function addQuestion(){
        $bankId = I('post.bankId',-1,'/^\d+$/');
        $number = I('post.num',-1,'/^\d+$/');
        if ($bankId==-1||$number==-1){
            $this->error("非法访问");
        }
        $userId=session('userid');
        $UserQuestion=D('UserQuestion');
        $result=$UserQuestion->addQuestionToFavorite($bankId,$number,$userId);


        //以下尚需修改
        switch($result){
            case 0:
                $this->success('添加成功');
                break;
            case 1:
                $this->error('数据格式有误');
                break;
            case 2:
                $this->error('题目已被收藏');
                break;
        }
    }

    /**
     * 依照题库Id、题目编号和用户ID来在移除收藏夹中的题目
     * $_POST=array(
     *      bankId      =>题库id，
     *      num         =>题目编号，
     * )
     * 尚需修改
     */
    public function removeQuestion(){
        $bankId = I('post.bankId',-1,'/^\d+$/');
        $number = I('post.num',-1,'/^\d+$/');
        if ($bankId==-1||$number==-1){
            $this->error("非法访问");
        }
        $userId=session('userid');
        $UserQuestion=D('UserQuestion');
        $result=$UserQuestion->removeQuestionFromFavorite($bankId,$number,$userId);


        //以下尚需修改
        switch($result){
            case 0:
                $this->success('移除成功');
                break;
            case 1:
                $this->error('数据格式有误');
                break;
            case 2:
                $this->error('题目原本就没有被收藏');
                break;
        }
    }


    /**
     * $_GET=array(
     *      courseAlias     =>科目别名，
     * )
     */
    public function result(){
        $courseAlias = I('get.courseAlias', '', ALIAS_FORMAT);
        $Course = D('Course');
        $Result = D('Result');
        $courseId = $Course->getCourseId($courseAlias);
        $userId = session('userid');
        if ($courseAlias==''){
            $this->error("非法访问");
        }

        $Favorite = D('Favorite');
        $favoriteId = $Favorite->getfavoriteId($courseAlias,$userId);
        $favoriteInfo = $Favorite->getFavoriteInfo($favoriteId);

        $Course = D('Course');
        $courseInfo = $Course->getCourseInfo($courseId);

        $data['course_alias'] = $courseInfo['alias'];
        $Question = D('Question');
        $data['amount'] = $Question->getQuestionAmountForFavorite($courseId,$userId);

        //这里可能还需要一些收藏夹的什么信息，但是我现在不知道发送什么过去

        $this->assign($data);
        $this->display();
    }

    /*
     * $_POST=array(
     *      courseAlias     =>科目别名，
     *      data            =>用户数据的json字符串
     * )
     */
    public function updateUserData(){
        $userId = session('userid');
        $courseAlias = I('post.courseAlias', '', ALIAS_FORMAT);
        $userData = I('post.data', '');
        if ($courseAlias == '' || $userData == ''){
            $this->ajaxReturn('error');
            exit;
        }

        $Favorite = D('Favorite');
        $favoriteId = getFavoriteId($courseAlias,$userId);
        $bankId = $Favorite->getFavoriteId($courseAlias,$userId);
        if ($bankId == null){
            $this->ajaxReturn('error');
            exit;
        }

        $userData = json_decode($userData);
        $data['answer'] = $userData;
        $data['completed'] = sizeof($userData);
        $Result = D('Result');
        $result = $Result->saveRecordForFavorite($userId, $favoriteId, $data);
        if ($result){
            $this->ajaxReturn('success');
        }else{
            $this->ajaxReturn('error');
        }
    }

    /*
     * $_POST=array(
     *      courseAlias     =>科目别名
     * )
     */
    public function  loadUserData(){
        $userId = session('userid');
        $courseAlias = I('post.courseAlias', '', ALIAS_FORMAT);
        if ($courseAlias == '' ){
            $this->ajaxReturn('error');
            exit;
        }
        $Favorite = D('Favorite');
        $favoriteId = $Favorite->getFavoriteId($courseAlias,$userId);
        if ($favoriteId == null){
            $data['status'] = 2;
            $this->ajaxReturn($data);
            exit;
        }

        $Result = D('Result');
        $userData = $Result->getRecordBySearchForFavorite($userId, $favoriteId);
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
     *      courseAlias     =>科目别名
     * )
     */
    public function clearUserData(){
        $userId = session('userid');
        $courseAlias = I('post.courseAlias', '', ALIAS_FORMAT);
        if ($courseAlias == ''){
            $data['status'] = 1;
            $this->ajaxReturn($data);
            exit;
        }
        $Favorite = D('Favorite');
        $favoriteId = $Favorite->getFavoriteId($courseAlias,$userId);
        if ($favoriteId == null){
            $data['status'] = 2;
            $this->ajaxReturn($data);
            exit;
        }
        $Result = D('Result');
        $result = $Result->deleteRecordForFavorite($userId, $favoriteId);
        if ($result !== false){
            $data['status'] = 0;
            $this->ajaxReturn($data);
        }else{
            $data['status'] = 3;
            $this->ajaxReturn($data);
        }
    }
}