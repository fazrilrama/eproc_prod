<?php
include_once('config/api.php');
include_once('util/curlProcessor.php');

class RNI_API_Helper{
    var $COMPANY_ID=null;
    var $API_KEY=null;

    public function __construct($COMPANY_ID,$API_KEY) {
        $this->COMPANY_ID=$COMPANY_ID;
        $this->API_KEY=$API_KEY;
    }

    public function setCompanyID($COMPANY_ID){
        $this->COMPANY_ID=$COMPANY_ID;
        return $this;
    }

    public function setAPIKey($API_KEY){
        $this->API_KEY=$API_KEY;
        return $this;
    }

    function RNIAPIInsertProcurement($dataItem=[]){
        $data=[
            'company_id'=>$this->COMPANY_ID,
            'type'=>RNI_API_TYPE_trans_procurement_real,
            'data'=>$dataItem,
        ];
        return curlProcessor(RNI_API_BASE_URL.'/insert','POST',[
            'Content-Type: '.RNI_API_CONTENT_TYPE_JSON,
            'Authorization: Bearer '.$this->API_KEY,
        ],"object",json_encode($data));
    }

    function RNIAPIReadData($type=RNI_API_TYPE_trans_production_real,$filter=[]){
        $data=[
            'company_id'=>$this->COMPANY_ID,
            'type'=>$type,
            'filter'=>$filter
        ];
        return curlProcessor(RNI_API_BASE_URL.'/get','POST',[
            'Content-Type: '.RNI_API_CONTENT_TYPE_JSON,
            'Authorization: Bearer '.$this->API_KEY,
        ],"object",json_encode($data));
    }

    function RNIAPIDeleteData($type=RNI_API_TYPE_trans_production_real,$filter=[]){
        $data=[
            'company_id'=>$this->COMPANY_ID,
            'type'=>$type,
            'filter'=>$filter
        ];
        return curlProcessor(RNI_API_BASE_URL.'/delete','POST',[
            'Content-Type: '.RNI_API_CONTENT_TYPE_JSON,
            'Authorization: Bearer '.$this->API_KEY,
        ],"object",json_encode($data));
    }
}