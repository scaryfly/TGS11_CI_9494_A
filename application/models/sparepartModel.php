<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class SparepartModel extends CI_Model
{
    private $table = 'spareparts';
    public $id;
    public $name;
    public $merk;
    public $amount;
    public $created_at;
    public $rule = [
    [
    'field' => 'name',
    'label' => 'name',
    'rules' => 'required'
    ],
    ];
    public function Rules() { return $this->rule; }

    public function getAll() {
        if (!$this->apitoken_exists()) 
			return ['msg'=>'unauthenticated','error'=>true]; 
        return ['msg'=>$this->db->get('spareparts')->result(),'error'=>false];
    }
    public function store($request) {
        if (!$this->apitoken_exists()) 
			return ['msg'=>'unauthenticated','error'=>true];
    $this->name = $request->name;
    $this->merk = $request->merk;
    $this->amount = $request->amount;
    $this->created_at = $request->created_at;
    if($this->db->insert($this->table, $this)){
    return ['msg'=>'Berhasil','error'=>false];
    }
    return ['msg'=>'Gagal','error'=>true];
    }
    public function update($request,$id) {
        if (!$this->apitoken_exists()) 
			return ['msg'=>'unauthenticated','error'=>true];
    $updateData = ['merk' => $request->merk, 'name' =>$request->name,'amount' => $request->amount, 'created_at' =>$request->created_at ];
    if($this->db->where('id',$id)->update($this->table, $updateData)){
    return ['msg'=>'Berhasil','error'=>false];
    }
    return ['msg'=>'Gagal','error'=>true];
    }
    public function destroy($id){
        if (!$this->apitoken_exists()) 
			return ['msg'=>'unauthenticated','error'=>true];
    if (empty($this->db->select('*')->where(array('id' => $id))->get($this->table)->row())) return ['msg'=>'Id tidak ditemukan','error'=>true];

    if($this->db->delete($this->table, array('id' => $id))){
    return ['msg'=>'Berhasil','error'=>false];
    }
    return ['msg'=>'Gagal','error'=>true];
    }
    public function apitoken_exists()
	{
		// cek header authorization
		$token_full = $this->input->get_request_header('Authorization');
		$splited = explode(' ', $token_full);
		// cek array hasil split
		if (count($splited) < 2) return 0;
		$type = $splited[0];
		$token = $splited[1];
		// cek type credential
		if ($type != 'Bearer') return 0;
		//cari api token yang sama
		$this->db->where('api_token', $token);
		$query = $this->db->get('users', 1);
		$row = $query->row();
		if (empty($row)) 
			return 0;
		return $row->id;
	}
    
}
