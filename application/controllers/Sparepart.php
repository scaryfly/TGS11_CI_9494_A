<?php

use Restserver\Libraries\REST_Controller;

class Sparepart extends REST_Controller
{
	public function __construct()
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE');
		header('Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding');
		parent::__construct();
		$this->load->model('SparepartModel');
        $this->load->library('form_validation');
        $this->load->helper('date');
	}

	public function index_get()
	{
		$response = $this->SparepartModel->getAll();
		return $this->returnData($response['msg'], $response['error']);
	}

	public function index_post($id = null)
	{
		$validation = $this->form_validation;
		$rule = $this->SparepartModel->rules();
        array_push(
            $rule,
            [
                'field' => 'merk',
                'label' => 'merk',
                'rules' => 'required'
            ],

            [
                'field' => 'name',
                'label' => 'name',
                'rules' => 'required'
            ],
            [
                'field' => 'amount',
                'label' => 'amount',
                'rules' => 'required|integer'
            ]
        );
		$validation->set_rules($rule);
		if (!$validation->run()) {
			return $this->returnData($this->form_validation->error_array(), true);
		}
		$sparepart = new SparepartData();
		$sparepart->name = $this->post('name');
		$sparepart->merk = $this->post('merk');
        $sparepart->amount = $this->post('amount');
        $datestring = '%Y/%m/%d - %h:%i %a';
        $timestamp = time();
        $gmt = local_to_gmt(time());
        $timezone  = 'UP5';
        $daylight_saving = TRUE;
        $sparepart->created_at = mdate($datestring, gmt_to_local($timestamp, $timezone, $daylight_saving));
        
		if ($id == null) {
			$response = $this->SparepartModel->store($sparepart);
		} else {
			$response = $this->SparepartModel->update($sparepart, $id);
		}
		return $this->returnData($response['msg'], $response['error']);
	}

	public function index_delete($id = null)
	{
		if ($id == null) {
			return $this->returnData('Parameter Id Tidak Ditemukan', true);
		}
		$response = $this->SparepartModel->destroy($id);
		return $this->returnData($response['msg'], $response['error']);
	}

	public function returnData($msg, $error)
	{
		$response['error'] = $error;
		$response['message'] = $msg;
		return $this->response($response);
	}
}

class SparepartData
{
	public $name;
	public $merk;
    public $amount;
    public $created_at;
}
