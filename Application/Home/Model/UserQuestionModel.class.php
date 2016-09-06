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
}