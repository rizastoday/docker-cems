<?php
namespace App\Models;

use CodeIgniter\Model;

class Users extends Model{
    
    protected $table = 'tblm_users';
    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = ['id', 'name', 'username', 'email', 'password', 'created_at'];
    protected $useTimestamps = false;

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}