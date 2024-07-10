<?php
defined('BASEPATH') or exit('No direct script access allowed');

class File_manage extends App_Controller
{
    function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'upload'],
                ['method' => 'delete'],
            ]
        ]);
    }

    const PATH_UPLOAD_COMPANY_LOGO = FCPATH . '/upload/company/logo/';
    const PATH_UPLOAD_COMPANY_FILE = FCPATH . '/upload/company/file/';

    public function upload($case = null)
    {
        switch ($case) {
            case 'logo': {
                    $fieldName = $this->input->post('fieldName');
                    $this->upload_company_logo($fieldName);
                    break;
                }
            case 'company_file': {
                    $fieldName = $this->input->post('fieldName');
                    $this->upload_company_file($fieldName);
                    break;
                }
        }
    }

    public function delete($context)
    {
        switch ($context) {
            case 'logo': {
                    $filename = $this->input->post('filename');
                    $this->delete_company_logo($filename);
                    break;
                }
            case 'company_file': {
                    $filename = $this->input->post('filename');
                    $this->delete_company_file($filename);
                    break;
                }
        }
    }

    private function upload_company_logo($fieldName)
    {

        $result['jquery-upload-file-error'] = 'File field not uploaded!';
        $result['fieldName'] = $fieldName;

        if ($fieldName != null) {
            if (isset($_FILES[$fieldName])) {
                // $error = $_FILES[$fieldName]["error"];
                if (!is_array($_FILES[$fieldName]["name"])) {
                    $config['upload_path']          = self::PATH_UPLOAD_COMPANY_LOGO;
                    $config['allowed_types']        = 'png|jpg|jpeg';
                    $config['max_size']             = 1024;
                    $config['remove_spaces']        = true;
                    $config['encrypt_name']         = true;
                    $this->load->library('upload', $config);
                    if (!$this->upload->do_upload($fieldName)) {
                        $result['jquery-upload-file-error'] = $this->upload->display_errors();
                    } else {
                        $result[0] = $this->upload->data();
                    }
                } else {
                    //Multiple files, file[]
                    $fileCount = count($_FILES[$fieldName]["name"]);
                    for ($i = 0; $i < $fileCount; $i++) {
                        $config['upload_path']          = self::PATH_UPLOAD_COMPANY_LOGO;
                        $config['allowed_types']        = 'png|jpg|jpeg';
                        $config['max_size']             = 1024;
                        $config['remove_spaces']        = true;
                        $config['encrypt_name']         = true;
                        $this->load->library('upload', $config);
                        if (!$this->upload->do_upload($fieldName)) {
                            $result['jquery-upload-file-error'] = $this->upload->display_errors();
                        } else {
                            $result[$i] = $this->upload->data();
                        }
                    }
                }
            }
        }

        echo json_encode($result);
    }


    private function upload_company_file($fieldName)
    {

        $result['jquery-upload-file-error'] = 'File field not uploaded!';
        $result['fieldName'] = $fieldName;

        if ($fieldName != null) {
            if (isset($_FILES[$fieldName])) {
                // $error = $_FILES[$fieldName]["error"];
                if (!is_array($_FILES[$fieldName]["name"])) {
                    $config['upload_path']          = self::PATH_UPLOAD_COMPANY_FILE;
                    $config['allowed_types']        = 'pdf|png|jpg|jpeg';
                    $config['max_size']             = 2048;
                    $config['remove_spaces']        = true;
                    $config['encrypt_name']         = true;
                    $this->load->library('upload', $config);
                    if (!$this->upload->do_upload($fieldName)) {
                        $result['jquery-upload-file-error'] = $this->upload->display_errors();
                    } else {
                        $result[0] = $this->upload->data();
                    }
                } else {
                    //Multiple files, file[]
                    $fileCount = count($_FILES[$fieldName]["name"]);
                    for ($i = 0; $i < $fileCount; $i++) {
                        $config['upload_path']          = self::PATH_UPLOAD_COMPANY_FILE;
                        $config['allowed_types']        = 'pdf|png|jpg|jpeg';
                        $config['max_size']             = 2048;
                        $config['remove_spaces']        = true;
                        $config['encrypt_name']         = true;
                        $this->load->library('upload', $config);
                        if (!$this->upload->do_upload($fieldName)) {
                            $result['jquery-upload-file-error'] = $this->upload->display_errors();
                        } else {
                            $result[$i] = $this->upload->data();
                        }
                    }
                }
            }
        }

        echo json_encode($result);
    }

    private function delete_company_logo($filename)
    {
        $result = [
            'success' => false,
            'message' => 'Filename is not defined!'
        ];

        $file_path = self::PATH_UPLOAD_COMPANY_LOGO . $filename;
        if (file_exists($file_path) === TRUE) {
            unlink($file_path);
            $result = [
                'success' => true,
                'message' => 'File is deleted!'
            ];
        } else {
            $result = [
                'success' => true,
                'message' => 'File is deleted!'
            ];
        }
        echo json_encode($result);
    }

    private function delete_company_file($filename)
    {
        $result = [
            'success' => false,
            'message' => 'Filename is not defined!'
        ];

        $file_path = self::PATH_UPLOAD_COMPANY_FILE . $filename;
        if (file_exists($file_path) === TRUE) {
            unlink($file_path);
            $result = [
                'success' => true,
                'message' => 'File is deleted!'
            ];
        } else {
            $result = [
                'success' => true,
                'message' => 'File is deleted!'
            ];
        }
        echo json_encode($result);
    }
}
