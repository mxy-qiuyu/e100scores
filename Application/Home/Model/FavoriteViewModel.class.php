<?php
namespace Home\Model;
use Think\Model\ViewModel;
class FavoriteViewModel extends ViewModel {
    public $viewFields = array(
        'question' =>array(
            'id' =>'question_id',
            'bank_id',
            'number',
            'title',
            'options',
            'type',
            'analysis',
            'point'
        ),
        'user_question' =>array(
            'note',
            'note_update_time',
            '_on'=>'question.id=user_question.question_id'
        ),
        'question_bank'=>array(
            'name'=>'bank',
            'alias'=>'bank_alias',
            '_on'=>'question_bank.id=question.bank_id'
        )
    );

    /*
     * 如果未找到题目返回false
     * 这里有调用Question模型的getQuestionKey方法
     */
    public function getQuestionListForFavorite($courseId,$userId){
        $result = $this
            ->where('
                user_question.user_id="%s" AND 
                question_bank.course_id="%d" AND 
                question_bank.publish=1 AND 
                user_question.is_collected=1
                ',
                $userId,
                $courseId
            )
            ->select();

        if (!$result)return false;

        foreach($result as &$value){
            $value['options'] = unserialize($value['options']);
            $value['key'] = QuestionModel::getQuestionKey($value['options']);
        }
        return $result;
    }


    /*
    * 如果未找到题目返回false
    * 这里有调用Question模型的getQuestionKey方法
    */
    public function getQuestionByNumForFavorite($courseId,$userId,$number){
        $result = $this
            ->where('
                user_question.user_id="%s" AND 
                
                question_bank.course_id="%d" AND 
                question_bank.publish=1 AND 
                                
                user_question.is_collected=1
                ',
                $userId,
                $courseId
            )
            ->limit($number-1,1)
            ->select();

        if (!$result)return false;

        $result[0]['options'] = unserialize($result[0]['options']);
        $result[0]['key'] = QuestionModel::getQuestionKey($result[0]['options']);
        return $result[0];
    }
    /*
     * 在收藏夹题目变动之后，用于确定总的题目数量的函数，开销比较大
     */
    public function getQuestionAmountForFavorite($courseId,$userId){
        $result = $this
            ->where('
                user_question.user_id="%s" AND 
                question_bank.course_id="%d" AND 
                question_bank.publish=1 AND 
                user_question.is_collected=1
                ',
                $userId,
                $courseId
            )
            ->count();
        return $result;
    }
}