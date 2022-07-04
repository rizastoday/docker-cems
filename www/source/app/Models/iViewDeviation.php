<?php
namespace App\Models;

use CodeIgniter\Model;

class iViewDeviation extends Model{
    
    protected $table = 'vw_alarm';
    protected $primaryKey = 'alarmId';

    protected $returnType = 'array';

    protected $allowedFields = ['alarmId', 'cemsParameterId', 'desc', 'high', 'highHigh', 'value', 'status', 'isSent', 'sentTimestamp', 'timestamp'];
    protected $useTimestamps = false;

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}