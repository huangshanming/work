<?php
namespace app\admin\controller;
use think\Db;
use think\Controller;
use app\admin\model\Cate as cateModel;
use app\admin\model\Attrs as attrsModel;
use app\admin\model\Goods as goodsModel;
use app\admin\model\GoodsAttr as goodsAttrModel;

class Goods extends Controller
{
	//获取添加商品界面初始化信息
    public function get_add_info()
    {
    	$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
    	$cateModel = new cateModel();
    	$where = [];
    	$where['status'] = 1;
    	$cates = $cateModel->get_cate($where);
    	$attrsModel = new attrsModel();
    	$where = [];
    	$where['status'] = 1;
        $where['admin_id'] = $user_id;
    	$attrs = $attrsModel->get_attrs($where);
    	$data = [];
    	$data['cates'] = $cates;
    	$data['attrs'] = $attrs;
    	return json(code('e200'),$data,'获取数据成功');
    }

    //添加商品
    public function add_goods()
    {
        $img = new Image();
    	$user_id = User::is_login();
		if(!$user_id)
		{
			return json(code('e500'),'','用户未登录');
		}
        if(!is_numeric(input('count')) || input('count')<0) 
        {
            return json(code('e400'),'','参数错误');
        }
        
        if(!is_numeric(input('price')) || input('price')<0) 
        {
            return json(code('e400'),'','参数错误');
        }        
        $sp1 = input('sp1');
        $sp2 = input('sp2');
        $sp3 = input('sp3');
        $sp4 = input('sp4');
        $bp  = input('bp');
        $attr_1 = input('attr_1');
        $attr_2 = input('attr_2');
        $attr_3 = input('attr_3');
        $attr_4 = input('attr_4');
        $av_1 = input('av_1');
        $av_2 = input('av_2');
        $av_3 = input('av_3');
        $av_4 = input('av_4');
        $data = [];
        if($bp)
        {
            $data['picture'] = $img::saveBase64Image($bp);
            if(!$data['picture'])
            {
                return json(code('e400'),'','添加失败');
            }
        }
        if($sp1)
        {
            $data['icon1'] = $img::saveBase64Image($sp1);
            if(!$data['icon1'])
            {
                return json(code('e400'),'','添加失败');
            }
        }
        if($sp2)
        {
            $data['icon2'] = $img::saveBase64Image($sp2);
            if(!$data['icon2'])
            {
                return json(code('e400'),'','添加失败');
            }
        }
        if($sp3)
        {
            $data['icon3'] = $img::saveBase64Image($sp3);
            if(!$data['icon3'])
            {
                return json(code('e400'),'','添加失败');
            }
        }
        if($sp4)
        {
            $data['icon4'] = $img::saveBase64Image($sp4);
            if(!$data['icon4'])
            {
                return json(code('e400'),'','添加失败');
            }
        }
        $attr_list = [];
        $av_list = [];
        if($attr_1 && $av_1) {
            $attr_list[] = $attr_1;
            $av_list[] = $av_1;
        }
        if($attr_2 && $av_2) {
            $attr_list[] = $attr_2;
            $av_list[] = $av_2;
        }
        if($attr_3 && $av_3) {
            $attr_list[] = $attr_3;
            $av_list[] = $av_3;
        }
        if($attr_4 && $av_4) {
            $attr_list[] = $attr_4;
            $av_list[] = $av_4;
        }
        $list = [];
        if($attr_list!=[] && $av_list!=[]) {
            $where = [];
            $where['id'] = array('in',$attr_list);
            $where['admin_id'] = $user_id;
            $attrs = Db::name('attrs')->field('name')->where($where)->select();
            $where = [];
            $where['id'] = array('in',$av_list);
            $vals = Db::name('attr_value')->field('value')->where($where)->select();
            if(count($attrs)==count($vals)) {
                foreach ($attrs as $row) {
                    $list['attrs'][] =  $row['name'];
                }
                foreach ($vals as $row) {
                    $list['vals'][] =  $row['value'];
                }
            }
        }
        $data['admin_id'] = $user_id;
        $data['title'] = input('title');
        $data['cate_id'] = input('cate_id');
        $data['create_time'] = time();
        $data['detail'] = input('detail');
        Db::startTrans();
        $goodsModel = new goodsModel();
        $goods_id = $goodsModel->add_goods($data);
        $aData = [];
        $aData['goods_id'] = $goods_id;
        $aData['attr_info'] = json_encode($list);
        $aData['attrs_id'] = implode(',',$attr_list);
        $aData['price'] = input('price');
        $aData['count'] = input('count');
        $aData['create_time'] = time();
        $goodsAttrModel = new goodsAttrModel();
        $goods_attr_id = $goodsAttrModel->add_attr($aData);
        if($goods_id && $goods_attr_id)
        {
            Db::commit();
            return json(code('e200'),'','添加成功');
        }else
        {
            Db::rollback();
            return json(code('e400'),'','添加失败');
        }
        
    }

