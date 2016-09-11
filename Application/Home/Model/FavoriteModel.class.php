<?php
namespace Home\Model;
use Think\Model;
class FavoriteModel extends Model{
    /*
     * @return 找不到时返回NULL
     */
    public function getFavoriteIdByCourseId($courseId,$userId){
        $Favorite=M('favorite');
        $result = $Favorite
            ->where('course_id="%d" AND user_id="%s"',$courseId,$userId)
            ->getField('id');
        return $result;
    }

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

    /*
     * @return 找不到时返回NULL
     */
    public function getFavoriteInfoById($FavoriteId){
        $Favorite = M('Favorite');
        $result = $Favorite->where($FavoriteId)->find();
        return $result;
    }

    /* 添加收藏夹
     * @return 0:成功；1：收藏夹已存在；2：数据格式不当；3：插入失败
     */
    public function createFavorite($courseAlias,$userId,$amount=0){
        $Favorite = M('favorite');
        $Course = M('course');
        $courseId = $Course->where('alias="%s"',$courseAlias)->getField('id');
        $result = $Favorite->where('course_id="%d" AND user_id="%d"',$courseId,$userId)->select();
        if ($result) return 1;

        $data['course_id'] = $courseId;
        $data['user_id'] = $userId;
        $data['amount'] = $amount;

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


    /* 添加收藏夹(可以设置收藏夹题目数量）
     * @return 0:成功；1：收藏夹已存在；2：数据格式不当；3：插入失败
     */
    public function createFavoriteByCourseId($courseId,$userId,$amount=0){
        $Favorite = M('favorite');
        $result = $Favorite->where('course_id="%d" AND user_id="%d"',$courseId,$userId)->find();
        if ($result) return 1;

        $data['course_id'] = $courseId;
        $data['user_id'] = $userId;
        $data['amount'] = $amount;

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

    /* 改变收藏夹表题量
     * @return 0:成功；1:收藏夹不存在；2：数据格式不当；3：插入数据库失败；
     */
    public function setFavoriteQuestionAmount($FavoriteId,$amount){
        $Favorite = M('Favorite');
        $FavoriteInfo = $Favorite->where($FavoriteId)->find();
        if (!$FavoriteInfo)return 1;
        $FavoriteInfo['amount'] = $amount;
        if($Favorite->create($FavoriteInfo)){
            if($Favorite->save()){
                return 0;
            }else{
                return 3;
            }
        }else{
            return 2;
        }
    }

}