<?php
define('RNI_API_PRODUCTION',false);
if(RNI_API_PRODUCTION){
    define('RNI_API_BASE_URL','https://103.140.207.246/eis/data/eis');
}
else{
    define('RNI_API_BASE_URL','https://103.140.207.246/eis/data/eis');
}
//global curl log
$GLOBALS['RNI_API_CURL_LOG_HANDLER'] ="RNI_API_WriteCurlLog";

define("RNI_API_CONTENT_TYPE_JSON", "application/json");
define("RNI_API_TYPE_trans_production_real","trans_production_real");
define("RNI_API_TYPE_trans_purchasing_real","trans_purchasing_real");
define("RNI_API_TYPE_trans_procurement_real","trans_procurement_real");
define("RNI_API_TYPE_sdm_trans_data_karyawan","sdm_trans_data_karyawan");
define("RNI_API_PROCURMENT_STATUS_REQ","req");
define("RNI_API_PROCURMENT_STATUS_PROCESS","process");
define("RNI_API_PROCURMENT_STATUS_DONE","done");