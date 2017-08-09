<?php
class News_model extends CI_Model {
    public function __construct()
    {
        $this->load->database();
    }

    function get_page($preNum = 0,$page=0)
    {
        if ($preNum)
        {
            $this->db->order_by('time DESC');
            $this->db->limit($preNum, $page);
            $query = $this->db->get('news');
            return $query->result_array();
        }
        $this->db->order_by('time DESC');
        $query = $this->db->get('news');
        return $query->result_array();
    }

    public function add_news($data){
        if($this->db->insert('news', $data)){
            return "添加成功!";
        }else{
            return "添加失败!";
        }

    }

    public function  get_news($id){
        $query = $this->db->get_where('news', array('id' => $id));
        return $query->row_array();
    }

    public function edit_news($id,$data){
        $this->db->where('id <>', $id);
        $this->db->where('slug', $data['slug']);
        //$this->db->from('news');
        $query = $this->db->get('news');
        $result=$query->row_array();
        if(empty($result)) {  //不存在
            $this -> db -> where('id', $id);
            $error= $this -> db -> update('news', $data);
            if($error){
                return "修改成功!";
            }else{
                return "修改失败!";
            }
        }else{
            return "此产品地址已经存在，不能重复!";
        }
    }

    public function del_news($uids){
        $this -> db -> where_in('id', $uids);
        $result= $this -> db -> delete('news');
        if($result) {
            return "删除成功!";
        }else{
            return "删除失败!";
        }
    }


    // type
    public function get_type()
    {   //select
        $this->db->order_by('pid ASC,listid ASC');
        $query = $this->db->get('news_type');
        return $query->result_array();
    }

    public function  get_type_tree($items){
        $tree = array();
        foreach ($items as $item){
            if($item['pid']==0){
                $tree[]=$item;
            }else{
                foreach($tree as $key=>$val){
                    if($item['pid']==$val['id']){
                        $tree[$key]['son'][]=$item;
                    }
                }
            }
        }

        return $tree;
    }

    public function get_type_father()
    {   //select
        $this->db->where('pid',0);
        $this->db->order_by('listid ASC');
        $query = $this->db->get('news_type');
        return $query->result_array();
    }
    function add_type($data)
    {  //insert
        $query = $this->db->get_where('news_type', array('title' => $data['title'],'pid' => $data['pid']));
        $result=$query->row_array();
        if(empty($result)) {  //不存在关联
            $error= $this->db->insert('news_type', $data);
            if($error){
                return "添加成功!";
            }else{
                return "添加失败!";
            }
        }else{
            return "此号已经存在，不能重复添加!";
        }

    }
    public function set_type($id,$data)
    {  //update
        $this -> db -> where('id', $id);
        return  $this -> db -> update('news_type', $data);
    }
    function del_type($id)
    {   //delete
        $query = $this->db->get_where('news', array('typeid' => $id));
        $result=$query->row_array();
        if(empty($result)) {  //不存在关联
            $this -> db -> where('id', $id);
            $result= $this -> db -> delete('news_type');
            if($result) {
                $arr=array(
                    "err" =>0 ,
                    "mes" =>"删除成功"
                );
            } else{
                $arr=array(
                    "err" =>1 ,
                    "mes" =>"删除失败"
                );
            }
        }else{
            $arr=array(
                "err" =>2 ,
                "mes" =>"此分类已被引用,不能删除"
            );
        }
        return $arr;
    }


}