<?php
namespace Home\Model;
use Think\Model;
class UserQuestionModel extends Model{

    //依据题目和用户id返回笔记
    public function getNote($userId,$questionId){
        $UserQuestion=M('user_question');
        $result=$UserQuestion->where('user_id="%s" AND question_id=%d', $userId, $questionId)->field('note,	note_update_time')->find();
        return $result;
    }

    //依据题目和用户id更新笔记
    public function updateNoteById($userId,$questionId,$content){
        $UserQuestion=M('user_question');
        $originalData = $this->getNote($userId,$questionId);
        if($originalData==null){
            $data['user_id']=$userId;
            $data['question_id']=$questionId;
            $data['note']=$content;
        }else{
            $data = $originalData;
            $data['note'] = $content;
        }
        $data['note_update_time'] = date('Y-m-d H:i:s');
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
     * @return 0:成功；1：数据格式有误；2：题目已被收藏
     */
    public function addQuestionToFavorite($bankId,$num,$userId){
        $UserQuestion = M('user_question');
        $Question = M('question');
        $questionId = $Question->where('bank_id="%d" AND number="%d"',$bankId,$num)->getfield('id');
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
            return 0;
        }else{
            return 1;
        }
    }

    /* 依据题库Id、题目编号和用户Id将题目添加到收藏夹
     *
     * @return 0:成功；1：数据格式有误；2：题目原本就没有被收藏
     */
    public function removeQuestionFromFavorite($bankId,$num,$userId){
        $UserQuestion = M('user_question');
        $Question = M('question');
        $questionId = $Question->where('bank_id="%d" AND number="%d"',$bankId,$num)->getfield('id');
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
            return 0;
        }else{
            return 1;
        }
    }

}