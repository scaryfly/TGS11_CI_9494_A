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

 public function getAll() { return
 $this->db->get('data_sparepart')->result();
 }
 public function store($request) {
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
$updateData = ['merk' => $request->merk, 'name' =>$request->name,'amount' => $request->amount, 'created_at' =>$request->created_at ];
if($this->db->where('id',$id)->update($this->table, $updateData)){
return ['msg'=>'Berhasil','error'=>false];
}
return ['msg'=>'Gagal','error'=>true];
}
public function destroy($id){
if (empty($this->db->select('*')->where(array('id' => $id))->get($this->table)->row())) return ['msg'=>'Id tidak ditemukan','error'=>true];

if($this->db->delete($this->table, array('id' => $id))){
return ['msg'=>'Berhasil','error'=>false];
}
return ['msg'=>'Gagal','error'=>true];
}
}
