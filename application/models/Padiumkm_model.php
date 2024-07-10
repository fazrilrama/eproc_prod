<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Padiumkm_model extends App_Model
{
    var $API_KEY='dW1RDuaU2al3RnC5jqLfE4DUDOZkOrQu';
    var $BASE_URL='https://api.thebigbox.id/padi-umkm/1.0.0/padis';
    var $INTERNAL_API_KEY='TnsdFYBymK8CO6VZeiwfXh32G1jAvMJWxSokI5cNuzbpHPgErQa0tDULq4l9R7';

    public function JENIS_KATEGORI_USAHA($id=null){
        if($id!=null) $this->db->where('id',$id);
        return $this->db->get('padiumkm_jenis_kategori_usaha')->result();
    }
    public function JENIS_KEG_USAHA($id=null){
        if($id!=null) $this->db->where('id',$id);
        return $this->db->get('padiumkm_jenis_kegiatan_usaha')->result();
    }

    public function KATEGORI_PROJECT($id=null){
        if($id!=null) $this->db->where('id',$id);
        return $this->db->get('padiumkm_kategori_project')->result();
    }
    public function RATING_PROJECT($id=null){
        if($id!=null) $this->db->where('id',$id);
        return $this->db->get('padiumkm_rating')->result();
    }
    public function KATEGORI_DELIVERY_TIME($id=null){
        if($id!=null) $this->db->where('id',$id);
        return $this->db->get('padiumkm_rating')->result();
    }

    private function curlProcessor($data=[]){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->BASE_URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'
        ,'X-API-KEY:'.$this->API_KEY));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $result = curl_exec($ch);
        curl_close($ch);
        return json_decode($result,TRUE);   
    }

    public function searchProject($limit=10,$search=null,$addFilter=null){
     $limit=$limit!=null?$limit:10;
     $params=[];
     $sql="SELECT project.*
     ,date(project.start_date) as start_date
     ,date(project.end_date) as end_date
     ,(SELECT price FROM project_bidding where id_project=project.id order by created_at desc limit 1)
     as final_price
     ,winner_sap.id_sap
     ,concat(if(winner_profile.prefix_name is null,'',concat(winner_profile.prefix_name,' ')),replace(winner_profile.name,winner_profile.prefix_name,'') ) as vendor_name
     ,(select data_json from padiumkm_data_umkm where vendor_sap_id=winner_sap.id_sap) as padiumkm_data
     FROM project
     INNER JOIN tbl_sync_sap winner_sap on winner_sap.id_company=project.winner
     INNER JOIN company_profile winner_profile on winner_profile.id=project.winner
     WHERE project.deleted_at is null
     and project.status=3
     and winner is not null";
     if($addFilter!=null){
         $f=$addFilter();
         $sql.=$f['sql'];
         foreach($f['params'] as $p){
             $params[]=$p;
         }
     }
     else{
         $sql.=" AND project.id not in (select project_id from padiumkm_data_transaksi)";
     }
     
     if($search!=null){
         $sql.=" AND ( 
         lower(project.id) like ?
         OR lower(contract_no) like ?
         OR lower((SELECT price FROM project_bidding where id_project=project.id order by created_at desc limit 1)) like ?
         OR lower(winner_sap.id_sap) like ?
         OR lower(concat(if(winner_profile.prefix_name is null,'',concat(winner_profile.prefix_name,' ')),replace(winner_profile.name,winner_profile.prefix_name,'') )) like ?
         OR lower(project.name) like ?
         OR lower(project.description) like ?
         )";
         for($i=0;$i<7;$i++){
             $params[]='%'.strtolower($search).'%';
         }
     }
     if($limit!=-1){
        $sql.=" LIMIT ?";
        $params[]=(int)$limit;
     }
     $data=$this->db->query($sql,$params)->result();
     $data=array_map(function($d){
         $d->description=strip_tags($d->description);
         return $d;
     },$data);
     return $data;

    }

    public function searchVendor($limit=10,$search=null,$addFilter=null){
        $limit=$limit!=null?$limit:10;
        $params=[];
        $sql="SELECT sap.*
        ,compro.id_user
        ,compro.prefix_name
        ,compro.name
        ,compro.postfix_name
        ,concat(if(compro.prefix_name is null,'',concat(compro.prefix_name,' ')),replace(compro.name,compro.prefix_name,'') ) as vendor_name
        ,compro.id_group
        ,compro.authorized_capital
        ,compro.paid_up_capital
        ,grp.description as group_name
        ,npwp.no as npwp
        ,contact.work_area
        ,contact.address
        ,contact.building_no
        ,usr.email
        ,contact.email as email_for_contact
        ,contact.pos_code
        ,contact.phone
        ,contact.id_city
        ,contact.id_country
        ,contact.id_country_province
        ,city.name as city_name
        ,prov.name as prov_name
        ,country.name as country_name
        ,bank.no as bank_no
        ,bank.bank_name
        ,bank.owner as bank_owner
        ,(select data_json from padiumkm_data_umkm where vendor_sap_id=sap.id_sap) as padiumkm_data
        FROM tbl_sync_sap sap
        INNER JOIN company_profile compro on sap.id_company=compro.id
        INNER JOIN sys_user usr on (usr.id_user=compro.id_user and usr.is_blacklisted=0)
        INNER JOIN m_group grp on grp.id=compro.id_group
        INNER JOIN company_legal_npwp npwp on npwp.id_company=compro.id
        INNER JOIN company_contact contact on contact.id_company=compro.id
        INNER JOIN m_city city on city.id=contact.id_city
        INNER JOIN m_country_province prov on prov.id=contact.id_country_province
        INNER JOIN m_country country on country.id=contact.id_country
        INNER JOIN company_finance_bank bank on bank.id_company=compro.id
        WHERE sap.id_sap is not null";
        if($addFilter!=null){
            $f=$addFilter();
            $sql.=$f['sql'];
            foreach($f['params'] as $p){
                $params[]=$p;
            }
        }
        else{
            $sql.=" AND sap.id_sap not in (select vendor_sap_id from padiumkm_data_umkm)";
        }
        
        if($search!=null){
            $sql.=" AND ( 
            lower(sap.id_sap) like ?
            OR lower(compro.id_user) like ?
            OR lower(npwp.no) like ?
            OR lower(contact.email) like ?
            OR lower(usr.email) like ?
            OR lower (concat(if(compro.prefix_name is null,'',concat(compro.prefix_name,' ')),replace(compro.name,compro.prefix_name,'') )) like ?
            )";
            for($i=0;$i<6;$i++){
                $params[]='%'.strtolower($search).'%';
            }
        }
        if($limit!=-1){
           $sql.=" LIMIT ?";
           $params[]=(int)$limit;
        }
        return $this->db->query($sql,$params)->result();
   
    }
    
    public function getListUMKM($page=1,$size=10){
        return $this->curlProcessor([
            'get_umkm'=>[
                'size'=>$size,
                'page'=>$page
            ]
        ]);
    }

    public function getListUMKMFromDB($withLimit=true, $limit=10, $offset=0, $addFilter=null){
        $params=[];
        $sql="
        select 
        padiumkm_data_umkm.id
        ,padiumkm_data_umkm.vendor_sap_id
        ,REPLACE(JSON_EXTRACT(data_json, '$.uid'),'\"','') AS uid
        ,REPLACE(JSON_EXTRACT(data_json, '$.nama_umkm'),'\"','') AS nama_umkm
        ,REPLACE(JSON_EXTRACT(data_json, '$.alamat'),'\"','') AS alamat
        ,REPLACE(JSON_EXTRACT(data_json, '$.blok_no_kav'),'\"','') AS blok_no_kav
        ,REPLACE(JSON_EXTRACT(data_json, '$.kode_pos'),'\"','') AS kode_pos
        ,REPLACE(JSON_EXTRACT(data_json, '$.kota'),'\"','') AS kota
        ,REPLACE(JSON_EXTRACT(data_json, '$.provinsi'),'\"','') AS provinsi
        ,REPLACE(JSON_EXTRACT(data_json, '$.no_telp'),'\"','') AS no_telp
        ,REPLACE(JSON_EXTRACT(data_json, '$.hp'),'\"','') AS hp
        ,REPLACE(JSON_EXTRACT(data_json, '$.email'),'\"','') AS email
        ,REPLACE(JSON_EXTRACT(data_json, '$.kategori_usaha'),'\"','') AS kategori_usaha
        ,REPLACE(JSON_EXTRACT(data_json, '$.jenis_kegiatan_usaha'),'\"','') AS jenis_kegiatan_usaha
        ,REPLACE(JSON_EXTRACT(data_json, '$.npwp'),'\"','') AS npwp
        ,REPLACE(JSON_EXTRACT(data_json, '$.nama_bank'),'\"','') AS nama_bank
        ,REPLACE(JSON_EXTRACT(data_json, '$.country_bank'),'\"','') AS country_bank
        ,REPLACE(JSON_EXTRACT(data_json, '$.no_rekening'),'\"','') AS no_rekening
        ,REPLACE(JSON_EXTRACT(data_json, '$.nama_pemilik_rekening'),'\"','') AS nama_pemilik_rekening
        ,REPLACE(JSON_EXTRACT(data_json, '$.longitute'),'\"','') AS longitute
        ,REPLACE(JSON_EXTRACT(data_json, '$.latitute'),'\"','') AS latitute
        ,REPLACE(JSON_EXTRACT(data_json, '$.total_project'),'\"','') AS total_project
        ,REPLACE(JSON_EXTRACT(data_json, '$.total_revenue'),'\"','') AS total_revenue
        ,REPLACE(JSON_EXTRACT(data_json, '$.ontime_rate'),'\"','') AS ontime_rate
        ,REPLACE(JSON_EXTRACT(data_json, '$.average_rating'),'\"','') AS average_rating
        ,padiumkm_data_umkm.created_at as timestamp
        ,'itdev_bgr' as `user`
        ,kat_usaha.name as kat_usaha_name
        ,keg_usaha.name as keg_usaha_name
        from padiumkm_data_umkm
        LEFT JOIN padiumkm_jenis_kategori_usaha kat_usaha on kat_usaha.id=REPLACE(JSON_EXTRACT(data_json, '$.kategori_usaha'),'\"','')
        LEFT JOIN padiumkm_jenis_kegiatan_usaha keg_usaha on keg_usaha.id=REPLACE(JSON_EXTRACT(data_json, '$.jenis_kegiatan_usaha'),'\"','')
        WHERE padiumkm_data_umkm.id is not null
        ";

        if($addFilter!=null){
            $filter=$addFilter();
            $sql.=" ".$filter['sql'];
            foreach($filter['params'] as $f){
                $params[]=$f;
            }
        }

        $countData=$this->db->query($sql,$params)->num_rows();
        $params=[];

        $sql=$this->db->last_query();
        if($withLimit){
            $sql.=" LIMIT ? OFFSET ?";
            $params[]=(int) $limit;
            $params[]=(int) $offset;
        }
        // $this->db->query($sql,$params)->result();
        // echo $this->db->last_query();
        // die();

        return [
            'data'=>$this->db->query($sql,$params)->result(),
            'count'=>$countData
        ];
    }

    public function getListTransaksiFromDB($withLimit=true, $limit=10, $offset=0, $addFilter=null){
        $params=[];
        $sql="
        select 
         padiumkm_data_transaksi.id
        ,padiumkm_data_transaksi.vendor_sap_id
        ,padiumkm_data_transaksi.project_id
        ,REPLACE(JSON_EXTRACT(data_json, '$.tanggal_transaksi'),'\"','') AS tanggal_transaksi
        ,REPLACE(JSON_EXTRACT(data_json, '$.transaksi_id'),'\"','') AS transaksi_id
        ,REPLACE(JSON_EXTRACT(data_json, '$.bumn_id'),'\"','') AS bumn_id
        ,REPLACE(JSON_EXTRACT(data_json, '$.nama_project'),'\"','') AS nama_project
        ,REPLACE(JSON_EXTRACT(data_json, '$.kategori_project'),'\"','') AS kategori_project
        ,REPLACE(JSON_EXTRACT(data_json, '$.total_nilai_project'),'\"','') AS total_nilai_project
        ,REPLACE(JSON_EXTRACT(data_json, '$.tipe_nilai_project'),'\"','') AS tipe_nilai_project
        ,REPLACE(JSON_EXTRACT(data_json, '$.kategori_umkm'),'\"','') AS kategori_umkm
        ,REPLACE(JSON_EXTRACT(data_json, '$.uid_umkm'),'\"','') AS uid_umkm
        ,REPLACE(JSON_EXTRACT(data_json, '$.nama_umkm'),'\"','') AS nama_umkm
        ,REPLACE(JSON_EXTRACT(data_json, '$.target_penyelesaian'),'\"','') AS target_penyelesaian
        ,REPLACE(JSON_EXTRACT(data_json, '$.tanggal_order_placement'),'\"','') AS tanggal_order_placement
        ,REPLACE(JSON_EXTRACT(data_json, '$.tanggal_confirmation'),'\"','') AS tanggal_confirmation
        ,REPLACE(JSON_EXTRACT(data_json, '$.tanggal_delivery'),'\"','') AS tanggal_delivery
        ,REPLACE(JSON_EXTRACT(data_json, '$.tannggal_invoice'),'\"','') AS tannggal_invoice
        ,REPLACE(JSON_EXTRACT(data_json, '$.total_cycle_time'),'\"','') AS total_cycle_time
        ,REPLACE(JSON_EXTRACT(data_json, '$.kategori_delivery_time'),'\"','') AS kategori_delivery_time
        ,REPLACE(JSON_EXTRACT(data_json, '$.rating'),'\"','') AS rating
        ,REPLACE(JSON_EXTRACT(data_json, '$.feedback'),'\"','') AS feedback
        ,REPLACE(JSON_EXTRACT(data_json, '$.deskripsi_project'),'\"','') AS deskripsi_project
        ,padiumkm_data_transaksi.created_at as timestamp
        ,'itdev_bgr' as `user`
        ,kat_usaha.name as kat_usaha_name
        ,kat_project.name as kat_project_name
        ,delivery_time.name as delivery_time_name
        ,rating.name as rating_name
        from padiumkm_data_transaksi
        LEFT JOIN padiumkm_jenis_kategori_usaha kat_usaha on kat_usaha.id=REPLACE(JSON_EXTRACT(data_json, '$.kategori_umkm'),'\"','')
        LEFT JOIN padiumkm_kategori_project kat_project on kat_project.id=REPLACE(JSON_EXTRACT(data_json, '$.kategori_project'),'\"','')
        LEFT JOIN padiumkm_delivery_time delivery_time on delivery_time.id=REPLACE(JSON_EXTRACT(data_json, '$.kategori_delivery_time'),'\"','')
        LEFT JOIN padiumkm_rating rating on rating.id=REPLACE(JSON_EXTRACT(data_json, '$.rating'),'\"','')
        WHERE padiumkm_data_transaksi.id is not null
        ";

        if($addFilter!=null){
            $filter=$addFilter();
            $sql.=" ".$filter['sql'];
            foreach($filter['params'] as $f){
                $params[]=$f;
            }
        }

        $countData=$this->db->query($sql,$params)->num_rows();
        $params=[];

        $sql=$this->db->last_query();
        if($withLimit){
            $sql.=" LIMIT ? OFFSET ?";
            $params[]=(int) $limit;
            $params[]=(int) $offset;
        }


        return [
            'data'=>$this->db->query($sql,$params)->result(),
            'count'=>$countData
        ];
    }

    public function getListTransaksi($page=1,$size=10){
        return $this->curlProcessor([
            'get_transaksi'=>[
                'size'=>$size,
                'page'=>$page
            ]
        ]);
    } 

    public function addListUMKM($data=[]){
        $existingData=$this->db
        ->where('vendor_sap_id',$data['uid'])
        ->get('padiumkm_data_umkm')->row();
        if($existingData!=null){
            // $input=[
            //     'status'=>'already_exist',
            //     'messages'=>'Data has been exist'
            // ];
            $this->deleteDataUMKM([
                'uid'=>[$existingData->vendor_sap_id]
            ]);
            $input=[
                'status'=>'success',
                'messages'=>'Data has been updated'
            ];
            $input=$this->curlProcessor([
                'umkm'=>$data
            ]);
            if($input['status']=='success'){
                if($this->db
                ->where('vendor_sap_id',$data['uid'])
                ->get('padiumkm_data_umkm')->row()
                ==null){
                    $this->db->insert('padiumkm_data_umkm',[
                        'vendor_sap_id'=>$data['uid'],
                        'data_json'=>json_encode($data,JSON_UNESCAPED_SLASHES)
                    ]);
                }
            }
        }
        else{
            $input=$this->curlProcessor([
                'umkm'=>$data
            ]);
            if($input['status']=='success'){
                if($this->db
                ->where('vendor_sap_id',$data['uid'])
                ->get('padiumkm_data_umkm')->row()
                ==null){
                    $this->db->insert('padiumkm_data_umkm',[
                        'vendor_sap_id'=>$data['uid'],
                        'data_json'=>json_encode($data,JSON_UNESCAPED_SLASHES)
                    ]);
                }
            }
        }
        
        return $input;
    }

    public function addListTransaksi($data=[]){
        $existingData=$this->db
        ->where('project_id',$data['transaksi_id'])
        ->get('padiumkm_data_transaksi')->row();
        if($existingData!=null){
            // $input=[
            //     'status'=>'already_exist',
            //     'messages'=>'Data has been exist'
            // ];
            $this->deleteDataTransaksi([
                'transaksi_id'=>[$existingData->project_id]
            ]);
            $input=[
                'status'=>'success',
                'messages'=>'Data has been updated'
            ];
            $input=$this->curlProcessor([
                'transaksi'=>$data
            ]);
            if($input['status']=='success'){
                if($this->db
                ->where('project_id',$data['transaksi_id'])
                ->get('padiumkm_data_transaksi')->row()
                ==null){
                    $this->db->insert('padiumkm_data_transaksi',[
                        'project_id'=>$data['transaksi_id'],
                        'vendor_sap_id'=>$data['uid_umkm'],
                        'data_json'=>json_encode($data,JSON_UNESCAPED_SLASHES)
                    ]);
                }
            }
        }
        else{
            $input=$this->curlProcessor([
                'transaksi'=>$data
            ]);
            if($input['status']=='success'){
                if($this->db
                ->where('project_id',$data['transaksi_id'])
                ->get('padiumkm_data_transaksi')->row()
                ==null){
                    $this->db->insert('padiumkm_data_transaksi',[
                        'project_id'=>$data['transaksi_id'],
                        'vendor_sap_id'=>$data['uid_umkm'],
                        'data_json'=>json_encode($data,JSON_UNESCAPED_SLASHES)
                    ]);
                }
            }
        }
        
        return $input;
    }

    public function deleteDataUMKM($data=[]){
        $input=$this->curlProcessor([
            'delete_umkm'=>$data
        ]);
        if($input['status']=='success'){
            if($this->db
            ->where('vendor_sap_id',$data['uid'][0])
            ->get('padiumkm_data_umkm')->row()
            !=null){
                $this->db->delete('padiumkm_data_umkm',[
                    'vendor_sap_id'=>$data['uid'][0]
                ]);
            }
        }

        return $input;
    }

    public function deleteDataTransaksi($data=[]){
        $input=$this->curlProcessor([
            'delete_transaksi'=>$data
        ]);
        if($input['status']=='success'){
            if($this->db
            ->where('project_id',$data['transaksi_id'][0])
            ->get('padiumkm_data_transaksi')->row()
            !=null){
                $this->db->delete('padiumkm_data_transaksi',[
                    'project_id'=>$data['transaksi_id'][0]
                ]);
            }
        }

        return $input;
    }
}