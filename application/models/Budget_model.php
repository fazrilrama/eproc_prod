<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Budget_model extends App_Model
{
    public function getList($withLimit=true,$addParams=[
        'limit'=>10,
        'offset'=>0,
        'search'=>null,
        'orderBy'=>0,
        'orderDir'=>'asc',
    ]
    ,$orderFields=[]
    ,$addFilter=null){
        return $this->createSSDModel(
        "
        SELECT {selectQuery}
        FROM m_budget a
        INNER JOIN m_branch_code b ON a.owner_code=b.no_fund_center
        WHERE 
        a.deleted_at is null
        "
        ,
        "a.*
        , b.name as branch_name
        , b.official_code as branch_code
        , IF(type=1,'Operasional','Non-Operasional') as type_name
        , (SELECT COUNT(id) from shopping_cart where buyer_id in (
            SELECT id_user from sys_user where branch_code=b.id
            )
            and status !=13
            and status !=14
            and status !=8
            and status !=9
        ) as pending_order
        ,b.no_fund_center
        ,a.id"
        
        ,"a.id"
        ,"{selectQuery}"
        ,$orderFields
        ,['b.name','no_fund_center','id_company_owner','a.time']
        ,$withLimit
        ,$addParams
        ,$addFilter);
    }
}