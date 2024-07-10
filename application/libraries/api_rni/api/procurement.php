<?php
function RNIAPIInsertProcurement($dataItem=[]){
    $data=[
        'company_id'=>RNI_API_COMPANY_ID,
        'type'=>RNI_API_TYPE_trans_procurement_real,
        'data'=>$dataItem,
    ];
    return curlProcessor(RNI_API_BASE_URL.'/insert','POST',[
        'Content-Type'=>RNI_API_CONTENT_TYPE_JSON,
        'Authorization'=>'Bearer '.RNI_API_ACCESS_TOKEN,
    ],"object",json_encode($data));
}