<?php
function RNIAPIDeleteData($type=RNI_API_TYPE_trans_production_real,$filter=[]){
    $data=[
        'company_id'=>RNI_API_COMPANY_ID,
        'type'=>$type,
        'filter'=>$filter
    ];
    return curlProcessor(RNI_API_BASE_URL.'/delete','POST',[
        'Content-Type: '.RNI_API_CONTENT_TYPE_JSON,
        'Authorization: Bearer '.RNI_API_ACCESS_TOKEN,
    ],"object",json_encode($data));
}