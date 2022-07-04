<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyProfileModel extends Model
{
    protected $table = 'tblm_companyProfile';
    protected $primaryKey = 'companyProfileId';
    protected $returnType = 'array';
    protected $allowedFields = ['name', 'noTelp', 'address', 'timestamp'];
    protected $useSoftDeletes = true;
    protected $dateFormat = 'datetime';
    protected $useTimestamps = true;

    protected $skipValidation     = false;
    protected $validationRules    = [
        'name'          => 'required|min_length[100]',
    ];

    // protected $validationMessages = [
    //     'email'        => [
    //         'is_unique' => 'Sorry. That email has already been taken. Please choose another.'
    //     ]
    // ];


    /**
     * Protected & internals methods
     */

    protected function updatData(array $data)
    {
        if (! isset($data[$this->primaryKey]) )
        { 
            return $data; 
        }

        $this->builder()
            ->whereIn($this->primaryKey, $data[$this->primaryKey])
            ->set($data)
            ->update();

        return $data;
    }
}