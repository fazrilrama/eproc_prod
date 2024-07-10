<?php

/**
 *@category Internal Helpers
 *@author Riyan S.I (riyansaputrai007@gmail.com)
 *Dynamic basic crud for any params
 */

function rcrud_get($table_name = "", $params = array())
{
    $CI = get_instance();
    $table_attr = $CI->db->list_fields($table_name);

    //Custom Select
    if (isset($params['rcrud_select'])) {
        $CI->db->limit($params['rcrud_select']);
    }

    //Joining
    if (isset($params['rcrud_join'])) {
        foreach ($params['rcrud_join'] as $join) {
            $join[2] = isset($join[2]) ? $join[2] : 'inner';
            if (isset($join[0]) && isset($join[1]) && isset($join[2])) {
                $CI->db->join($join[0], $join[1], $join[2]);
            }
        }
    }

    //Direct Where
    if (isset($params['rcrud_direct_where'])) {
        foreach ($params['rcrud_direct_where'] as $where) {
            $CI->db->where($where);
        }
    }

    //Default Table Fields Where
    foreach ($table_attr as $key) {
        $val = isset($params[$key]) ? $params[$key] : $CI->input->get($key);
        if ($val != null) {
            $CI->db->where($key, $val);
        }
    }

    // Ordering
    if (isset($params['rcrud_order'])) {
        foreach ($params['rcrud_order'] as $order) {
            $CI->db->order_by($order);
        }
    }

    //Limit
    if (isset($params['rcrud_limit'])) {
        $CI->db->limit($params['rcrud_limit']);
    }

    $data = $CI->db->get($table_name)->result_array();
    return $data;
}

function generate_uuid()
{
    $CI = get_instance();
    return $CI->db->query('Select uuid() as id')->row()->id;
}

function rcrud_add($table_name = "", $params = array())
{

    $CI = get_instance();
    $table_attr = $CI->db->list_fields($table_name);
    $data = array();
    foreach ($table_attr as $key) {
        $val = isset($params[$key]) ? $params[$key] : $CI->input->post($key);
        if ($val != null) {
            $data += array($key => $val);
        }
    }
    $CI->db->trans_start();
    $CI->db->insert($table_name, $data);
    $CI->db->trans_complete();
    if ($CI->db->trans_status() === TRUE) {
        $CI->db->trans_commit();
        return true;
    } else {
        $CI->db->trans_rollback();
        return false;
    }
}

function rcrud_edit($table_name = "", $params = array(), $conditions = array())
{

    $CI = get_instance();
    $table_attr = $CI->db->list_fields($table_name);

    $data = array();
    foreach ($table_attr as $key) {
        $val = isset($params[$key]) ? $params[$key] : $CI->input->post($key);
        if ($val != null) {
            if ($key != $table_attr[0]) {
                $data += array($key => $val);
            }
        }
    }


    $key = $table_attr[0];
    $id = $CI->input->post($key);
    if ($id != null) {
        $conditions = array($key => $id);
    }

    $condition = array();
    foreach ($table_attr as $key) {
        $val = isset($conditions[$key]) ? $conditions[$key] : null;
        if ($val != null) {
            $condition += array($key => $val);
        }
    }

    $CI->db->trans_start();
    $CI->db->where($condition);
    $CI->db->update($table_name, $data);
    $CI->db->trans_complete();
    if ($CI->db->trans_status() === TRUE) {
        $CI->db->trans_commit();
        return true;
    } else {
        $CI->db->trans_rollback();
        return false;
    }
}

function rcrud_delete($table_name = "", $params = array())
{
    $CI = get_instance();
    $table_attr = $CI->db->list_fields($table_name);
    $CI->db->trans_start();

    foreach ($table_attr as $key) {
        $val = isset($params[$key]) ? $params[$key] : $CI->input->get($key);
        if ($val != null) {
            $CI->db->where($key, $val);
        }
    }

    $this->delete($table_name);

    $CI->db->trans_complete();
    if ($CI->db->trans_status() === TRUE) {
        $CI->db->trans_commit();
        return true;
    } else {
        $CI->db->trans_rollback();
        return false;
    }
}
