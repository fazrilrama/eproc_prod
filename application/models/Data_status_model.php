<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Data_status_model extends App_Model
{
    public function get($addParams=[
        'useLimit'=>true,
        'limit'=>10,
        'offset'=>0,
        'orderBy'=>'id',
        'orderDirection'=>'asc',
        'search'=>null,
        'f_kompetensi' => null,
        'f_sub_kompetensi' => null,
        'f_area_kerja' => null,
        'f_bidang' => null,
        'f_branch' => null,
        'f_cabang' => null,
        'f_company_owner'=>null
        
    ],$modifier=null){
        $params=[];
        
        $f_kompetensi = isset($addParams['f_kompetensi'])==null?null:$addParams['f_kompetensi'];
        $f_sub_kompetensi = isset($addParams['f_sub_kompetensi'])==null?null:$addParams['f_sub_kompetensi'];
        $f_area_kerja = isset($addParams['f_area_kerja'])==null?null:$addParams['f_area_kerja'];
        $f_bidang = isset($addParams['f_bidang'])==null?null:$addParams['f_bidang'];
        $f_branch = isset($addParams['f_branch'])==null?null:$addParams['f_branch'];
        $f_company_owner = isset($addParams['f_company_owner'])==null?null:$addParams['f_company_owner'];

        $kompetensi_name_condition = "";
        $kompetensi_name_where = "";
        $sub_kompetensi_name_condition = "";
        $sub_kompetensi_name_where = "";
        $bidang_where = "";

        $area_kerja = "";
        $branch_where = "";
        $f_company_owner_where = "";
        
        
        if ($f_kompetensi != null) {
            $kompetensi_name_condition = " AND h.id=?";
            $kompetensi_name_where = " AND ? in (SELECT h.id FROM company_competencies f 
            JOIN m_company_sub_competency g on g.id=f.id_company_sub_competency
            JOIN m_company_competency h on h.id=g.id_company_competency
            WHERE f.deleted_at is null AND f.id_company=a.id )";
            $params[]=$f_kompetensi;
            $params[]=$f_kompetensi;
        }

        if ($f_sub_kompetensi != null) {
            $sub_kompetensi_name_condition = " AND g.id=?";
            $sub_kompetensi_name_where = " AND ? in (SELECT h.id FROM company_competencies f 
            JOIN m_company_sub_competency g on g.id=f.id_company_sub_competency
            JOIN m_company_competency h on h.id=g.id_company_competency
            WHERE f.deleted_at is null AND f.id_company=a.id )";
            $params[]=$f_sub_kompetensi;
            $params[]=$f_sub_kompetensi;
        }

        if ($f_bidang != null) {
            $bidang_where = " AND ? in (SELECT i.id_company_type FROM company_type i
            WHERE i.deleted_at is null AND i.id_company=a.id)";
            $params[]=$f_bidang;
        }

        if ($f_area_kerja != null) {
            $area_kerja = " AND ? in (select id_city from company_work_area where id_company=a.id )";
            $params[]=$f_area_kerja;
        }

        // if ($f_branch != null && $f_branch != "1000") {
        //     $branch_where = " AND  (
        //             SELECT count(id_city) FROM company_work_area 
        //             WHERE id_company=a.id
        //             AND id_city in (SELECT id from m_city WHERE branch_id=?) 
        //         )>=1
        //     ";
        //     $params[]=$f_branch;
        // }



        if($f_company_owner!=null){
            $f_company_owner_where=" AND mc.id=?";
            $params[]=$f_company_owner;
        }


        if($f_branch != null && $f_branch != "111") {

            $getBranchId = $this->db->select('*')->from('m_branch_code')->where('official_code', $f_branch)->get()->row();

            $getCompany = $this->db->select('*')->from('company_cabang_area')->where('id_cabang', $getBranchId->id)->group_by('id_company')->get()->result();

            $id_comp = [];
            for ($i=0; $i < count($getCompany); $i++) { 
                $element = $getCompany[$i];

                array_push($id_comp, "$element->id_company");
            }

            // $string_result = implode(', ', $id_comp);
            $string_result = "('" . implode("', '", $id_comp) . "')";
            // print_r($string_result);
            // die;

            $sql = "SELECT a.*,
            b.address,
            b.email,
            f.email as login_email,    
            b.phone,
            c.no as no_npwp,
            d.id_sap,
            e.name as group_name,
            e.description as group_desc
            
            , (SELECT GROUP_CONCAT(DISTINCT(h.name) separator '||') 
                FROM company_type f 
                inner join m_company_type h
            WHERE f.deleted_at is null 
            AND f.id_company=a.id
            AND f.id_company_type=h.id
            ) as company_types
            
            , (SELECT GROUP_CONCAT(DISTINCT(h.name) separator '||') FROM company_competencies f 
            JOIN m_company_sub_competency g on g.id=f.id_company_sub_competency
            JOIN m_company_competency h on h.id=g.id_company_competency
            WHERE f.deleted_at is null AND f.id_company=a.id $kompetensi_name_condition ) as competencies_name
            
            , (SELECT GROUP_CONCAT(DISTINCT(g.name) separator '||') FROM company_competencies f 
            JOIN m_company_sub_competency g on g.id=f.id_company_sub_competency
            JOIN m_company_competency h on h.id=g.id_company_competency
            WHERE f.deleted_at is null AND f.id_company=a.id $sub_kompetensi_name_condition ) as sub_competencies_name
            
            , (SELECT GROUP_CONCAT(DISTINCT(i.name) separator '||') FROM company_work_area j
                inner join m_city i on i.id=j.id_city 
                where j.id_Company=a.id) as work_area
            ,(
                if(f.is_blacklisted=0,'Whitelisted','Blacklisted')
            ) as blacklist_status_name
            ,f.id_usr_status
            ,f.is_blacklisted
            ,f.blacklist_note
            ,f.id_usr_role
            ,mc.codename as company_owner_name
            ,pic.name as pic_name
            ,pic.mobile_phone as pic_mobile_phone
            
            FROM company_profile a
            inner JOIN company_contact b on (a.id=b.id_company and b.deleted_at is null)
            inner JOIN company_legal_npwp c on (a.id=c.id_company and c.deleted_at is null)
            inner JOIN tbl_sync_sap d on (a.id=d.id_company and d.deleted_at is null)
            inner JOIN m_group e on d.id_group=e.id
            inner JOIN sys_user f on f.id_user=a.id_user
            inner JOIN sys_usr_status g on g.id_usr_status=f.id_usr_status
            inner join sys_usr_role rl on rl.id_usr_role=f.id_usr_role
            left join m_company mc on mc.id=a.id_company_owner
            left join company_pic pic on pic.id_company=a.id
            WHERE a.deleted_at is null
            and f.id_usr_status=2
            and a.id IN $string_result
            $kompetensi_name_where
            $sub_kompetensi_name_where
            $area_kerja
            $bidang_where
            $f_company_owner_where";

            $sql="SELECT * FROM ($sql) as dt
            where id is not null";
        } else {
            $sql = "SELECT a.*,
            b.address,
            b.email,
            f.email as login_email,    
            b.phone,
            c.no as no_npwp,
            d.id_sap,
            e.name as group_name,
            e.description as group_desc
            
            , (SELECT GROUP_CONCAT(DISTINCT(h.name) separator '||') 
                FROM company_type f 
                inner join m_company_type h
            WHERE f.deleted_at is null 
            AND f.id_company=a.id
            AND f.id_company_type=h.id
            ) as company_types
            
            , (SELECT GROUP_CONCAT(DISTINCT(h.name) separator '||') FROM company_competencies f 
            JOIN m_company_sub_competency g on g.id=f.id_company_sub_competency
            JOIN m_company_competency h on h.id=g.id_company_competency
            WHERE f.deleted_at is null AND f.id_company=a.id $kompetensi_name_condition ) as competencies_name
            
            , (SELECT GROUP_CONCAT(DISTINCT(g.name) separator '||') FROM company_competencies f 
            JOIN m_company_sub_competency g on g.id=f.id_company_sub_competency
            JOIN m_company_competency h on h.id=g.id_company_competency
            WHERE f.deleted_at is null AND f.id_company=a.id $sub_kompetensi_name_condition ) as sub_competencies_name
            
            , (SELECT GROUP_CONCAT(DISTINCT(i.name) separator '||') FROM company_work_area j
                inner join m_city i on i.id=j.id_city 
                where j.id_Company=a.id) as work_area
            ,(
                if(f.is_blacklisted=0,'Whitelisted','Blacklisted')
            ) as blacklist_status_name
            ,f.id_usr_status
            ,f.is_blacklisted
            ,f.blacklist_note
            ,f.id_usr_role
            ,mc.codename as company_owner_name
            ,pic.name as pic_name
            ,pic.mobile_phone as pic_mobile_phone
            
            FROM company_profile a
            inner JOIN company_contact b on (a.id=b.id_company and b.deleted_at is null)
            inner JOIN company_legal_npwp c on (a.id=c.id_company and c.deleted_at is null)
            inner JOIN tbl_sync_sap d on (a.id=d.id_company and d.deleted_at is null)
            inner JOIN m_group e on d.id_group=e.id
            inner JOIN sys_user f on f.id_user=a.id_user
            inner JOIN sys_usr_status g on g.id_usr_status=f.id_usr_status
            inner join sys_usr_role rl on rl.id_usr_role=f.id_usr_role
            left join m_company mc on mc.id=a.id_company_owner
            left join company_pic pic on pic.id_company=a.id
            WHERE a.deleted_at is null
            and f.id_usr_status=2
            $kompetensi_name_where
            $sub_kompetensi_name_where
            $area_kerja
            $bidang_where
            $branch_where
            $f_company_owner_where";

            $sql="SELECT * FROM ($sql) as dt
            where id is not null";
        }

        

        if($modifier!=null){
            $modifier=$modifier($sql);
            $sql=$modifier['sql'];
            foreach($modifier['params'] as $p){
                $params[]=$p;
            }
        }

        if(isset($addParams['search']) && $addParams['search']!=null ){
            $sql.="
            and (
                lower(address) like ?
                or lower(blacklist_status_name) like ?
                or lower(competencies_name) like ?
                or lower(created_at) like ?
                or lower(email) like ?
                or lower(group_desc) like ?
                or lower(group_name) like ?
                or lower(id_sap) like ?
                or lower(name) like ?
                or lower(no_npwp) like ?
                or lower(phone) like ?
                or lower(prefix_name) like ?
                or lower(sub_competencies_name) like ?
                or lower(verification_status) like ?
                or lower(work_area) like ?
                or lower(login_email) like ?
                or lower(pic_name) like ?
                or lower(pic_mobile_phone) like ?
            )
            ";
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
            $params[]='%'.strtolower($addParams['search']).'%';
        }
        $sql.=" ORDER BY ".$addParams['orderBy']." ".$addParams['orderDirection'];
        $dataCount=$this->db->query($sql,$params)->num_rows();
        $sql=$this->db->last_query();

        $params=[];
        if($addParams['useLimit'] && isset($addParams['limit']) && isset($addParams['offset'])){
            $sql.=" LIMIT ? OFFSET ?";
            $params[]=$addParams['limit'];
            $params[]=$addParams['offset'];
        }
        $data=$this->db->query($sql,$params)->result();
        return [
            'data'=>$data,
            'recordsTotal'=>$dataCount,
            'recordsFiltered'=>$dataCount
        ];
    }
}