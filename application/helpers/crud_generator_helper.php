<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *@category Internal Helpers
 *@author Riyan S.I (riyansaputrai007@gmail.com)
 */

function get_header($table_fields, $fields_exception, $inflated = [])
{
    $table_header = [];
    $j = 0;
    foreach ($table_fields as $f) {
        $is_exception = false;
        for ($i = 0; $i < count($fields_exception); $i++) {
            if ($fields_exception[$i] == $f) {
                $is_exception = true;
                break;
            }
        }

        if ($is_exception == false) {
            $is_inflated = false;
            foreach ($inflated as $key => $val) {
                if ($key == $f) {
                    $table_header[$f] = [
                        'text' => ucwords(str_replace('_', ' ', $f)),
                        'id' => $f
                    ];

                    $table_header[$f]['text'] = $val['text'];
                    $is_inflated = true;
                    break;
                }
            }
            if (!$is_inflated) {
                $table_header[$f] = [
                    'text' => ucwords(str_replace('_', ' ', $f)),
                    'id' => $f
                ];
            }
            $j++;
        }
    }

    $result = [
        'raw' => $table_header, 'header_text' => [], 'header_id' => []
    ];

    $i = 0;
    foreach ($table_header as $h) {
        $result['header_text'][$i] = $h['text'];
        $result['header_id'][$i] = $h['id'];
        $i++;
    }

    return $result;
}

function form_builder($table, $fields_exception = [], $attr_inflater = [], $label_inflater = [], $footer_note = [])
{
    $html = '';
    $form = [];
    $i = 0;
    $ci = &get_instance();
    $field_data = $ci->db->field_data($table);
    foreach ($field_data as $f) {
        $is_exception = false;
        for ($j = 0; $j < count($fields_exception); $j++) {
            if ($fields_exception[$j] == $f->name) {
                $is_exception = true;
                break;
            }
        }
        if (!$is_exception) {

            $form_field = [
                'label' => [
                    'text' => ucwords(str_replace('_', ' ', $f->name)),
                    'id' => strtolower($f->name) . '_label'
                ],
                'input_field' => [
                    'inflater' => [],
                    'html' => '',
                    'attr' => [
                        'id' => strtolower($f->name),
                        'class' => 'form-control',
                        'placeholder' => ucwords(str_replace('_', ' ', $f->name)),
                        'name' => strtolower($f->name)
                    ]
                ]
            ];

            if (isset($attr_inflater[$f->name])) $form_field['input_field']['inflater'] = $attr_inflater[$f->name];
            if (isset($label_inflater[$f->name])) $form_field['label'] = $label_inflater[$f->name];

            // postgresql
            // if ($f->type == 'character varying') {
            //     $html = '<input type="text"';
            // } else if ($f->type == 'text') {
            //     $html = '<textarea type="textarea" ';
            // } else if ($f->type == 'timestamp without time zone') {
            //     $html = '<input type="date"';
            // } else if ($f->type == 'integer') {
            //     $html = '<input type="number"';
            // }

            // mysql
            if ($f->type == 'varchar') {
                $html = '<input type="text"';
            } else if ($f->type == 'text') {
                $html = '<textarea type="textarea" ';
            } else if ($f->type == 'datetime') {
                $html = '<input type="datetime-local"';
            } else if ($f->type == 'date') {
                $html = '<input type="date"';
            } else if ($f->type == 'int' || $f->type == 'bigint') {
                $html = '<input type="number"';
            }

            foreach ($form_field['input_field']['attr'] as $key => $val) {
                $is_inflated = false;
                $inf_key = '';
                $inf_val = '';
                foreach ($form_field['input_field']['inflater'] as $k => $v) {
                    if ($k == $key) {
                        $inf_key = $k;
                        $inf_val = $v;
                        $is_inflated = true;
                        break;
                    }
                }

                if (!$is_inflated) {
                    $html .= ' ' . $key . '= "' . $val . '"';
                } else {
                    $html .= ' ' . $inf_key . '="' . $inf_val . '"';
                }
            }

            foreach ($form_field['input_field']['inflater'] as $k => $v) {
                foreach ($form_field['input_field']['attr'] as $key => $val) {
                    if ($k != $key) {
                        if ($v == null) {
                            $html .= ' ' . $k;
                        } else {
                            $html .= ' ' . $k . '= "' . $v . '"';
                        }
                    }
                }
            }


            if ($f->type == 'text') {
                $html .= '></textarea>';
            } else {
                $html .= ' />';
            }

            if (isset($footer_note[$f->name])) $html .= '</b>' . $footer_note[$f->name];

            $form_field['input_field']['html'] = $html;

            $form[$i] = $form_field;
            $i++;
        }
    }

    return $form;
}

function add_data($table_name = null, $add_params = [])
{
    $ci = &get_instance();
    $table_name = ($table_name == null) ? $ci->input->post('_table') : $table_name;
    if ($table_name != null) {
        if (rcrud_add($table_name, $add_params) == true) {
            return (array('success' => true));
        } else {
            return (array('success' => false));
        }
    } else {

        return (array('success' => false));
    }
}

function get_data($table = null, $dataKey = null)
{
    $ci = &get_instance();
    $table = ($table == null) ? $ci->input->get('_table') : $table;
    $dataKey = ($dataKey == null) ? $ci->input->get('_key') : $dataKey;

    if ($table != null) {
        if ($dataKey != null) {
            $id = $ci->input->get($dataKey);
            if ($id != null) $ci->db->where($dataKey, $id);
        }
        $data = $ci->db
            ->where('deleted_at is null')
            ->order_by('updated_at desc')
            ->get($table)->result();
    } else {
        $data = [];
    }

    return ($data);
}

function edit_data($table = null, $edit_params = ['updated_at' => '0000-00-00 00:00:00'], $conditions = [])
{
    $edit_params['updated_at'] = current_datetime();

    $ci = &get_instance();
    $table = ($table == null) ? $ci->input->post('_table') : $table;
    if ($table != null) {
        if (rcrud_edit($table, $edit_params, $conditions)) {
            return (array('success' => true));
        } else {
            return (array('success' => false));
        }
    } else {
        return (array('success' => false));
    }
}

function delete_data($table = null, $dataKey = null)
{
    $ci = &get_instance();
    $success = false;
    $table = ($table == null) ? $ci->input->post('_table') : $table;
    $dataKey = ($dataKey == null) ? $ci->input->post('_key') : $dataKey;

    $id = $ci->input->post($dataKey);
    if ($id != null && $id != '' && $dataKey != null) {
        $success = $ci->db->where($dataKey, $id)->update(
            $table,
            [
                'deleted_at' => current_datetime()
            ]
        );
    }

    return ([
        'success' => $success
    ]);
}
