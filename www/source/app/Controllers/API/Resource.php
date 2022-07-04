<?php

namespace App\Controllers\API;

use App\Models\Cems;
use App\Models\VwCemsParameter;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Resource extends ResourceController
{
    use ResponseTrait;
    protected $format = 'json';

    public function GETCEMS()
    {
        $id = $this->request->getVar('id');
        $model = new Cems();
        if($id){
            $data = $model->getWhere(['cemsId'  =>  $id])->getResult();
            if($data){
                return $this->respond($data, 200);
            }else {
                return $this->failNotFound('not found');
            }
        }else {
            $data = $model->findAll();
            if($data){
                return $this->respond($data, 200);
            }else {
                return $this->failNotFound('data empty');
            }
        }
    }
    public function GETPARAMETER()
    {
        $id = $this->request->getVar('id');
        $cemsId = $this->request->getVar('cemsId');
        $model = new VwCemsParameter();
        if($id || $cemsId){
            if($id && $cemsId){
                $data = $model->getWhere(['cemsId'  =>  $cemsId], ['cemsParameterId' => $id])->getResult();
                if($data){
                    return $this->respond($data, 200);
                }else {
                    return $this->failNotFound('not found');
                }
            }else if($id && !$cemsId){
                $data = $model->getWhere(['cemsParameterId' => $id])->getResult();
                if($data){
                    return $this->respond($data, 200);
                }else {
                    return $this->failNotFound('not found');
                }
            }else if(!$id && $cemsId){
                $data = $model->getWhere(['cemsId' => $cemsId])->getResult();
                if($data){
                    return $this->respond($data, 200);
                }else {
                    return $this->failNotFound('not found');
                }
            }
        }else {
            $data = $model->findAll();
            if($data){
                return $this->respond($data, 200);
            }else {
                return $this->failNotFound('data empty');
            }
        }
    }

}