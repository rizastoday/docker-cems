<?php
namespace App\Models;

use CodeIgniter\Model;

class CemsThird extends Model{
    
	protected $DBGroup   = 'third';
    protected $table = 'tblm_cems';
    protected $primaryKey = 'cemsId';

    protected $returnType = 'array';

    protected $allowedFields = ['cemsId', 'name', 'latitude', 'longitude', 'dimension', 'fuel', 'timestamp', 'status'];
    protected $useTimestamps = false;

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}