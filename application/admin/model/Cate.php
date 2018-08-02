<?php
namespace app\admin\model;
use think\Model;
/**
 * 基础model
 */
class Cate extends Model{
    public function get_cate(array $where)
    {
        return $this->field('id,name,create_time,update_time,status')->where($where)->select();
    }

}