    public function get_list()
    {
        $user_id = User::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        $goodsModel = new goodsModel();
        $condition = [];
        if(trim(input('title'))) 
        {
            $condition['title'] = array('like','%'.input('title').'%');
        }
        $count = $goodsModel->get_count($user_id,1,$condition);
        $page = input('p')?input('p'):1;
        if($page<=0 || $page>$count || !is_numeric($page))
        {
            $page = 1;
        }
        $pagesize = 10;
        $list = $goodsModel->get_list($user_id,1,$page,$pagesize,$condition);
        foreach ($list as &$row) {
            $row['picture'] = getPicUrl($row['picture'],'admin');
        }
        $data = [];
        $data['list'] = $list;
        $data['page'] = $page;
        $data['pagesize'] = $pagesize;
        $data['count'] = $count;
        $data['max_page'] = ceil($count/$pagesize);
        return json(code('e200'),$data,'获取数据成功');
    }

    public function get_info()
    {
        $user_id = User::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        $attrId = input('id');
        $goodsModel = new goodsModel();
        $list = $goodsModel->get_by_id($user_id,$attrId);
        $list['picture'] = $list['picture']?getPicUrl($list['picture'],'admin'):'';
        $list['icon1'] = $list['icon1']?getPicUrl($list['icon1'],'admin'):'';
        $list['icon2'] = $list['icon2']?getPicUrl($list['icon2'],'admin'):'';
        $list['icon3'] = $list['icon3']?getPicUrl($list['icon3'],'admin'):'';
        $list['icon4'] = $list['icon4']?getPicUrl($list['icon4'],'admin'):'';
        if(!$list)
        {
            return json(code('e401'),'','没有权限执行此操作');
        }
        return json(code('e200'),$list,'获取数据成功');
    }

    public function update_goods()
    {
        $user_id = User::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        $goods_id = input('id');

        $img = new Image();
        $data = [];
        $where = [];
        $picture = input('bp');
        $icon1 = input('sp1');
        $icon2 = input('sp2');
        $icon3 = input('sp3');
        $icon4 = input('sp4');
        if(strlen($picture)>config('img_len'))
        {
            $data['picture'] = $img::saveBase64Image($picture);
            if(!$data['picture'])
            {
                return json(code('e400'),'','更新失败');
            }
        }
        if(strlen($icon1)>config('img_len'))
        {
            $data['icon1'] = $img::saveBase64Image($icon1);
            if(!$data['icon1'])
            {
                return json(code('e400'),'','更新失败');
            }
        }
        if(strlen($icon2)>config('img_len'))
        {
            $data['icon2'] = $img::saveBase64Image($icon2);
            if(!$data['icon2'])
            {
                return json(code('e400'),'','更新失败');
            }
        }
        if(strlen($icon3)>config('img_len'))
        {
            $data['icon3'] = $img::saveBase64Image($icon3);
            if(!$data['icon3'])
            {
                return json(code('e400'),'','更新失败');
            }
        }
        if(strlen($icon4)>config('img_len'))
        {
            $data['icon4'] = $img::saveBase64Image($icon4);
            if(!$data['icon4'])
            {
                return json(code('e400'),'','更新失败');
            }
        }
        $where['admin_id'] = $user_id;
        $data['title'] = input('title');
        $data['cate_id'] = input('cate_id');
        $data['update_time'] = time();
        $data['detail'] = input('detail');
        $where['id'] = $goods_id;
        $goodsModel = new goodsModel();
        $goods_ret = $goodsModel->update_goods($data,$where);
        if($goods_ret)
        {
            return json(code('e200'),'','更新成功');
        }else
        {
            return json(code('e400'),'','更新失败');
        }
    }

