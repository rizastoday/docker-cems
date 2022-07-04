<?php
namespace App\Models;

use CodeIgniter\Model;

class CemsParameter extends Model{
    
    protected $table = 'tblm_cemsParameter';
    protected $primaryKey = 'cemsParameterId';

    protected $returnType = 'array';

    protected $allowedFields = ['cemsParameterId','cemsId','parameterName','source','constant','desc','high','highHigh','codeVal','timestamp','uom','active'];
    protected $useTimestamps = false;

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}