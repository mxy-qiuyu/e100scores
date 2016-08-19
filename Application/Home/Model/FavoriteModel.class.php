<?php
namespace Home\Model;
use Think\Model;
class FavoriteModel extends QuestionBankModel{

    /*
     * @return 找不到时返回NULL
     */
    public function getFavoriteId($courseAlias,$userId){
        $Favorite=M('favorite');
        $result = $Favorite
                    ->join('course ON course.id=favorite.course_id')
                    ->where('course.alias="%s" AND favorite.user_id="%s"',$courseAlias,$userId)
                    ->getField('favorite.id');
        return $result;
    }

    public function getFavoriteInfo($favoriteId){
        $Favorite=M('favorite');
        $result = $Favorite->where($favoriteId)->find();
        return $result;
    }
    /*
     * @return 0:成功；1：收藏夹已存在；2：数据格式不当；3：插入失败
     */
    public function createFavorite($userId,$courseAlias){
        $Favorite=M('favorite');
        $Course=M('course');
        $courseId=$Course->where('alias="%s"',$courseAlias)->getField('id');
        $data['course_id'] = $courseId;
        $data['user_id'] = $userId;
        $data['question'] = serialize(array());
        if($Favorite->where('user_id="%s" AND course_id="%d"',$userId,$courseId)->find()){
            return 1;
        }
        if($Favorite->create($data)){
            if($Favorite->add()){
                return 0;
            }else{
                return 3;
            }
        }else{
            return 2;
        }

    }

    /*
     * @return 0:成功；1：收藏夹不存在；2：异常，删除了多个收藏夹；3：失败，没有删除任何收藏夹
     */
    public function dropFavorite($userId,$courseAlias){
        $Favorite=M('favorite');
        $Course=M('course');
        $courseId=$Course->where('alias="%s"',$courseAlias)->getField('id');

        if(!$Favorite->where('user_id="%s" AND course_id="%d"',$userId,$courseId)->find()){
            return 1;
        }else{
            $deleteNum = $Favorite->where('user_id="%s" AND course_id="%d"',$userId,$courseId)->delete();
            switch ($deleteNum){
                case 0:
                    return 3;
                    break;
                case 1:
                    return 0;
                    break;
                default:
                    if($deleteNum>1)return 2;
            }
        }
    }

}