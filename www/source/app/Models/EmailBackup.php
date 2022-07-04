<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailBackup extends Model
{
	protected $DBGroup              = 'backup';
	protected $table                = 'tblm_email';
	protected $primaryKey           = 'id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDelete        = true;
	protected $protectFields        = true;
	protected $allowedFields        = ['email', 'deleted_at', 'cemsId'];

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'created_at';
	protected $updatedField         = 'updated_at';
	protected $deletedField         = 'deleted_at';

	// Validation
	protected $validationRules      = [
		'email'		=>	[
			'label'	=>	'E-Mail',
			'rules'	=>	'required|valid_email'
		]
	];
	protected $validationMessages   = [
		'email'				=>	[
			'valid_email'	=>	'E-Mail not valid'
		]
	];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = [];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = [];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
}