    //增加商品属性
    public function add_attr() 
    {
        $user_id = User::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        if(!input('goods_id') ||!is_numeric(input('goods_id')) || !is_numeric(input('count')) || !is_numeric(input('origin_price'))|| !is_numeric(input('price')))
        {
            return json(code('e400'),'','参数错误');
        }
        $attr_1 = input('attr_1');
        $attr_2 = input('attr_2');
        $attr_3 = input('attr_3');
        $attr_4 = input('attr_4');
        $av_1 = input('av_1');
        $av_2 = input('av_2');
        $av_3 = input('av_3');
        $av_4 = input('av_4');
        $attr_list = [];
        $av_list = [];
        if($attr_1 && $av_1) {
            $attr_list[] = $attr_1;
            $av_list[] = $av_1;
        }
        if($attr_2 && $av_2) {
            $attr_list[] = $attr_2;
            $av_list[] = $av_2;
        }
        if($attr_3 && $av_3) {
            $attr_list[] = $attr_3;
            $av_list[] = $av_3;
        }
        if($attr_4 && $av_4) {
            $attr_list[] = $attr_4;
            $av_list[] = $av_4;
        }
        $list = [];
        if($attr_list!=[] && $av_list!=[]) {
            $where = [];
            $where['id'] = array('in',$attr_list);
            $where['admin_id'] = $user_id;
            $attrs = Db::name('attrs')->field('name')->where($where)->select();
            $where = [];
            $where['id'] = array('in',$av_list);
            $vals = Db::name('attr_value')->field('value')->where($where)->select();
            if(count($attrs)==count($vals)) {
                foreach ($attrs as $row) {
                    $list['attrs'][] =  $row['name'];
                }
                foreach ($vals as $row) {
                    $list['vals'][] =  $row['value'];
                }
            }
        }
        $aData = [];
        $aData['goods_id'] = input('goods_id');
        $aData['attr_info'] = json_encode($list);
        $aData['attrs_id'] = implode(',',$attr_list);
        $aData['price'] = input('price');
        $aData['count'] = input('count');
        $aData['create_time'] = time();
        $goodsAttrModel = new goodsAttrModel();
        $goods_attr_id = $goodsAttrModel->add_attr($aData);
        if($goods_attr_id) {
            return json(code('e200'),'','添加成功');
        }
        return json(code('e400'),'','添加失败');
    }

    //删除属性
    public function del_attr() 
    {
        $user_id = User::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        if(!input('id') || !is_numeric(input('id')) || !is_numeric(input('goods_id')))
        {
            return json(code('e400'),'','参数错误');
        }
        $goodsAttrModel = new goodsAttrModel();
        $ret = $goodsAttrModel->del_attr($user_id,input('goods_id'),input('id'));
        if($ret) {
            return json(code('e200'),'','成功');
        }
        return json(code('e400'),'','删除失败');
    }

    //删除商品
    public function del_goods() 
    {
        $user_id = User::is_login();
        if(!$user_id)
        {
            return json(code('e500'),'','用户未登录');
        }
        if(!input('id') || !is_numeric(input('id')))
        {
            return json(code('e400'),'','参数错误');
        }
        $goodsModel = new goodsModel();
        $ret = $goodsModel->del($user_id,input('id'));
        if($ret) {
            return json(code('e200'),'','成功');
        }
        return json(code('e400'),'','删除失败');
    }
    
}
