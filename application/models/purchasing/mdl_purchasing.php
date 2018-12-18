<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mdl_purchasing extends CI_Model{
	
	function get_item_group($where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('in_ms_item_group.*, in_ms_item_class.class_code, in_ms_item_class.class_desc, ms_department.department_name,  ms_department.department_desc');
		$this->db->from('in_ms_item_group');
		$this->db->join('in_ms_item_class', 'in_ms_item_group.class_id = in_ms_item_class.class_id','left');
		$this->db->join('ms_department', 'in_ms_item_group.department_id = ms_department.department_id','left');
		if(count($where) > 0){
			$this->db->where($where);
		}
		if(count($like) > 0){
			$this->db->like($like);
		}
		if($order_by != ""){
			$this->db->order_by($order_by);
		}
		if($limit_row > 0){
			$this->db->limit($limit_row, $limit_start);
		}

		return $this->db->get();
    }
	function get_item_service($where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('in_ms_item.*, in_ms_uom.uom_code ');
		$this->db->from('in_ms_item');
		$this->db->join('in_ms_uom', 'in_ms_item.uom_id = in_ms_uom.uom_id','left'); 
		if(count($where) > 0){
			$this->db->where($where);
		}
		if(count($like) > 0){
			$this->db->like($like);
		}
		if($order_by != ""){
			$this->db->order_by($order_by);
		}
		if($limit_row > 0){
			$this->db->limit($limit_row, $limit_start);
		}

		return $this->db->get();
    }
	
	function get_item_material($where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
		$this->db->select('in_ms_item.*, in_ms_uom.uom_code,uom_code_issue.uom_code as uom_code_issue ');
		$this->db->from('in_ms_item');
		$this->db->join('in_ms_uom', 'in_ms_item.uom_id = in_ms_uom.uom_id','left'); 
		$this->db->join('in_ms_uom uom_code_issue', 'in_ms_item.uom_id_distribution = uom_code_issue.uom_id','left'); 		 
		if(count($where) > 0){
			$this->db->where($where);
		}
		if(count($like) > 0){
			$this->db->like($like);
		}
		if($order_by != ""){
			$this->db->order_by($order_by);
		}
		if($limit_row > 0){
			$this->db->limit($limit_row, $limit_start);
		}

		return $this->db->get();
    }
	 
	
	function get_item_code($group_id =0 ){
		$qry_prefix_code= $this->db->query("select   concat (class_code,'/', group_code,'/')  as prefix_code  from in_ms_item_group 
				inner join in_ms_item_class on in_ms_item_group.class_id = in_ms_item_class.class_id
				where  in_ms_item_group.group_id =  ".$group_id);
		$prefix_code = $qry_prefix_code->row()->prefix_code;

		$qry = $this->mdl_general->get('in_ms_item', array('group_id' => $group_id, 'status <>' => STATUS_DELETE, 'item_code <>' => Purchasing::DIRECT_PURCHASE), array(), 'item_code ASC');
		if ($qry->num_rows() > 0) {
		    $i = 1;
		    foreach($qry->result() as $row) {
		        $last_code = substr($row->item_code, -4);
		        $last_code = intval($last_code);

		        if ($i != $last_code) {
		            break;
                }

                $i++;
            }

            $len = strlen($i);
            if($len < 4){
                $number = str_repeat('0', (4 - $len)) . $i;
            }
        } else {
		    $number = '0001';
        }

        $next_code = $prefix_code . $number;

		return $next_code;

		/*$exist = $this->mdl_general->count('in_ms_item', array('group_id' => $group_id, 'item_code <>' => Purchasing::DIRECT_PURCHASE));


		if($exist > 0){	
		 $number = $this->db->query("SELECT top 1 case when right(t1.item_code,4)-1 = 0 then 
						( select concat('".$prefix_code."', right(max(right(item_code,4))+1+10000 ,4) ) from in_ms_item where in_ms_item.group_id =  ".$group_id ." ) 
						else concat('".$prefix_code."',right(right(t1.item_code,4)-1 +10000 ,4)) end as nomer FROM in_ms_item t1
						left outer join in_ms_item t2 
						on right(t1.item_code,4) -1 = right(t2.item_code,4)
						where right(t2.item_code,4) is null 
						and right(t1.item_code,4) > 0
						and t1.group_id =  ".$group_id .
					" 	order by t1.item_code  desc ");
			}		 
		else {
		$number =$this->db->query("SELECT   concat('".$prefix_code."',  '0001' ) as nomer ");
		}
		return $number  ;*/

		/*
		if(count($where) > 0){
			$this->db->where($where);
		} 

		return $this->db->get();*/
    }

    function get_pr_list($where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0, $where_string = ''){
        $this->db->select('in_pr.*,  ms_department.department_name ,pr_detail.item_desc,isnull(ms_project.project_initial,\' \') as project_initial, ms_user.user_fullname, ms_user.user_email');
        $this->db->from('in_pr');
        $this->db->join("ms_department", 'in_pr.department_id = ms_department.department_id','left');		
        $this->db->join("ms_project", 'in_pr.project_id = ms_project.project_id','left');
        $this->db->join("ms_user", 'in_pr.user_created = ms_user.user_id','left');
		$this->db->join("(SELECT distinct pr_id, convert( varchar(max),STUFF((SELECT  concat( ', ' , item_desc)
						FROM in_pr_item a
						where b.pr_id =a.pr_id
						FOR XML PATH ('')),1,1,'') )  as 'item_desc'
						from  in_pr_item  b ) as pr_detail ", "pr_detail.pr_id = in_pr.pr_id","left");
		

        if(count($where) > 0){
            $this->db->where($where);
        }
        if(trim($where_string) != ''){
            $this->db->where($where_string);
        }
        if(count($like) > 0){
            $this->db->like($like);
        }
        if($order_by != ""){
            $this->db->order_by($order_by);
        }
        if($limit_row > 0){
            $this->db->limit($limit_row, $limit_start);
        }

        return $this->db->get();
    }
	 
	function count_pr($table_name, $where = array(), $like = array(), $where_string = ''){
		$this->db->from($table_name);
		$this->db->join("(SELECT distinct pr_id, convert( varchar,STUFF((SELECT  concat( ', ' , item_desc)
						FROM in_pr_item a
						where b.pr_id =a.pr_id
						FOR XML PATH ('')),1,1,'') )  as 'item_desc'
						from  in_pr_item  b ) as pr_detail ", "pr_detail.pr_id = in_pr.pr_id","left");
        $this->db->join("ms_user", 'in_pr.user_created = ms_user.user_id','left');
		if(count($where) > 0){
			$this->db->where($where);
		}
        if(trim($where_string) != ''){
            $this->db->where($where_string);
        }
		if(count($like) > 0){
			$this->db->like($like);
		}

		return $this->db->count_all_results();
    }
	
    function get_po_list($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){

        $this->db->select('in_po.*,in_pr.pr_code, ms_department.department_name, pr_detail.item_desc, in_supplier.supplier_name, in_supplier.supplier_address, in_supplier.supplier_telephone, in_supplier.supplier_fax, in_supplier.contact_name, in_supplier.contact_phone, currencytype.currencytype_code, currencytype.currencytype_desc ');
        $this->db->from('in_po');
        $this->db->join("ms_department", 'in_po.department_id = ms_department.department_id','left');	
        $this->db->join("in_supplier", 'in_po.supplier_id = in_supplier.supplier_id','left');	
        $this->db->join("in_pr", 'in_po.pr_id = in_pr.pr_id','left');
        $this->db->join("currencytype", 'currencytype.currencytype_id = in_po.currencytype_id','left');
        $this->db->join("(SELECT distinct pr_id, convert( varchar,STUFF((SELECT  concat( ', ' , item_desc)
						FROM in_pr_item a
						where b.pr_id =a.pr_id
						FOR XML PATH ('')),1,1,'') )  as 'item_desc'
						from  in_pr_item  b ) as pr_detail ", "pr_detail.pr_id = in_pr.pr_id","left");

        if(count($where) > 0){
            $this->db->where($where);
        }

        if(count($like) > 0){
            $this->db->like($like);
        }
        if($order_by != ""){
            $this->db->order_by($order_by);
        }
        if($limit_row > 0){
            $this->db->limit($limit_row, $limit_start);
        }

        if($is_count){
            return $this->db->count_all_results();
        }
        else {
            return $this->db->get();
        }
    }
	
    function get_pr_by_project($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
        $this->db->select('in_pr.*,  ms_department.department_name ,pr_detail.item_desc');
        $this->db->from('in_pr');
        $this->db->join("ms_department", 'in_pr.department_id = ms_department.department_id','left');		
        $this->db->join("(SELECT distinct pr_id, convert( varchar,STUFF((SELECT  concat( ', ' , item_desc)
						FROM in_pr_item a
						where b.pr_id =a.pr_id
						FOR XML PATH ('')),1,1,'') )  as 'item_desc'
						from  in_pr_item  b ) as pr_detail ", "pr_detail.pr_id = in_pr.pr_id","left");
		

        if(count($where) > 0){
            $this->db->where($where);
        }
        if(count($like) > 0){
            $this->db->like($like);
        }
        if($order_by != ""){
            $this->db->order_by($order_by);
        }
        if($limit_row > 0){
            $this->db->limit($limit_row, $limit_start);
        }

        if($is_count){
            return $this->db->count_all_results();
        }
        else {
            return $this->db->get();
        }
    }
	
	function get_pr_detail($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
        $this->db->select('in_pr_item.*,in_ms_uom.uom_code, in_supplier.supplier_name, in_ms_item.item_code, in_ms_item.account_coa_id, gl_coa.coa_code, in_ms_item.qty_distribution AS item_factor ');
        $this->db->from('in_pr_item'); 
        $this->db->join('in_ms_uom', 'in_pr_item.uom_id = in_ms_uom.uom_id', 'left');
        $this->db->join('in_ms_item', 'in_pr_item.item_id = in_ms_item.item_id', 'left');
        $this->db->join('gl_coa', 'in_ms_item.account_coa_id = gl_coa.coa_id', 'left');
        $this->db->join('in_supplier', 'in_pr_item.supplier_id = in_supplier.supplier_id', 'left');
		if(count($where) > 0){
			$this->db->where($where);
		}
		if(count($like) > 0){
			$this->db->like($like);
		}
		if($order_by != ""){
			$this->db->order_by($order_by);
		}
		if($limit_row > 0){
			$this->db->limit($limit_row, $limit_start);
		}
		
		if($is_count){
			return $this->db->count_all_results();
		}
		else {
			return $this->db->get();
		}
    }

    function get_po_detail($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0){
        $this->db->select('in_po_detail.*, in_pr_item.item_desc, in_ms_item.item_code, in_ms_item.item_desc as ms_item_desc, in_ms_uom.uom_code, tax_type.taxtype_code, in_pr_item.qty_remain, gl_coa.coa_code as account_coa_code ');
        $this->db->from('in_po_detail');
        $this->db->join('in_pr_item', 'in_po_detail.pr_item_id = in_pr_item.pr_item_id', 'left');
        $this->db->join('in_ms_uom', 'in_po_detail.uom_id = in_ms_uom.uom_id', 'left');
        $this->db->join('in_ms_item', 'in_po_detail.item_id = in_ms_item.item_id', 'left');
        $this->db->join('tax_type', 'in_po_detail.tax_id = tax_type.taxtype_id', 'left');
        $this->db->join('gl_coa', 'in_po_detail.account_coa_id = gl_coa.coa_id', 'left');
        if(count($where) > 0){
            $this->db->where($where);
        }
        if(count($like) > 0){
            $this->db->like($like);
        }
        if($order_by != ""){
            $this->db->order_by($order_by);
        }
        if($limit_row > 0){
            $this->db->limit($limit_row, $limit_start);
        }

        if($is_count){
            return $this->db->count_all_results();
        }
        else {
            return $this->db->get();
        }
    }

	function get_po_approved($is_count = false, $where = array(), $like = array(), $order_by = "", $limit_row = 0, $limit_start = 0,$where_string){
        $this->db->select('in_po.* ');
        $this->db->from('in_po');
        $this->db->join('in_grn', ' in_po.po_id =in_grn.po_id', 'left'); 
        $this->db->join('ms_project', ' in_po.project_id = ms_project.project_id ', 'left'); 
		
        if(count($where) > 0){
            $this->db->where($where);
        }
		if(trim($where_string) != ''){
            $this->db->where($where_string);
        }
        if(count($like) > 0){
            $this->db->like($like);
        }
        if($order_by != ""){
            $this->db->order_by($order_by);
        }
        if($limit_row > 0){
            $this->db->limit($limit_row, $limit_start);
        }

        if($is_count){
            return $this->db->count_all_results();
        }
        else {
            return $this->db->get();
        }
    }
}
?>