<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class History extends BaseController
{
	protected $table = 'tblh_log';
    protected $column_order = array('timestamp', 'address', 'activity', 'data', 'data2', 'user');
    protected $column_search = array('timestamp', 'address', 'activity', 'data', 'data2', 'user');
    protected $order = array('timestamp' => 'desc');
    public function __construct()
	{
		$this->datatable = new \App\Models\Datatable($this->table, $this->column_order, $this->column_search, $this->order);
	}
	public function index()
	{
		if(!in_array("WEB.VIEW.SETTING.HISTORY", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        $data['title'] = 'Activity History';
        return view('pages/history/index', $data);
	}

	public function ajax_list()
    {
		$request = \Config\Services::request();
        $datatable = $this->datatable;
        $where = [];
        $list = $datatable->datatable($where);
        $output = array(
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $datatable->count_all($where),
            "recordsFiltered" => $datatable->count_filtered($where),
            "data" => $list,
        );
        echo json_encode($output);
	}
}
