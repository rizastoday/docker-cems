<?php
namespace App\Models;

use CodeIgniter\Model;

class Log extends Model{
    
    protected $table = 'tblh_log';
    protected $primaryKey = 'logId';

    protected $returnType = 'array';

    protected $allowedFields = ['logId', 'user', 'activity', 'data', 'data2', 'timestamp'];
    protected $useTimestamps = false;

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}