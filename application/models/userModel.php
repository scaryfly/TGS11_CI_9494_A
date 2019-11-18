<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class UserModel extends CI_Model
{
    private $table = 'users';
    public $id;
    public $name;
    public $email;
    public $password;
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
		return ['msg'=> $this->db->get('users')->result(),'error'=>false];
    }
    public function store($request) {
		if (!$this->apitoken_exists()) 
			return ['msg'=>'unauthenticated','error'=>true];
        $this->name = $request->name;
        $this->email = $request->email;
        $this->password = password_hash($request->password, PASSWORD_BCRYPT);
        if($this->db->insert($this->table, $this)){
        return ['msg'=>'Berhasil','error'=>false];
        }
        return ['msg'=>'Gagal','error'=>true];
    }
    public function update($request,$id) {
		if (!$this->apitoken_exists()) 
			return ['msg'=>'unauthenticated','error'=>true];
    $updateData = ['email' => $request->email, 'name' =>$request->name];
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
    public function response_login()
	{
		// cek hasil validasi
		$this->form_validation->set_rules($this->rulesLogin());
		$validator = $this->form_validation->run();
		if (!$validator) {
			return ['msg'=>'Gagal','error'=>true];
		}
		// ambil input
		$post = $this->input->post();
		$this->password = $post['password'];
		$this->email = $post['email'];
		// buat apitoken
		$uniqueSTR = rand();
		$this->api_token = sha1($uniqueSTR);
		//cari email 
		$this->db->where('email', $this->email);
		$query = $this->db->get($this->table, 1);
		$row = $query->row();
		if (empty($row)) 
			return ['msg'=>'Gagal','error'=>true];
		// update token
		$this->db->where('email', $this->email);
		$this->db->set('api_token', $this->api_token);
		$query = $this->db->update($this->table);
		// simpan token dan ubah ke array
		$model = ['id' => $row->id ];
		$model['api_token'] = $this->api_token;
        if (password_verify($this->password, $row->password)) 
			return ['msg'=> $model,'error'=>false] ;
		return ['msg'=>'Gagal','error'=>true];
    }
    public function response_logout(){
		$user_id = $this->apitoken_exists();
		if (!$user_id) 
			return ['msg'=>'tidak terautentikasi','error'=>true];
		$this->db->where('id',$user_id);
		$model = $this->db->get($this->table, 1);
		if (empty($model)) 
			return['msg'=>'id tidak ditemukan','error'=>true];
		$this->db->where('id',$user_id);
		$this->db->set('api_token', '');
		$query = $this->db->update($this->table);
		return ['msg'=>'success logout','error'=>false];
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
	private function rulesLogin()
	{
		// rule validasi
		$rules = [
			[
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required',
			],
			[
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'required|valid_email',
			],
		];
		return $rules;
	}

}
