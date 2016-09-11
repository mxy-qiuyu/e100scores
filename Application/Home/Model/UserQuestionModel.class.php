<?php
namespace Home\Model;
use Think\Model;
class UserQuestionModel extends Model{

    //依据题目和用户id返回笔记
    public function getNote($userId,$questionId){
        $UserQuestion = M('user_question');
        $result = $UserQuestion->where('user_id="%s" AND question_id=%d',$userId,$questionId)->field('note, note_update_time')->find();
        return $result;
    }

    //依据题目和用户id更新笔记
    public function updateNoteById($userId,$questionId,$content,$date){
        $UserQuestion = M('user_question');
        $originalData = $this->getNote($userId,$questionId);
        if($originalData==null){
            $data['user_id'] = $userId;
            $data['question_id'] = $questionId;
            $data['note'] = $content;
        }else{
            $data = $originalData;
            $data['note'] = $content;
        }
        $data['note_update_time'] = $date;
        if ($UserQuestion->create($data)){
            if($originalData==null){
                $UserQuestion->add();
            }else{
                $UserQuestion->save();
            }
            return true;
        }else{
            return false;
        }
    }

    /* 依据题库Id、题目编号和用户Id将题目添加到收藏夹
     *
     * @return 0:成功；1：数据格式有误；2：题目已被收藏；3：未在题库中找到题目；4：收藏夹不存在，尝试创建收藏夹失败
     */
    public function addQuestionToFavorite($bankId,$num,$userId,$courseId){
        $UserQuestion = M('user_question');
        $Question = M('question');
        $questionId = $Question->where('bank_id="%d" AND number="%d"',$bankId,$num)->getfield('id');
        if ($questionId==null){
            return 3;
        }
        $originalData=$UserQuestion->where('user_id="%s" AND question_id=%d', $userId, $questionId)->find();

        if($originalData==null) {
            $data['user_id'] = $userId;
            $data['question_id'] = $questionId;
            $data['is_collected'] = 1;
        }else{
            if ($originalData['is_collected'] == 1 )return 2;
            $data = $originalData;
            $data['is_collected'] = 1;
        }

        if ($UserQuestion->create($data)){
            if($originalData==null){
                $UserQuestion->add();
            }else{
                $UserQuestion->save();
            }

            //这部分代码是题目成功加入收藏夹后执行的，对收藏夹耦合字段amount进行处理
            $Favorite = D('Favorite');
            $FavoriteView = D('FavoriteView');
            $amount = $FavoriteView->getQuestionAmount($courseId,$userId);
            $FavoriteId = $Favorite->getFavoriteIdByCourseId($courseId,$userId);
            if(!$FavoriteId){//收藏夹不存在
                $status = $Favorite->createFavoriteByCourseId($courseId,$userId,$amount);
                if($status==2||$status==3) return 4;
            }else{
                $setResult = $Favorite->setFavoriteQuestionAmount($FavoriteId,$amount);
                if($setResult!=0)return 4;
            }

            return 0;
        }else{
            return 1;
        }


    }

    /* 依据题库Id、题目编号和用户Id将题目添加到收藏夹
     *
     * @return 0:成功；1：数据格式有误；2：题目原本就没有被收藏；3：未在题库中找到题目；4：收藏夹不存在，尝试创建收藏夹失败
     */
    public function removeQuestionFromFavorite($bankId,$num,$userId,$courseId){
        $UserQuestion = M('user_question');
        $Question = M('question');
        $questionId = $Question->where('bank_id="%d" AND number="%d"',$bankId,$num)->getfield('id');
        if ($questionId==null){
            return 3;
        }
        $originalData=$UserQuestion->where('user_id="%s" AND question_id=%d', $userId, $questionId)->find();

        if($originalData==null) {
            $data['user_id'] = $userId;
            $data['question_id'] = $questionId;
            $data['is_collected'] = 0;
        }else{
            if ($originalData['is_collected']==0)return 2;
            $data = $originalData;
            $data['is_collected'] = 0;
        }

        if ($UserQuestion->create($data)){
            if($originalData==null){
                $UserQuestion->add();
            }else{
                $UserQuestion->save();
            }

            //这部分代码是题目成功加入收藏夹后执行的，对收藏夹耦合字段amount进行处理
            $Favorite = D('Favorite');
            $FavoriteView = D('FavoriteView');
            $amount = $FavoriteView->getQuestionAmount($courseId,$userId);
            $FavoriteId = $Favorite->getFavoriteIdByCourseId($courseId,$userId);
            if(!$FavoriteId){//收藏夹不存在
                $status = $Favorite->createFavoriteByCourseId($courseId,$userId,$amount);
                if($status==2||$status==3) return 4;
            }else{
                $setResult = $Favorite->setFavoriteQuestionAmount($FavoriteId,$amount);
                if($setResult!=0) return 5;
            }

            return 0;
        }else{
            return 1;
        }
    }
}