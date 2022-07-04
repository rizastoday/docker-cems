<?php

namespace App\Controllers;

use App\Models\Log;
use CodeIgniter\Exceptions\PageNotFoundException;
use Exception;
use CodeIgniter\I18n\Time;
use Httpful\Http;
use Httpful\Mime;
use Httpful\Request;

class Cems extends BaseController
{
	public function __construct()
	{
		$this->auth_param = json_decode(session()->get('parameter'));
	}
    public function index()
    {
		if(!in_array("WEB.VIEW.SETTING.CEMS", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        $db = \Config\Database::connect();
        $data['title'] = 'Cems Data';
        return view('pages/cems/index', $data);
    }
    public function details($cemsId)
    {
		if(!in_array("WEB.DETAIL.CEMS", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        $session = session();
        if(!$session->get('logged_in')){
            return redirect()->to('auth'); 
        }
        $db = \Config\Database::connect();
        $cemsId = base64_decode($cemsId);
        $query = $db->table('tblm_cems')->where('cemsId', $cemsId)->orderBy("timestamp", "desc")->get();
        $scheduleShutdown = $db->table('tblt_schedule_unit')->where(['cemsId' => $cemsId, 'executed' => 0, 'hour >=' => date("Y-m-d H:i:00", strtotime(new Time('now')))])->orderBy('created_at', 'desc')->get(1)->getRow();
        $data['title'] = 'Cems Detail & Setting';
        $data["cems"] = $query->getRow();
        $data["schedule"] = $scheduleShutdown;
        $data['cemsList'] = $db->table('tblm_cems')->get()->getResult();
        $data['cemsParameterList'] = $db->table('vw_CemsParameter')->get()->getResult();

        if (!isset($data['cems']->cemsId)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data['cemsParameter'] = $db->table('vw_CemsParameter')->where(array('cemsId' => $cemsId))->get()->getResult();
        return view('pages/cems/details', $data);
        // echo json_encode($scheduleShutdown);
    }

    public function saveCompanyProfile($companyProfileId = 0)
    {
        if ($_POST) {
            $db = \Config\Database::connect();
            $builder = $db->table("tblm_companyProfile");

            // $db_backup = \Config\Database::connect('backup', false);
            // $builder_backup = $db_backup->table("tblm_companyProfile");

            $name = $_POST["name"];
            $noTelp = $_POST["noTelp"];
            $address = $_POST["address"];

            $data = array(
                "name" => $name,
                "noTelp" => $noTelp,
                "address" => $address
            );

            if ($companyProfileId > 0) {
                $builder->where("companyProfileId", $companyProfileId)->update($data);
                // $builder_backup->where("companyProfileId", $companyProfileId)->update($data);

                echo json_encode(array(
                    "status"    => 200,
                    "Message"   => "Success Update",
                    "data"  => $data
                ));
            } else {
                $getData = $builder->orderBy("timestamp", "desc")->get()->getRow();
                if (isset($getData->companyProfileId)) {
                    $builder->where("companyProfileId", $getData[0]->companyProfileId)->update($data);
                    // $builder_backup->where("companyProfileId", $getData[0]->companyProfileId)->update($data);

                    echo json_encode(array(
                        "status"    => 200,
                        "Message"   => "Success Update",
                        "data"  => $data
                    ));
                } else {
                    $builder->insert($data);
                    // $builder_backup->insert($data);

                    echo json_encode(array(
                        "status"    => 200,
                        "Message"   => "Success Add2",
                    ));
                }
            }
        } else {
            echo json_encode(array(
                "status"    => 400,
                "Message"   => "Wrong Method"
            ));
        }
    }

    public function executeScheduleShutdown($id)
    {
        $log = new Log();
        try {
            $db = \Config\Database::connect();
            $db->query("CALL sp_scheduleShutdown(". $id .")")->getResult();
            Request::post(env('NODE_URL').'/triggersql')
            ->method(Http::POST)
            ->send();

            
            $log->save([
                'user'      =>  session()->get('user_name'),
                'address'   =>  $this->IP,
                'activity'  =>  'Execute Shutdown Scheduling'
            ]);
            return json_encode([
                'status'    =>  true,
                'url'       =>  env('NODE_URL').'/triggersql'
            ]);
        }catch(Exception $e){
            $log->save([
                'user'      =>  session()->get('user_name'),
                'address'   =>  $this->IP,
                'activity'  =>  'Failed Executing Shutdown Schedule',
                'data'      =>  $e->getMessage()
            ]);
            return json_encode([
                'status'    =>  false,
                'message'   =>  $e->getMessage()
            ]);
        }
    }


    public function deleteCems()
    {
        try {
            $request = \Config\Services::request();
            $db = \Config\Database::connect();
            if($request->getPost('cemsId')){
                $check = $db->table('tblm_cems')->where(['cemsId' => $request->getPost('cemsId')])->get()->getRow();
                if(!$check){
                    throw new Exception('Cems ID not found');
                }
                $checkParameter = $db->table('tblm_cemsParameter')->where(['cemsId' => $check->cemsId])->get()->getResult();
                if(count($checkParameter) > 0){
                    foreach($checkParameter as $params){
                        $db->table('tblm_cemsParameter')->delete(['cemsParameterId' => $params->cemsParameterId]);
                    }
                }

                $db->table('tblh_log')->insert([
                    'user'      =>  session()->get('user_name'),
                    'address'   =>  $this->IP,
                    'activity'  =>  'Delete CEMS data',
                    'data'      =>  json_encode($check)
                ]);

                $db->table('tblm_cems')->delete(['cemsId' => $request->getPost('cemsId')]);

                return json_encode([
                    'status' => true
                ]);

            }else {
                throw new Exception('Cems ID is required');
            }
        }catch(Exception $e){
            return json_encode([
                'status' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function saveOnlyCems()
    {
        try {
            $request = \Config\Services::request();
            $cems = new \App\Models\Cems();
            $log = new Log();
            $db = \Config\Database::connect();
            $schedule = $db->table("tblt_schedule_unit");
            if($request->getPost('cemsId')){
                $checkCems = $cems->where('cemsId', $request->getPost('cemsId'))->get()->getRow();
                if(!$checkCems){
                    throw new Exception("Cems data not found");
                }

                
                if($request->getPost("show_dashboard") == 1){
                    $checkParameter = $db->table("tblm_cemsParameter")->where("cemsId", $request->getPost('cemsId'))->get()->getResult();
                    if(!$checkParameter){
                        throw new Exception("Cannot set CEMS to show in dashboard, please set parameter first");
                    }
                }

                if($request->getPost('unit_scheduling')){
                    $data["hour"] = $request->getPost('unit_scheduling');
                    $data["executed"] = 0;
                    $data["cemsId"] = $request->getPost('cemsId');
                    
                    $scheduleShutdown = $schedule->where(['cemsId' => $request->getPost('cemsId'), 'executed' => 0])->orderBy('created_at', 'desc')->get(1)->getRow();
                    if(!$scheduleShutdown){
                        $schedule->insert($data);

                        
                        $log->insert([
                            'user'      =>  session()->get('user_name'),
                            'address'   =>  $this->IP,
                            'activity'  =>  'Set CEMS Shutdown Schedule',
                            'data'      =>  json_encode($schedule->where("scheduleId", $db->insertID())->get()->getResultArray())
                        ]);
                    }
                }

                
                if($request->getPost('status') != "Shutdown"){
                    $scheduleShutdown = $schedule->where(['cemsId' => $request->getPost('cemsId'), 'executed' => 1, 'timestamp_end' => null])->orderBy('created_at', 'desc')->get(1)->getRow();
                    try{
                        if($scheduleShutdown){
                            $update['timestamp_end'] = date("Y-m-d H:i:s", strtotime(new Time('now'))); 
                            $schedule->where('scheduleId', $scheduleShutdown->scheduleId)->update($update);
                        }
                    }catch(Exception $e){
                        throw new Exception($e->getMessage());
                    }
                }

                if($request->getPost('status') != 'Running') {
                    $parameterId = $db->table('tblm_parameter')->where(['name' => 'maintenance'])->get()->getRow()->parameterId;
                    $cemsParameter = $db->table('tblm_cemsParameter')->where(['cemsId' => $request->getPost('cemsId')])->get()->getResult();
                    foreach($cemsParameter as $param){
                        $db->table('tblt_parameter')->update([
                            'values'            =>  1
                        ],[
                            'cemsParameterId'   =>  $param->cemsParameterId,
                            'm_parameterId'     =>  $parameterId
                        ]);
                    }
                }else {
                    $parameterId = $db->table('tblm_parameter')->where(['name' => 'maintenance'])->get()->getRow()->parameterId;
                    $cemsParameter = $db->table('tblm_cemsParameter')->where(['cemsId' => $request->getPost('cemsId')])->get()->getResult();
                    foreach($cemsParameter as $param){
                        $db->table('tblt_parameter')->update([
                            'values'            =>  0
                        ],[
                            'cemsParameterId'   =>  $param->cemsParameterId,
                            'm_parameterId'     =>  $parameterId
                        ]);
                    }
                }

                $cems->update($request->getPost('cemsId'), [
                    "name" => $request->getPost("name") ?? null,
                    "latitude" => $request->getPost("latitude") ?? null,
                    "longitude" => $request->getPost("longitude") ?? null,
                    "dimension" => $request->getPost("dimension") ?? null,
                    "fuel" => $request->getPost("fuel") ?? null,
                    "status" => $request->getPost("status") ?? 'Running',
                    "show_dashboard" => intval($request->getPost("show_dashboard") ?? 0),
                    "group" => $request->getPost("group") && $request->getPost("group") != "null" ? $request->getPost("group") : null,
                    "timestamp" => Time::now()
                ]);

                $log->insert([
                    'user'      =>  session()->get('user_name'),
                    'address'   =>  $this->IP,
                    'activity'  =>  'Update CEMS Data',
                    'data'      =>  json_encode($cems->where("cemsId", $request->getPost('cemsId'))->get()->getResultArray())
                ]);
                return json_encode(array(
                    "status"    =>  true,
                ));


            }else {
                $cems->save([
                    "name" => $request->getPost("name") ?? null,
                    "latitude" => $request->getPost("latitude") ?? null,
                    "longitude" => $request->getPost("longitude") ?? null,
                    "dimension" => $request->getPost("dimension") ?? null,
                    "fuel" => $request->getPost("fuel") ?? null,
                    "status" => $request->getPost("status") ?? 'Running',
                    "show_dashboard" => 0,
                    "group" => $request->getPost("group") && $request->getPost("group") != "null" ? $request->getPost("group") : null,
                ]);

                
                $log->insert([
                    'user'      =>  session()->get('user_name'),
                    'address'   =>  $this->IP,
                    'activity'  =>  'Add CEMS Data',
                    'data'      =>  json_encode($cems->where("cemsId", $request->getPost('cemsId'))->get()->getResultArray())
                ]);
                return json_encode(array(
                    "status"    =>  true,
                ));
            }
        }catch(Exception $e){
            return json_encode([
                'status' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function saveOnlyParameter()
    {
        $request = \Config\Services::request();
        if ($_POST) {
            $log = new Log();
            $db = \Config\Database::connect();
            $cemsId = $request->getPost('cemsId');
            $update_cems = false;
            $result = null;
            $ch = null;

            $success_add = 0;
            $failed_add = 0;
            $success_update = 0;
            $failed_update = 0;
            $success_delete = 0;
            $failed_delete = 0;
            $error = array();

            $tblm_cemsParameter = $request->getPost('tblm_cemsParameter');
            if ($tblm_cemsParameter) {

                $parameter = $db->table('tblm_parameter');
                $uom_terukur = $parameter->where('name', 'uom_terukur')->get()->getResultArray();
                $uom_terkoreksi = $parameter->where('name', 'uom_terkoreksi')->get()->getResultArray();
                $formula = $parameter->where('name', 'formula')->get()->getResultArray();
                $source = $parameter->where('name', 'source')->get()->getResultArray();
                $desc = $parameter->where('name', 'desc')->get()->getResultArray();
                $send_klhk = $parameter->where('name', 'send_klhk')->get()->getResultArray();
                $maintenance = $parameter->where('name', 'maintenance')->get()->getResultArray();
                $timestamp_maintenance = $parameter->where('name', 'timestamp_maintenance')->get()->getResultArray();
                $high_terukur = $parameter->where('name', 'high_terukur')->get()->getResultArray();
                $highHigh_terukur = $parameter->where('name', 'highHigh_terukur')->get()->getResultArray();
                $high_terkoreksi = $parameter->where('name', 'high_terkoreksi')->get()->getResultArray();
                $highHigh_terkoreksi = $parameter->where('name', 'highHigh_terkoreksi')->get()->getResultArray();
                $maintenance_scheduling = $parameter->where('name', 'maintenance_scheduling')->get()->getResultArray();
                $maintenance_description = $parameter->where('name', 'maintenance_description')->get()->getResultArray();
                $klhk_code = $parameter->where('name', 'klhk_code')->get()->getResultArray();

                foreach ($tblm_cemsParameter as $row) {
                    if ($row["addUpdate"] == "update") {
                        $dataParameterUpdate = array(
                            "cemsId" => $cemsId,
                            "parameterName" => $row["parameterName"],
                            "codeVal" => $row["codeVal"]
                        );
                        try{
                            $builder = $db->table("tblm_cemsParameter");
                            if ($row["deleting"] == "delete") {
                                $check = $builder->getWhere(['cemsParameterId' => $row["cemsParameterId"]])->getRow();
                                $dataParameterUpdate["active"] = $check->active == "1" ? "0" : "1";
                                $success_delete++;
                            }
                            $builder->where("cemsParameterId", $row["cemsParameterId"])->update($dataParameterUpdate);

                            $t_parameter = $db->table('tblt_parameter');


                            $check_uom_terukur = $t_parameter->where([
                                'm_parameterId'     =>  $uom_terukur[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_uom_terkoreksi = $t_parameter->where([
                                'm_parameterId'     =>  $uom_terkoreksi[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_formule = $t_parameter->where([
                                'm_parameterId'     =>  $formula[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_source = $t_parameter->where([
                                'm_parameterId'     =>  $source[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_desc = $t_parameter->where([
                                'm_parameterId'     =>  $desc[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_send_klhk = $t_parameter->where([
                                'm_parameterId'     =>  $send_klhk[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_maintenance      = $t_parameter->where([
                                'm_parameterId'     =>  $maintenance[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_timestamp_maintenance      = $t_parameter->where([
                                'm_parameterId'     =>  $timestamp_maintenance[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_high_terukur = $t_parameter->where([
                                'm_parameterId'     =>  $high_terukur[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_highHigh_terukur = $t_parameter->where([
                                'm_parameterId'     =>  $highHigh_terukur[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_high_terkoreksi = $t_parameter->where([
                                'm_parameterId'     =>  $high_terkoreksi[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_highHigh_terkoreksi      = $t_parameter->where([
                                'm_parameterId'     =>  $highHigh_terkoreksi[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_maintenance_scheduling      = $t_parameter->where([
                                'm_parameterId'     =>  $maintenance_scheduling[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_maintenance_description      = $t_parameter->where([
                                'm_parameterId'     =>  $maintenance_description[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();
                            $check_klhk_code      = $t_parameter->where([
                                'm_parameterId'     =>  $klhk_code[0]['parameterId'],
                                'cemsParameterId'   =>  $row["cemsParameterId"]
                            ])->get()->getResultArray();

                            
                            //
                            if(isset($row['uom_terukur'])){
                                if($check_uom_terukur){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $uom_terukur[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["uom_terukur"] != '-' ? $row["uom_terukur"] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update Uom Terukur : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["uom_terukur"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $uom_terukur[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'            =>  $row['uom_terukur'] != '-' ? $row['uom_terukur'] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set Uom Terukur : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["uom_terukur"])
                                    ]);
                                }
                            }
                            if(isset($row['uom_terkoreksi'])){
                                if(count($check_uom_terkoreksi) > 0){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $uom_terkoreksi[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["uom_terkoreksi"] != '-' ? $row["uom_terkoreksi"] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update Uom Terkoreksi : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["uom_terkoreksi"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $uom_terkoreksi[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['uom_terkoreksi'] != '-' ? $row['uom_terkoreksi'] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set Uom Terkoreksi : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["uom_terkoreksi"])
                                    ]);
                                }
                            }
                            if(isset($row['formula'])){
                                if($check_formule){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $formula[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["formula"] != '-' ? $row["formula"] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update Formula : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["formula"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $formula[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['formula'] != '-' ? $row['formula'] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set Formula : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["formula"])
                                    ]);
                                }
                            }
                            if(isset($row['source'])){
                                if($check_source){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $source[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["source"] != '-' ? $row["source"] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update Sourche : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["source"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $source[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['source'] != '-' ? $row['source'] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set Sourche : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["source"])
                                    ]);
                                }
                            }
                            if(isset($row['desc'])){
                                if($check_desc){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $desc[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["desc"] != '-' ? $row["desc"] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update Description : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["desc"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $desc[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['desc'] != '-' ? $row['desc'] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set Description : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["desc"])
                                    ]);
                                }
                            }
                            if(isset($row['send_klhk'])){
                                if($check_send_klhk){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $send_klhk[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["send_klhk"] == "on" ? 1 : 0
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update Send KLHK Status : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["send_klhk"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $send_klhk[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['send_klhk'] == "on" ? 1 : 0
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set Send KLHK Status : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["send_klhk"])
                                    ]);
                                }
                            }
                            if(isset($row['klhk_code'])){
                                if($check_klhk_code){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $klhk_code[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["klhk_code"] != '-' ? $row["klhk_code"] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update Sourche : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["klhk_code"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $klhk_code[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['klhk_code'] != '-' ? $row['klhk_code'] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set Sourche : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["klhk_code"])
                                    ]);
                                }
                            }
                            if(isset($row['maintenance_scheduling'])){
                                if($check_maintenance_scheduling){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $maintenance_scheduling[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["maintenance_scheduling"] == "on" ? 1 : 0
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update Maintenance Schedule Status : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["maintenance_scheduling"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $maintenance_scheduling[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['maintenance_scheduling'] == "on" ? 1 : 0
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set Maintenance Schedule Status : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["maintenance_scheduling"])
                                    ]);
                                }
                            }
                            if(isset($row['maintenance'])){
                                if($check_maintenance){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $maintenance[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["maintenance"] == "on" ? 1 : 0
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update Maintenance Parameter : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["maintenance"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $maintenance[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['maintenance'] == "on" ? 1 : 0
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set Maintenance Parameter : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["maintenance"])
                                    ]);
                                }
                                if($check_timestamp_maintenance){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $timestamp_maintenance[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  new Time('now')
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $timestamp_maintenance[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  new Time('now')
                                    ]);
                                }

                                
                                if($row['maintenance'] == "off"){
                                    $schedule = new \App\Models\Schedule();
                                    $checkSchedule = array_map(function($arr){
                                        return intval($arr['scheduleId']);
                                    }, $schedule->where([
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'executed'          =>  1,
                                        'timestamp_end'     =>  null
                                    ])->select('scheduleId')->findAll());
                                    if(!empty($checkSchedule)){
                                        $schedule->save([
                                            'scheduleId'    =>  $checkSchedule,
                                            'timestamp_end' =>  date("Y-m-d H:i:s", strtotime(new Time('now')))
                                        ]);
                                    }

                                    
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $maintenance_description[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  null
                                    ]);
                                }
                                // if($row['maintenance'] == "off"){
                                //     $schedule = new \App\Models\ScheduleBackup();
                                //     $checkSchedule = array_map(function($arr){
                                //         return intval($arr['scheduleId']);
                                //     }, $schedule->where([
                                //         'cemsParameterId'   =>  $row["cemsParameterId"],
                                //         'executed'          =>  1,
                                //         'timestamp_end'     =>  null
                                //     ])->select('scheduleId')->findAll());
                                //     if(!empty($checkSchedule)){
                                //         $schedule->save([
                                //             'scheduleId'    =>  $checkSchedule,
                                //             'timestamp_end' =>  date("Y-m-d H:i:s", strtotime(new Time('now')))
                                //         ]);
                                //     }
                                // }
                            }
                            if(isset($row['high_terukur'])){
                                
                                    

                                if($check_high_terukur){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $high_terukur[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["high_terukur"] != '-' ? $row["high_terukur"] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update High Terukur : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["high_terukur"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $high_terukur[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['high_terukur'] != '-' ? $row['high_terukur'] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set High Terukur : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["high_terukur"])
                                    ]);
                                }
                            }
                            if(isset($row['high_terkoreksi'])){
                                if($check_high_terkoreksi){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $high_terkoreksi[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["high_terkoreksi"] != '-' ? $row["high_terkoreksi"] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update High Terkoreksi : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["high_terkoreksi"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $high_terkoreksi[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['high_terkoreksi'] != '-' ? $row['high_terkoreksi'] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set High Terkoreksi : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["high_terkoreksi"])
                                    ]);
                                }
                            }
                            
                            if(isset($row['highHigh_terukur'])){
                                if($check_highHigh_terukur){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $highHigh_terukur[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["highHigh_terukur"] != '-' ? $row["highHigh_terukur"] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update High High Terukur : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["highHigh_terukur"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $highHigh_terukur[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['highHigh_terukur'] != '-' ? $row['highHigh_terukur'] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set High High Terukur : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["highHigh_terukur"])
                                    ]);
                                }
                            }
                            if(isset($row['highHigh_terkoreksi'])){
                                if($check_highHigh_terkoreksi){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $highHigh_terkoreksi[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["highHigh_terkoreksi"] != '-' ? $row["highHigh_terkoreksi"] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update High High Terkoreksi : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["highHigh_terkoreksi"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $highHigh_terkoreksi[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['highHigh_terkoreksi'] != '-' ? $row['highHigh_terkoreksi'] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set High High Terkoreksi : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["highHigh_terkoreksi"])
                                    ]);
                                }
                            }
                            if(isset($row['maintenance_description'])){
                                if($check_maintenance_description){
                                    $t_parameter->where([
                                        'm_parameterId'     =>  $maintenance_description[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"]
                                    ])->update([
                                        'values'             =>  $row["maintenance_description"] != '-' ? $row["maintenance_description"] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Update Maintenance Description : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["maintenance_description"])
                                    ]);
                                }else {
                                    $t_parameter->insert([
                                        'm_parameterId'     =>  $maintenance_description[0]['parameterId'],
                                        'cemsParameterId'   =>  $row["cemsParameterId"],
                                        'values'             =>  $row['maintenance_description'] != '-' ? $row['maintenance_description'] : null
                                    ]);
                                    $log->insert([
                                        'user'      =>  session()->get('user_name'),
                                        'address'   =>  $this->IP,
                                        'activity'  =>  'Set Maintenance Description : Cems Parameter ID'.$row["cemsParameterId"],
                                        'data'      =>  json_encode($row["maintenance_description"])
                                    ]);
                                }
                            }

                            $success_update++;
                        }catch(Exception $e){
                            $failed_update++;
                            array_push($error, $e->getMessage());
                            break;
                        }
                    } 
                    if ($row["addUpdate"] == "add") {
                        $dataParameterAdd = array(
                            "cemsId" => $cemsId,
                            "parameterName" => $row["parameterName"],
                            "codeVal" => $row["codeVal"],
                            "active" => 1,
                        );
                        try{
                            $builder = $db->table("tblm_cemsParameter");
                            $builder->insert($dataParameterAdd);
                            $success_add++;
                            $log->insert([
                                'user'      =>  session()->get('user_name'),
                                'address'   =>  $this->IP,
                                'activity'  =>  'Add New Parameter : Cems Parameter'.$row["parameterName"],
                                'data'      =>  json_encode($dataParameterAdd)
                            ]);

                            //
                            $t_parameter = $db->table('tblt_parameter');
                            if(isset($row['uom_terukur'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $uom_terukur[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'            =>  $row['uom_terukur']
                                ]);
                            }
                            if(isset($row['uom_terkoreksi'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $uom_terkoreksi[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['uom_terkoreksi']
                                ]);
                            }
                            if(isset($row['formula'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $formula[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['formula']
                                ]);
                            }
                            if(isset($row['source'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $source[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['source']
                                ]);
                            }
                            if(isset($row['klhk_code'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $klhk_code[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['klhk_code']
                                ]);
                            }
                            if(isset($row['desc'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $desc[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['desc']
                                ]);
                            }
                            if(isset($row['send_klhk'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $send_klhk[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['send_klhk'] == "on" ? 1 : 0
                                ]);
                            }
                            if(isset($row['maintenance'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $maintenance[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['maintenance'] == "on" ? 1 : 0
                                ]);
                            }
                            if(isset($row['maintenance_scheduling'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $maintenance_scheduling[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['maintenance_scheduling'] == "on" ? 1 : 0
                                ]);
                            }
                            if(isset($row['high_terukur'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $high_terukur[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['high_terukur']
                                ]);
                            }
                            if(isset($row['high_terkoreksi'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $high_terkoreksi[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['high_terkoreksi']
                                ]);
                            }
                            if(isset($row['highHigh_terukur'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $highHigh_terukur[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['highHigh_terukur']
                                ]);
                            }
                            if(isset($row['highHigh_terkoreksi'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $highHigh_terkoreksi[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['highHigh_terkoreksi']
                                ]);
                            }
                            if(isset($row['maintenance_description'])){
                                $t_parameter->insert([
                                    'm_parameterId'     =>  $maintenance_description[0]['parameterId'],
                                    'cemsParameterId'   =>  $row["cemsParameterId"],
                                    'values'             =>  $row['maintenance_description']
                                ]);
                            }
                        }catch(Exception $e){
                            $failed_add++;
                            array_push($error, $e->getMessage());
                            // break;
                        }
                    }
                }
            }

            return json_encode(array(
                "status"    => 200
            ));
        } else {
            echo json_encode(array(
                "status"    => 400,
                "Message"   => "Wrong Method"
            ));
        }
    }

    public function copyCEMS()
    {
        try {
            $request = \Config\Services::request();
            if($request->getPost('copyCemsId') && $request->getPost('cemsId')){
                $parameters = $this->db->table('tblm_cemsParameter')->where('cemsId', $request->getPost('copyCemsId'))->get()->getResult();
                foreach($parameters as $param){
                    $this->db->table('tblm_cemsParameter')->insert([
                        'cemsId' => $request->getPost('cemsId'),
                        'parameterName' => $param->parameterName,
                        'source' => $param->source,
                        'constant' => $param->constant,
                        'desc' => $param->desc,
                        'high' => $param->high,
                        'highHigh' => $param->highHigh,
                        'cemsParameterCreatedBy' => $param->cemsParameterCreatedBy,
                        'cemsParameterCreatedDate' => $param->cemsParameterCreatedDate,
                        'cemsParameterUpdatedBy' => $param->cemsParameterUpdatedBy,
                        'cemsParameterUpdatedDate' => $param->cemsParameterUpdatedDate
                    ]);
                }

            }else {
                throw new \Exception("Parameter is not complete");
            }
        }catch(Exception $e){
            echo json_encode([
                'status'    =>  false,
                'message'   =>  $e->getMessage()
            ]);
        }
    }

    public function ajax_list()
    {
        $table = 'tblm_cems';
        $column_order = array('timestamp', 'name', 'latitude', 'longitude', 'dimension', 'fuel', 'status');
        $column_search = array('timestamp', 'name', 'latitude', 'longitude', 'dimension', 'fuel', 'status');
        $order = array('cemsId' => 'asc');
        $where = [];
        if($this->auth_param->DIS != 1){
			if(count(explode(',', $this->auth_param->UNIT)) > 1){
                $cemsCode = [];
                foreach(explode(',', $this->auth_param->UNIT) as $unit){
                    array_push($cemsCode, $unit);
                }
                $where = "cemsCode = ". implode(' OR cemsCode = ', $cemsCode);
			}else {
                $where = [
					'cemsCode'  =>	$this->auth_param->UNIT
                ];
			}
        }
		$request = \Config\Services::request();
        $datatable = new \App\Models\Datatable($table, $column_order, $column_search, $order);
        $list = $datatable->datatable($where);
		$data = array();
		$no = $request->getPost('start');
        foreach ($list as $lists) {
			$row    = array();
			$row[] = $lists->name;
			$row[] = $lists->latitude;
			$row[] = $lists->longitude;
			$row[] = $lists->dimension;
			$row[] = $lists->fuel;
			$row[] = $lists->status;
			$row[] = $lists->show_dashboard == 1 ? 'SHOW' : 'HIDE';
            if(in_array("WEB.DETAIL.CEMS", session()->get('role'))){
                $row[] = '<div class="d-flex justify-content-center align-items-center">  <button title="View Detail" onclick="v.Detail('.$lists->cemsId.')"  class="btn btn-sm btn-primary mx-2"><i class="fa fa-eye"></i> Detail </button>  </div>';
            }
            $data[] = $row;
			$no++;
        }
        $output = array(
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $datatable->count_all($where),
            "recordsFiltered" => $datatable->count_filtered($where),
            "data" => $data,
        );
        echo json_encode($output);
	}

    public function ajax_list_schedule_shutdown()
    {
        $table = 'tblt_schedule_unit';
        $column_order = array('created_at', 'hour', 'timestamp_executed', 'timestamp_end');
        $column_search = array('created_at', 'hour', 'timestamp_executed', 'timestamp_end');
        $order = array('created_at' => 'desc');
		$request = \Config\Services::request();
        $datatable = new \App\Models\Datatable($table, $column_order, $column_search, $order);
        $where = [
            'cemsId'    => $request->getPost('cemsId')
        ];
        $list = $datatable->datatable($where);
        $output = array(
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $datatable->count_all($where),
            "recordsFiltered" => $datatable->count_filtered($where),
            "data" => $list,
        );
        echo json_encode($output);
	}

    public function removeSchedule()
    {
        $request = \Config\Services::request();
        $model = new \App\Models\ScheduleShutdown();
        $log = new Log();
        if($_POST){
            if($request->getPost('id')){
                $check = $model->where('scheduleId', $request->getPost('id'))->where('executed', 0)->get()->getRow();
                if($check){
                    try {
                        $model->delete($request->getPost('id'));
                        $log->save([
                            'user'      =>  session()->get('user_name'),
                            'address'   =>  $this->IP,
                            'activity'  =>  'Delete Schedule Shutdown',
                            'data'      =>  json_encode([
                                'scheduleId'        =>  $request->getPost('id')
                            ])
                        ]);
                        echo json_encode([
                            'status'    =>  true
                        ]);
                    }catch(Exception $e){
                        echo json_encode([
                            'status'    =>  false,
                            'message'   =>  $e->getMessage()
                        ]);
                    }
                }
            }else {
                echo json_encode([
                    'status'    =>  false,
                    'message'   =>  'Id Empty'
                ]);
            }
        }else {
            echo json_encode([
                'status'    =>  false,
                'message'   =>  'Wrong method'
            ]);
        }
    }

}
