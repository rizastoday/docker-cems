<?php namespace App\Controllers;

use CodeIgniter\I18n\Time;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Log;
use CodeIgniter\Exceptions\PageNotFoundException;
use DateTime;
use Moment\Moment;

class Dashboard extends BaseController
{
	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->cemsModel = new \App\Models\Cems();
		$this->parameterModel = new \App\Models\VwCemsParameter();
		$this->auth_param = json_decode(session()->get('parameter'));

		$this->parameter = $this->parameterModel->asArray()->where([
			'active'	=>	1
		])->findAll();
		if(env('FILTER') == 'DIS'){
			$this->parameter = $this->parameterModel->asArray()->where([
				'active'	=>	1,
				'send_klhk'	=>	1
			])->findAll();
		}
		if($this->auth_param->DIS == 0){
			if(count(explode(',', $this->auth_param->UNIT)) > 1){
				$this->cems = $this->cemsModel->where([
					'show_dashboard'	=>	1,
				])->whereIn('cemsCode', array_map(function($v){ return intval($v); }, explode(',', $this->auth_param->UNIT)))->findAll();
			}else {
				$this->cems = $this->cemsModel->where([
					'show_dashboard'	=>	1,
					'cemsCode'			=>	$this->auth_param->UNIT
				])->findAll();
			}
		}else {
			$this->cems = $this->cemsModel->where([
				'show_dashboard'	=>	1,
			])->findAll();
		}
		$this->alarm = $this->db->table('vw_alarm');
	}
	public function index()
	{
		if(!in_array("WEB.VIEW.DASHBOARD", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
		$data['title'] = 'Dashboard';
		$data['parameter'] = $this->parameter;
		$data['cems'] = $this->cems;
		return view('pages/dashboard/index', $data);
		// return json_encode(session()->get());
	}
	public function showcase()
	{
		if(!in_array("WEB.VIEW.SHOWCASE", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
		$data['title'] = 'Dashboard Showcase';
		$data['parameter'] = $this->parameter;
		$data['cems'] = $this->cems;
		return view('pages/dashboard/showcase', $data);
	}
	public function trending($time = '')
	{
		if(!in_array("WEB.VIEW.TRENDING", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
		$data['title'] = 'Trending Page';
		$data['parameter'] = $this->parameter;
		$data['cems'] = $this->cems;
		$data['time'] = $time;
		return view('pages/dashboard/trending', $data);
	}
	public function reporting()
	{
		if(!in_array("WEB.VIEW.REPORTING", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
		$data['title'] = 'Reporting Page';
		$data['parameter'] = $this->parameter;
		$data['cems'] = $this->cems;
		return view('pages/dashboard/reporting', $data);
	}
	public function deviation()
	{
		if(!in_array("WEB.VIEW.ALARM", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
		$data['title'] = 'Alarm Page';
		$data['parameter'] = $this->parameter;
		$data['cems'] = $this->cems;
		return view('pages/dashboard/deviation', $data);
	}
	public function sync()
	{
		if(!in_array("WEB.VIEW.SYNCLOG", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
		$data['title'] = 'Synchronize Log History';
		$data['parameter'] = $this->parameter;
		$data['cems'] = $this->cems;
		return view('pages/dashboard/sync', $data);
	}

	public function sync_error()
	{
		if(!in_array("WEB.VIEW.SYNCLOG", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
		$data['title'] = 'Error Log';
		$data['parameter'] = $this->parameter;
		$data['cems'] = $this->cems;
		return view('pages/dashboard/sync_error', $data);
	}

    public function ajax_list()
    {
		
		$table = 'vw_alarm';
		$column_order = array('timestamp', 'name', 'desc', 'high_terukur', 'highHigh_terukur','high_terkoreksi', 'highHigh_terkoreksi', 'value', 'status', 'uom_terukur', 'uom_terkoreksi');
		$column_search = array('timestamp', 'name', 'desc', 'high_terukur', 'highHigh_terukur','high_terkoreksi', 'highHigh_terkoreksi', 'value', 'status', 'uom_terukur', 'uom_terkoreksi');
		$order = array('timestamp' => 'desc');
		$request = \Config\Services::request();
        $deviation = new \App\Models\Datatable($table, $column_order, $column_search, $order);
        $where = array(
			'cemsId' => $request->getPost('cemsId'),
			'timestamp >=' => date('Y-m-d H:i:s', strtotime($request->getPost('from'))),
			'timestamp <=' => date('Y-m-d H:i:s', strtotime($request->getPost('to'))),
		);
        $list = $deviation->datatable($where);
        $output = array(
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $deviation->count_all($where),
            "recordsFiltered" => $deviation->count_filtered($where),
            "data" => $list
        );
        echo json_encode($output);
	}

	
    public function ajax_list_sync()
    {
		
		$table = 'tblm_newsynclog';
		$column_order = array('timestamp_id', 'cemsId', 'status');
		$column_search = array('timestamp_id', 'cemsId', 'status');
		$order = array('timestamp_id' => 'desc');
		$request = \Config\Services::request();
        $deviation = new \App\Models\Datatable($table, $column_order, $column_search, $order);
        $where = "timestamp_id between " . $request->getPost('from') . " and ". $request->getPost('to') . " and cemsId = " .$request->getPost('cemsId');
        $list = $deviation->datatable($where);
        $output = array(
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $deviation->count_all($where),
            "recordsFiltered" => $deviation->count_filtered($where),
            "data" => $list,
			"date"	=> intval($request->getPost('from'))
        );
        echo json_encode($output);
	}
	

    public function ajax_list_sync_error()
    {
		
		$table = 'tblt_catchsynclog';
		$column_order = array('timestamp_id', 'cemsId', 'status');
		$column_search = array('timestamp_id', 'cemsId', 'status');
		$order = array('timestamp_id' => 'desc');
		$request = \Config\Services::request();
        $deviation = new \App\Models\Datatable($table, $column_order, $column_search, $order);
        $where = "timestamp_id between " . $request->getPost('from') . " and ". $request->getPost('to') . " and cemsId = " .$request->getPost('cemsId');
        $list = $deviation->datatable($where);
        $output = array(
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $deviation->count_all($where),
            "recordsFiltered" => $deviation->count_filtered($where),
            "data" => $list,
			"date"	=> intval($request->getPost('from'))
        );
        echo json_encode($output);
	}

	public function export()
	{
		if(!in_array("WEB.REPORT", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
		$spreadSheet = new Spreadsheet();
		$model = new \App\Models\VwCemsParameter();
		$cems = new \App\Models\Cems();
		$request = \Config\Services::request();
		$cemsId = $request->getPost('cemsId');
		$cemsName = $cems->where('cemsId', $cemsId)->get()->getRow();
		$interval = $request->getPost('interval');
		$data = json_decode($request->getPost('data'));
		$keys = [];
		$test = null;
		foreach($data as $item => $values){
			array_push($keys, $values);
			if(count($keys) > 0){
				break;
			}
		}
		$keys = $keys[0];
		$keys = array_keys((array)$keys);

		$styleTitle = [
			'font' => [
				'bold' => true,
				'size' => 14
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
		];
		$styleHeader = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			]
		];
		$styleValue = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			]
		];
		$warning = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'color' => [
					'rgb' => 'FFFF00',
				]
			],
		];
		$danger = [
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'color' => [
					'rgb' => 'FF0000',
				]
			],
		];

		if($interval == 'hourly' || $interval == 'minute' || $interval == 'second' || $interval == 'average'){

			array_unshift($keys, "date");
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(1, 2)
						->applyFromArray($styleTitle);
			$spreadSheet->getActiveSheet()
						->mergeCellsByColumnAndRow(1, 2, count($keys), 2);
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow(1, 2, "CEMS ".ucfirst($interval)." Average Report - $cemsName->name");
			//header
			foreach( $keys as $index=>$values){
				$spreadSheet->getActiveSheet()
							->getColumnDimensionByColumn($index+1)
							->setAutoSize(true);
				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow($index+1, 5)
							->applyFromArray($styleHeader);
				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow($index+1, 6)
							->applyFromArray($styleHeader);
				if($index <= 1){
					$spreadSheet->getActiveSheet()
								->mergeCellsByColumnAndRow($index+1, 5, $index+1, 6);
					$spreadSheet->setActiveSheetIndex(0)
								->setCellValueByColumnAndRow($index+1, 5, strtoupper($values));
				}else{
					$param = explode("_", strtoupper($values))[0];
					$type = explode("_", strtolower($values))[1];
					$parameter = $model->where([
						'cemsId'			=>	$cemsId,
						'parameterName'		=>	$param
					])->get()->getResultArray();
					$uom = ($parameter[0]["uom_$type"] ? $parameter[0]["uom_$type"] : '');
					if($param == explode("_", $keys[$index-1])[0]){
						$spreadSheet->getActiveSheet()
								->mergeCellsByColumnAndRow($index, 5, $index+1, 5);
						$spreadSheet->setActiveSheetIndex(0)
									->setCellValueByColumnAndRow($index+1, 5, $param);
						$spreadSheet->setActiveSheetIndex(0)
									->setCellValueByColumnAndRow($index+1, 6, strtolower($type) == "terkoreksi" ? "(terkoreksi) $uom" : "$uom");
					}else {
						$spreadSheet->setActiveSheetIndex(0)
									->setCellValueByColumnAndRow($index+1, 5, $param);
						$spreadSheet->setActiveSheetIndex(0)
									->setCellValueByColumnAndRow($index+1, 6, $uom);
					}
				}
			}

			//value
			$row = 7;
			foreach($data as $item){
				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow(1, $row)
							->applyFromArray($styleValue);
				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow(2, $row)
							->applyFromArray($styleValue);
				$spreadSheet->setActiveSheetIndex(0)
							->setCellValueByColumnAndRow(1, $row, $interval == 'hourly' ? Time::parse($item->time, 'Asia/Jakarta')->toLocalizedString('MMMM dd, yyyy') : Time::parse($item->time, 'Asia/Jakarta')->toLocalizedString('MMMM dd, yyyy'));
				$spreadSheet->setActiveSheetIndex(0)
							->setCellValueByColumnAndRow(2, $row, $interval == 'hourly' ? Time::parse($item->time, 'Asia/Jakarta')->toLocalizedString('H:mm:ss') : Time::parse($item->time, 'Asia/Jakarta')->toLocalizedString('H:mm:ss'));
				foreach( $keys as $index=>$values){
					if($index <= 1){
						continue;
					}
					$spreadSheet->getActiveSheet()
								->getStyleByColumnAndRow($index+1, $row)
								->applyFromArray($styleValue);
					$spreadSheet->setActiveSheetIndex(0)
								->setCellValueByColumnAndRow($index+1, $row, $item->$values ?? "-");
				}
				$row+=1;
			}

			
			$spreadSheet->getActiveSheet()
						->mergeCellsByColumnAndRow(1, $row+1, 2, $row+1);
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(1, $row +1)
						->applyFromArray($styleValue);
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(2, $row +1)
						->applyFromArray($styleValue);
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow(1, $row+1, "Min.");
						// 
			$spreadSheet->getActiveSheet()
						->mergeCellsByColumnAndRow(1, $row+2, 2, $row+2);
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(1, $row +2)
						->applyFromArray($styleValue);
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(2, $row +2)
						->applyFromArray($styleValue);
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow(1, $row+2, "Max.");
						// 
			$spreadSheet->getActiveSheet()
						->mergeCellsByColumnAndRow(1, $row+3, 2, $row+3);
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(1, $row +3)
						->applyFromArray($styleValue);
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(2, $row +3)
						->applyFromArray($styleValue);
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow(1, $row+3, "Avg.");


			// value min max avg
			foreach( $keys as $index=>$values){
				if($index <= 1){
					continue;
				}


				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow($index+1, $row + 1)
							->applyFromArray($styleValue);
				$spreadSheet->setActiveSheetIndex(0)
							->setCellValueByColumnAndRow($index+1, $row + 1, min(array_map( function($ar) use ($values){
								return $ar->$values;
							}, $data)));
							// 
							
				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow($index+1, $row + 2)
							->applyFromArray($styleValue);
				$spreadSheet->setActiveSheetIndex(0)
							->setCellValueByColumnAndRow($index+1, $row + 2, max(array_map( function($ar) use ($values){
								return $ar->$values;
							}, $data)));
							// 
							
				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow($index+1, $row + 3)
							->applyFromArray($styleValue);
				$spreadSheet->setActiveSheetIndex(0)
							->setCellValueByColumnAndRow($index+1, $row + 3, array_sum(array_map( function($ar) use ($values){
								return $ar->$values;
							}, $data)) / count(array_map( function($ar) use ($values){
								return $ar->$values;
							}, $data)));
			}
		}
			
		if($interval == 'daily' || $interval == 'monthly'){
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(1, 2)
						->applyFromArray($styleTitle);
			$spreadSheet->getActiveSheet()
						->mergeCellsByColumnAndRow(1, 2, count($keys), 2);
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow(1, 2, "CEMS ".ucfirst($interval)." Average Report - $cemsName->name");

			//header
			foreach( $keys as $index=>$values){
				$spreadSheet->getActiveSheet()
							->getColumnDimensionByColumn($index+1)
							->setAutoSize(true);
				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow($index+1, 5)
							->applyFromArray($styleHeader);
				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow($index+1, 6)
							->applyFromArray($styleHeader);
				if($index <= 0){
					$spreadSheet->getActiveSheet()
								->mergeCellsByColumnAndRow($index+1, 5, $index+1, 6);
					$spreadSheet->setActiveSheetIndex(0)
								->setCellValueByColumnAndRow($index+1, 5, strtoupper($values));
				}else{
					$param = explode("_", strtoupper($values))[0];
					$type = explode("_", strtolower($values))[1];
					$parameter = $model->where([
						'cemsId'			=>	$cemsId,
						'parameterName'		=>	$param
					])->get()->getResultArray();
					$uom = ($parameter[0]["uom_$type"] ? $parameter[0]["uom_$type"] : '');

					if($param == explode("_", $keys[$index-1])[0]){
						$spreadSheet->getActiveSheet()
								->mergeCellsByColumnAndRow($index, 5, $index+1, 5);
						$spreadSheet->setActiveSheetIndex(0)
									->setCellValueByColumnAndRow($index+1, 5, $param);
						$spreadSheet->setActiveSheetIndex(0)
									->setCellValueByColumnAndRow($index+1, 6, strtolower($type) == "terkoreksi" ? "(terkoreksi) $uom" : "$uom");
					}else {
						$spreadSheet->setActiveSheetIndex(0)
									->setCellValueByColumnAndRow($index+1, 5, $param);
						$spreadSheet->setActiveSheetIndex(0)
									->setCellValueByColumnAndRow($index+1, 6, $uom);
					}
				}
			}

			//value
			$row = 7;
			foreach($data as $item){
				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow(1, $row)
							->applyFromArray($styleValue);
				$spreadSheet->setActiveSheetIndex(0)
							->setCellValueByColumnAndRow(1, $row, $interval == 'daily' ? Time::parse($item->time, 'Asia/Jakarta')->toLocalizedString('MMMM dd, yyyy') : Time::parse($item->time, 'Asia/Jakarta')->toLocalizedString('MMMM, yyyy'));
				foreach( $keys as $index=>$values){
					if($index <= 0){
						continue;
					}
					$spreadSheet->getActiveSheet()
								->getStyleByColumnAndRow($index+1, $row)
								->applyFromArray($styleValue);
					$spreadSheet->setActiveSheetIndex(0)
								->setCellValueByColumnAndRow($index+1, $row, $item->$values ?? "-");
				}
				$row+=1;
			}


			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(1, $row +1)
						->applyFromArray($styleValue);
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(2, $row +1)
						->applyFromArray($styleValue);
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow(1, $row+1, "Min.");
						// 
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(1, $row +2)
						->applyFromArray($styleValue);
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(2, $row +2)
						->applyFromArray($styleValue);
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow(1, $row+2, "Max.");
						// 
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(1, $row +3)
						->applyFromArray($styleValue);
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(2, $row +3)
						->applyFromArray($styleValue);
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow(1, $row+3, "Avg.");


			// value min max avg
			foreach( $keys as $index=>$values){
				if($index <= 0){
					continue;
				}


				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow($index+1, $row + 1)
							->applyFromArray($styleValue);
				$spreadSheet->setActiveSheetIndex(0)
							->setCellValueByColumnAndRow($index+1, $row + 1, min(array_map( function($ar) use ($values){
								return $ar->$values;
							}, $data)));
							// 
							
				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow($index+1, $row + 2)
							->applyFromArray($styleValue);
				$spreadSheet->setActiveSheetIndex(0)
							->setCellValueByColumnAndRow($index+1, $row + 2, max(array_map( function($ar) use ($values){
								return $ar->$values;
							}, $data)));
							// 
							
				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow($index+1, $row + 3)
							->applyFromArray($styleValue);
				$spreadSheet->setActiveSheetIndex(0)
							->setCellValueByColumnAndRow($index+1, $row + 3, array_sum(array_map( function($ar) use ($values){
								return $ar->$values;
							}, $data)) / count(array_map( function($ar) use ($values){
								return $ar->$values;
							}, $data)));
			}
		}

		$log = new Log();
		$log->save([
			'user'      =>  session()->get('user_name'),
			'address'   =>  $this->IP,
			'activity'  =>  'Export Reporting'
		]);

		$filename = 'Report Hourly CEMS';
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$filename.'.xlsx');
		header('Cache-Control: max-age=0');

		$xlsx = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadSheet, 'Xlsx');

		$xlsx->save('php://output');

		// echo json_encode($test);
		
	}

	public function export_sync()
	{
		$request = \Config\Services::request();
		$spreadSheet = new Spreadsheet();
		$db = \Config\Database::connect();
		$cems = $db->table('tblm_cems')->where(['cemsId' => $request->getPost('cemsId')])->get()->getRow();

        $where = "timestamp_id between " . $request->getPost('from') . " and ". $request->getPost('to') . " and cemsId = " .$request->getPost('cemsId');
		$data = $db->table('tblm_newsynclog')->where($where)->get()->getResult();
		$keys = ["Timestamp ID", "Status Code", "Status", "Last Update", "Description"];


		$styleTitle = [
			'font' => [
				'bold' => true,
				'size' => 16
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
		];
		$styleHeader = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			]
		];
		$styleValue = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			]
		];


		$spreadSheet->getActiveSheet()
					->getStyleByColumnAndRow(1, 2)
					->applyFromArray($styleTitle);
		$spreadSheet->getActiveSheet()
					->mergeCellsByColumnAndRow(1, 2, count($keys), 2);
		$spreadSheet->setActiveSheetIndex(0)
					->setCellValueByColumnAndRow(1, 2, strtoupper("REPORT SYNCHRONIZE CEMS UNIT ") . $cems->name);
		
	
		//header
		foreach( $keys as $index=>$values){
			$spreadSheet->getActiveSheet()
						->getColumnDimensionByColumn($index+1)
						->setAutoSize(true);
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow($index+1, 4)
						->applyFromArray($styleHeader);
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow($index+1, 4, strtoupper($values));
		}

		
		//value
		$index = 0;
		$row = 5;
		foreach($data as $item){
			$date = date($item->timestamp_id);

			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow($index+1, $row)
						->applyFromArray($styleValue);
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow($index+1, $row, $item->timestamp_id ? date('Y-m-d H:i:s.u', $item->timestamp_id / 1000)  : "-");
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow($index+2, $row, $item->statuscode ?? "-");
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow($index+3, $row, $item->status ?? "-");
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow($index+4, $row, $item->lastupdate ?? "-");
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow($index+5, $row, $item->desc ?? "-");
			$row+=1;
		}




		$filename = 'Report Hourly CEMS';
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$filename.'.xlsx');
		header('Cache-Control: max-age=0');

		$xlsx = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadSheet, 'Xlsx');

		$xlsx->save('php://output');
	}

	public function export_sync_error()
	{
		$request = \Config\Services::request();
		$spreadSheet = new Spreadsheet();
		$db = \Config\Database::connect();
		$cems = $db->table('tblm_cems')->where(['cemsId' => $request->getPost('cemsId')])->get()->getRow();

        $where = "timestamp_id between " . $request->getPost('from') . " and ". $request->getPost('to') . " and cemsId = " .$request->getPost('cemsId');
		$data = $db->table('tblt_catchsynclog')->where($where)->get()->getResult();
		$keys = ["Timestamp ID", "Error Event", "Status", "Description"];


		$styleTitle = [
			'font' => [
				'bold' => true,
				'size' => 16
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
		];
		$styleHeader = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			]
		];
		$styleValue = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			]
		];


		$spreadSheet->getActiveSheet()
					->getStyleByColumnAndRow(1, 2)
					->applyFromArray($styleTitle);
		$spreadSheet->getActiveSheet()
					->mergeCellsByColumnAndRow(1, 2, count($keys), 2);
		$spreadSheet->setActiveSheetIndex(0)
					->setCellValueByColumnAndRow(1, 2, strtoupper("REPORT ERROR CEMS UNIT ") . $cems->name);
		
	
		//header
		foreach( $keys as $index=>$values){
			$spreadSheet->getActiveSheet()
						->getColumnDimensionByColumn($index+1)
						->setAutoSize(true);
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow($index+1, 3)
						->applyFromArray($styleHeader);
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow($index+1, 3, strtoupper($values));
		}

		
		//value
		$index = 0;
		$row = 4;
		foreach($data as $item){
			$date = date($item->timestamp_id);

			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow($index+1, $row)
						->applyFromArray($styleValue);
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow($index+1, $row, $item->timestamp_id ? date('Y-m-d H:i:s.u', $item->timestamp_id / 1000)  : "-");
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow($index+2, $row, $item->event ?? "-");
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow($index+3, $row, $item->status ?? "-");
			$spreadSheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow($index+4, $row, $item->desc ?? "-");
			$row+=1;
		}




		$filename = 'Report Hourly CEMS';
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$filename.'.xlsx');
		header('Cache-Control: max-age=0');

		$xlsx = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadSheet, 'Xlsx');

		$xlsx->save('php://output');
	}

	public function test_export()
	{
		$spreadSheet = new Spreadsheet();
		$filename = 'Report Hourly';
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$filename.'.xlsx');
		// header('Cache-Control: max-age=0');

		$xlsx = new Xlsx($spreadSheet);
		$xlsx->save('php://output');
	}
	public function export_alarm()
	{
		$request = \Config\Services::request();
		$spreadSheet = new Spreadsheet();
		$cemsId = $request->getPost('cemsId');
		$from = $request->getPost('from');
		$to = $request->getPost('to');
        $where = array(
			'cemsId' => $cemsId,
			'timestamp >=' => $from,
			'timestamp <=' => $to,
		);
		$data = $this->alarm->select('timestamp, cemsId as unit, high_terukur, highHigh_terukur, uom_terukur, high_terkoreksi, highHigh_terkoreksi, uom_terkoreksi, name, value_ukur, value, status')->where($where)->get()->getResult();
		$keys = array_keys((array)$data[0]);
		$styleTitle = [
			'font' => [
				'bold' => true,
				'size' => 15
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
		];
		
		$styleHeader = [
			'font' => [
				'bold' => true,
			],
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
				],
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'color' => [
					'rgb' => '00FFFF',
				]
			],
		];

		
		$styleValue = [
			'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'borders' => [
				'outline' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			]
		];
		
		$spreadSheet->getActiveSheet()
					->getStyleByColumnAndRow(1, 2)
					->applyFromArray($styleTitle);
		$spreadSheet->getActiveSheet()
					->mergeCellsByColumnAndRow(1, 2, count($keys), 2);
		$spreadSheet->setActiveSheetIndex(0)
					->setCellValueByColumnAndRow(1, 2, 'REPORT ALARM FROM CONTINOUS EMISSION MONITORING SYSTEM UNIT ' . $cemsId);


		// header
		
		foreach( $keys as $index=>$values){
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow($index+1, 5)
						->applyFromArray($styleHeader);
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow($index+1, 6)
						->applyFromArray($styleHeader);
			if(($index <= 1) || $index > 3){
				$spreadSheet->getActiveSheet()
							->mergeCellsByColumnAndRow($index+1, 5, $index+1, 6);
				if($index <= 1){
					$spreadSheet->setActiveSheetIndex(0)
								->setCellValueByColumnAndRow($index+1, 5, strtoupper($values));

					$spreadSheet->getActiveSheet()
								->getColumnDimensionByColumn($index+1)
								->setAutoSize(true);
				}
			}else if($index == 2 || $index == 3){
				// 
				$spreadSheet->getActiveSheet()
				->mergeCellsByColumnAndRow($index+1, 5, $index+3, 5);
				$spreadSheet->getActiveSheet()
				->mergeCellsByColumnAndRow($index+3, 5, $index+5, 5);
				// 
				$spreadSheet->setActiveSheetIndex(0)
							->setCellValueByColumnAndRow($index+1, 5, 'TERUKUR');
				$spreadSheet->setActiveSheetIndex(0)
							->setCellValueByColumnAndRow($index+3, 5, 'TERKOREKSI');

				$spreadSheet->getActiveSheet()
							->getColumnDimensionByColumn($index+1)
							->setAutoSize(true);

				$spreadSheet->getActiveSheet()
							->getColumnDimensionByColumn($index+3)
							->setAutoSize(true);
			}
			
			$param = explode("_", strtoupper($values))[0];
			$spreadSheet->setActiveSheetIndex(0)
			->setCellValueByColumnAndRow($index+1, 6, strtoupper($param));


			$spreadSheet->getActiveSheet()
						->getColumnDimensionByColumn($index+1)
						->setAutoSize(true);
		}
		$spreadSheet->setActiveSheetIndex(0)
		->setCellValueByColumnAndRow(9, 5, strtoupper('NAME'));
		$spreadSheet->setActiveSheetIndex(0)
		->setCellValueByColumnAndRow(10, 5, strtoupper('VALUE TERUKUR'));
		$spreadSheet->setActiveSheetIndex(0)
		->setCellValueByColumnAndRow(11, 5, strtoupper('VALUE TERKOREKSI'));
		$spreadSheet->setActiveSheetIndex(0)
		->setCellValueByColumnAndRow(12, 5, strtoupper('STATUS'));

		
		
		$row = 7;
		foreach($data as $item){
			$spreadSheet->getActiveSheet()
						->getStyleByColumnAndRow(1, $row)
						->applyFromArray($styleValue);
			foreach( $keys as $index=>$values){
				$spreadSheet->getActiveSheet()
							->getColumnDimensionByColumn($index+1)
							->setAutoSize(true);
				$spreadSheet->getActiveSheet()
							->getStyleByColumnAndRow($index+1, $row)
							->applyFromArray($styleValue);
				$spreadSheet->setActiveSheetIndex(0)
							->setCellValueByColumnAndRow($index+1, $row, $item->$values ?? "-");
			}
			$row+=1;
		}

		$log = new Log();
		$log->save([
			'user'      =>  session()->get('user_name'),
			'address'   =>  $this->IP,
			'activity'  =>  'Export Reporting Alarm'
		]);

		$xlsx = new Xlsx($spreadSheet);
		$filename = 'Report Alarm CEMS';
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename='.$filename.'.xlsx');
		header('Cache-Control: max-age=0');

		$xlsx->save('php://output');

		// echo json_encode($keys);
	}
}
