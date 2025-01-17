<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class App_Model extends CI_Model
{

    // TABLES
    const TBL_ACTIVITY_LOG = 'sys_activity_log';
    const TBL_MENU = 'sys_menu';
    const TBL_MENU_PRIVILEGE = 'sys_menu_privilege';
    const TBL_SETTINGS = 'sys_settings';
    const TBL_USER = 'sys_user';
    const TBL_PASSWORD_CHANGE = 'sys_usr_password_change';
    const TBL_LOGIN_SESSION = 'sys_login_session';
    const TBL_USR_ROLE = 'sys_usr_role';
    const TBL_USR_STATUS = 'sys_usr_status';
    const TBL_USR_VERIFICATION = 'sys_usr_verification';
    const TBL_SETTINGS_EMAIL = 'sys_settings_email';

    const TBL_COMPANY_PROFILE = 'company_profile';
    const TBL_COMPANY_TYPE_LIST = 'company_type';
    const TBL_COMPANY_WORK_AREA = 'company_work_area';
    const TBL_COMPANY_CABANG_AREA = 'company_cabang_area';
    const TBL_COMPANY_PIC = 'company_pic';
    const TBL_COMPANY_CONTACT = 'company_contact';
    const TBL_COMPANY_LEGAL_DOMICILE = 'company_legal_domicile';
    const TBL_COMPANY_LEGAL_DOC = 'company_legal_doc';
    const TBL_COMPANY_LEGAL_NPWP = 'company_legal_npwp';
    const TBL_COMPANY_LEGAL_NIB = 'company_legal_nib';
    const TBL_COMPANY_LEGAL_SIUP = 'company_legal_siup';
    const TBL_COMPANY_LEGAL_TDP = 'company_legal_tdp';
    const TBL_COMPANY_BORN_LICENSE = 'company_legal_born_license';
    const TBL_COMPANY_MANAGEMENT = 'company_management';
    const TBL_COMPANY_FINANCE_BANK = 'company_finance_bank';
    const TBL_COMPANY_FINANCE_REPORT = 'company_finance_report';
    const TBL_COMPANY_CERTIFICATION = "company_certification";
    const TBL_COMPANY_FACILITIES = "company_facilities";
    const TBL_COMPANY_EXPERIENCE = "company_experience";
    const TBL_COMPANY_COMPETENCIES = "company_competencies";
    const TBL_COMPANY_DOCUMENT = "company_document";
    const TBL_COMPANY_PAKTA = "company_pakta";
    const TBL_NOTIFICATION = "tbl_notification";
    const TBL_REQUIRED_FORM = "tbl_required_form";
    const TBL_SAP_SYNC = "tbl_sync_sap";
    const TBL_COMPANY_CATALOGUE = "company_catalogue";
    const TBL_PROjECT = "project";

    //MASTER
    const TBL_COMPANY_TYPE = "m_company_type";
    const TBL_COMPANY_CLASSIFICATION = "m_company_classification";
    const TBL_COMPANY_COMPETENCY = "m_company_competency";
    const TBL_COMPANY_SUB_COMPETENCY = "m_company_sub_competency";
    const TBL_WORK_AREA = "m_work_area";
    const TBL_COUNTRY = "m_country";
    const TBL_COUNTRY_PROVINCE = "m_country_province";
    const TBL_CITY = "m_city";
    const TBL_BUSINESS_TYPE = "m_business_type";
    const TBL_CURRENCY = "m_currency";
    const TBL_CERTIFICATE_TYPE = "m_certificate_type";
    const TBL_FACILITIES_TYPE = "m_facilities_type";
    const TBL_GROUP_VENDOR = "m_group";
    const TBL_BANK = "m_bank_list";
    const TBL_BRANCH = "m_branch_code";
    



    // STATUS
    const STAT_ACCOUNT_VERIFY = 1;
    const STAT_ACCOUNT_ACTIVE = 2;
    const STAT_ACCOUNT_BLOCKED = 3;
    const STAT_ACCOUNT_VERIFY_PROFILE = 5;
    const STAT_ACCOUNT_DELETED = 4;
    const STAT_ACCOUNT_WAITING_VALIDATING_PROFILE = 6;

    // FIELDS
    const FIELD_CREATED_AT = 'created_at';
    const FIELD_UPDATED_AT = 'updated_at';
    const FIELD_DELETED_AT = 'deleted_at';
    const FIELD_IDENTITY = 'email';

    //ROLE
    const ROLE_ADMIN = 1;
    const ROLE_VERIFICATOR = 5;
    const ROLE_VENDOR = 2;
    const ROLE_VENDOR_PERSONAL = 6;
    const ROLE_VENDOR_GROUP = 7;
    const ROLE_CUSTOMER = 3;
    const ROLE_DISPATCHER = 4;

    // SQLS
    const SQL_IS_NULL = ' is null';

    //Verification Status
    const VERIFICATION_STATUS_PENDING = "Pending Verification";
    const VERIFICATION_STATUS_VERIFIED = "Verified";
    const VERIFICATION_STATUS_REJECTED = "Rejected";

    const SESS_KEY_LOGIN = 'is_logged_in';
    const SESS_KEY_USER_DATA = 'user';

    public function __construct()
    {
        parent::__construct();
        $timezone = get_settings()->timezone;
        date_default_timezone_set($timezone);
    }

    public function secure_input($input, $settings = [])
    {
        App_Controller::secure_input($input, $settings);
    }

    public function createSSDModel($masterQuery
    ,$selectQuery
    ,$countQuery
    ,$selectReplacePattern="{selectQuery}"
    ,$orderFields=[]
    ,$searchField=[]
    ,$withLimit=true,$addParams=[
        'limit'=>10,
        'offset'=>0,
        'search'=>null,
        'orderBy'=>0,
        'orderDir'=>'asc',
    ],$addFilter=null){
        $sql=$masterQuery;
        $params=[];
        if($addParams['search'] && $searchField!=null && is_array($searchField)){

            $sql.=" AND (";
            $j=0;
            foreach($searchField as $d){
                $j++;
                $sql.=($j>1?' OR':'')." lower($d) like ?";
            }
            $sql.=" )";

            for($i=0;$i<$j;$i++){
                $params[]='%'.strtolower($addParams['search']).'%';
            }
        }

        if($addFilter!=null){
            $filter=$addFilter();
            if(isset($filter['sql']) && isset($filter['params'])){
                $sql.=" ".$filter['sql'];
                foreach($filter['params'] as $p){
                    $params[]=$p;
                }
            }
        }

        $countData=$this->db->query(str_replace($selectReplacePattern,"count($countQuery) as total",$sql),$params)->row()->total;
        
        $sql.=" ORDER BY ".$orderFields[$addParams['orderBy']]." ".$addParams['orderDir'];
        if($withLimit){
            $sql.=" LIMIT ? OFFSET ?";
            $params[]=(int) $addParams['limit'];
            $params[]=(int) $addParams['offset'];
        }

        $data=$this->db->query(str_replace($selectReplacePattern,$selectQuery,$sql),$params)->result();

        return [
            'data'=>$data,
            'recordsTotal'=>$countData,
            'recordsFiltered'=>$countData,
        ];
    }
}
