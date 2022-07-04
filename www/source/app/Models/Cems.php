<?php
namespace App\Models;

use CodeIgniter\Model;

class Cems extends Model{
    
	protected $DBGroup   = 'default';
    protected $table = 'tblm_cems';
    protected $primaryKey = 'cemsId';

    protected $returnType = 'array';

    protected $allowedFields = ['cemsId', 'name', 'latitude', 'longitude', 'dimension', 'fuel', 'timestamp', 'status', 'cemsCode', 'group'];
    protected $useTimestamps = false;

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}