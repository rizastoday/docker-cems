<?php

namespace App\Controllers;

use App\Models\CompanyProfileModel;
use App\Models\Log;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\I18n\Time;
use Exception;
use Httpful\Http;
use Httpful\Mime;
use Httpful\Request;

class Setting extends BaseController
{
    public function __construct()
    {
		$this->parameter = null;
		$this->cems = null;
        if(session()->get('role')){
            $this->db = \Config\Database::connect();
            $this->cemsModel = new \App\Models\Cems();
            $this->parameterModel = new \App\Models\VwCemsParameter();
            $this->auth_param = json_decode(session()->get('parameter'));
    
            $this->parameter = $this->parameterModel->asArray()->where([
                'active'	=>	1
            ])->findAll();
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
        }
    }
    public function companyProfile()
    {
		if(!in_array("WEB.VIEW.SETTING.COMPANY", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        $db = \Config\Database::connect();
        $query = $db->table('tblm_companyProfile')->orderBy("timestamp", "desc")->get();
        $data["company_profile"] = $query->getRow();
        $data['type'] = 'company-profile';
        $data['title'] = ucwords(str_replace("-", " ", 'company-profile'));
        return view('pages/setting/companyProfile', $data);
    }
    public function sispek()
    {
		if(!in_array("WEB.VIEW.SETTING.SISPEK", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        $db = \Config\Database::connect();
        $query = $db->table('tblm_sispek')->orderBy("timestamp", "desc")->get();
        $cems = $db->table('tblm_cems')->get()->getResult();
        $param = $db->table('vw_CemsParameter')->get()->getResult();
        $data["sispek"] = $query->getRow();
        $data['type'] = 'sispek';
        $data['title'] = ucwords(str_replace("-", " ", 'sispek'));
        $data['cems'] = $cems;
        $data['cemsParameter'] = $param;
        return view('pages/setting/sispek', $data);
    }
    public function notification()
    {
		if(!in_array("WEB.VIEW.SETTING.NOTIFICATION", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        $db = \Config\Database::connect();
        $query = $db->table('tblm_notification')->orderBy("timestamp", "desc")->get();
        $data["notification"] = $query->getRow();
        $data['type'] = 'notification';
        $data['title'] = ucwords(str_replace("-", " ", 'notification'));
        return view('pages/setting/notification', $data);
    }
    public function parameter()
    {
		if(!in_array("WEB.VIEW.SETTING.PARAMETER", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        $parameter = new \App\Models\MParameter();
        // $data['parameter']  = $parameter->findAll();
        $data['title'] = ucwords(str_replace("-", " ", 'parameters'));
        return view('pages/setting/parameter', $data);
    }
    public function schedule()
    {
		if(!in_array("WEB.VIEW.SETTING.MAINTENANCE.SCHEDULE", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        $parameter = new \App\Models\VwCemsParameter();
        $schedule = new \App\Models\Schedule();
        // get current schedule
        $currentSchedule = array_map(function($e){ return $e['cemsParameterId']; }, $schedule->where([
            'executed'  => 0,
            'hour >='   => date('Y-m-d H:i:s', strtotime(Time::now()))
        ])->select(['cemsParameterId'])->get()->getResultArray());

        // get all schedule
        $allSchedule = array_map(function($e){ 
            return array([
                'dates'     =>  $e['hour'],
                'passed'    =>  date('Y-m-d H:i:s', strtotime($e['hour'])) < date('Y-m-d H:i:s', strtotime(Time::now())),
                'executed'  =>  $e['executed']
            ])[0];
        }, $schedule->findAll());

        // 
        $data['parameter'] = null;
        if($currentSchedule){
            $data['parameter']  = $parameter->where([
                'maintenance_scheduling' => 1,
                'active' => 1
            ])->whereNotIn('cemsParameterId', $currentSchedule)->findAll();
        }
        $data['parameter']  = $parameter->where([
            'maintenance_scheduling' => 1,
            'active' => 1
        ])->findAll();
		if(in_array("WEB.VIEW.DASHBOARD.U12", session()->get('role'))){
            if($currentSchedule){
                $data['parameter']  = $parameter->where([
                    'maintenance_scheduling' => 1,
                    'active' => 1,
                    'cemsId <' => 3
                ])->whereNotIn('cemsParameterId', $currentSchedule)->findAll();
            }
            $data['parameter']  = $parameter->where([
                'maintenance_scheduling' => 1,
                'active' => 1,
                'cemsId <' => 3
            ])->findAll();
		}
		if(in_array("WEB.VIEW.DASHBOARD.U34", session()->get('role'))){
            if($currentSchedule){
                $data['parameter']  = $parameter->where([
                    'maintenance_scheduling' => 1,
                    'active' => 1,
                    'cemsId >' => 2
                ])->whereNotIn('cemsParameterId', $currentSchedule)->findAll();
            }
            $data['parameter']  = $parameter->where([
                'maintenance_scheduling' => 1,
                'active' => 1,
                'cemsId >' => 2
            ])->findAll();
		}

        $data['cems'] = $this->cems;
        $data['cemsParameter'] = $this->parameter;
        $data['title'] = ucwords(str_replace("-", " ", 'maintenance schedule'));
        $data['allSchedule'] = $allSchedule;
        return view('pages/setting/schedule', $data);
        // echo json_encode($allSchedule );
    }
    public function email()
    {
		if(!in_array("WEB.VIEW.SETTING.EMAIL", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
		$data['cems'] = $this->cems;
        $data['title'] = ucwords(str_replace("-", " ", 'e-mail notification list'));
        return view('pages/setting/email', $data);
    }

    public function accountManagement()
    {
        if( !in_array('Superuser', session()->get('group')) ){
            throw PageNotFoundException::forPageNotFound();
        }
        $data['title'] = 'Account Management';
        return view('pages/setting/account', $data);
    }

    public function getUser()
    {
        try {
            $client = Request::post(env('API').'users/list')
            ->method(Http::POST)
            ->expectsJson()
            ->addHeader('X-Requested-With', 'XMLHttpRequest')
            ->addHeader('Authorization', session()->get('token'))
            ->sendsType(Mime::UPLOAD)
            ->body([
                'appId'     =>  session()->get('appId')
            ])
            ->send();

            if($client->code == 200){
                if($client->body->status){
                    $data = $client->body->data;
    
                    return json_encode([
                        'status'    =>  true,
                        'data'      => $data
                    ]);
                }else {
                    throw new Exception($client->body->message ?? 'Failed process data');
                }
            }else {
                    throw new Exception($client->body->message ?? 'Failed process data');
            }

        }catch(Exception $e){
            echo json_encode([
                'status'    =>  false,
                'message'   =>  $e->getMessage()      
            ]);
        }
    }

    
    public function getGroup()
    {
        try {
            $client = Request::post(env('API').'groups/list')
            ->method(Http::POST)
            ->expectsJson()
            ->addHeader('X-Requested-With', 'XMLHttpRequest')
            ->addHeader('Authorization', session()->get('token'))
            ->sendsType(Mime::UPLOAD)
            ->body([
                'appId'     =>  session()->get('appId')
            ])
            ->send();

            if($client->code == 200){
                if($client->body->status){
                    $data = $client->body->data;
                    $data = array_map(function($ar){
                        return [
                            'groupId' => $ar->groupId,
                            'name' => $ar->name,
                        ];
                    }, $data);
    
                    return json_encode([
                        'status'    =>  true,
                        'data'      => $data
                    ]);
                }else {
                    throw new Exception($client->body->message ?? 'Failed process data');
                }
            }else {
                    throw new Exception($client->body->message ?? 'Failed process data');
            }

        }catch(Exception $e){
            echo json_encode([
                'status'    =>  false,
                'message'   =>  $e->getMessage()      
            ]);
        }
    }

    public function addUser()
    {
        try {
            $request = \Config\Services::request();
            if($request->getPost('name') && $request->getPost('email')){
                $client = null;
                if(!$request->getPost('userId')){
                    if(!$request->getPost('password') && !$request->getPost('groupId')){
                        throw new Exception('Form required: Name, E-Mail, Password, Group');
                        die;
                    }
                    $client = Request::post(env('API').'users/create')
                            ->method(Http::POST)
                            ->expectsJson()
                            ->addHeader('X-Requested-With', 'XMLHttpRequest')
                            ->addHeader('Authorization', session()->get('token'))
                            ->sendsType(Mime::UPLOAD)
                            ->body([
                                'appId'             =>  session()->get('appId'),
                                'name'              =>  $request->getPost('name'),
                                'email'             =>  $request->getPost('email'),
                                'password'          =>  $request->getPost('password'),
                                'groupId'           =>  $request->getPost('groupId'),
                                'parameter[DIS]'    =>  1
                            ])
                            ->send();
                }else {
                    $client = Request::post(env('API').'users/create')
                            ->method(Http::POST)
                            ->expectsJson()
                            ->addHeader('X-Requested-With', 'XMLHttpRequest')
                            ->addHeader('Authorization', session()->get('token'))
                            ->sendsType(Mime::UPLOAD)
                            ->body([
                                'appId'             =>  session()->get('appId'),
                                'userId'            =>  $request->getPost('userId'),
                                'name'              =>  $request->getPost('name'),
                                'parameter[DIS]'    =>  1
                            ])
                            ->send();
                }

                if($client->code == 200){
                    if($client->body->status){
                        return json_encode([
                            'status'    =>  true,
                            'message'   =>  $request->getPost('userId') ? 'Success update User' : 'Success add User'
                        ]);
                    }else {
                        throw new Exception($client->body->message ?? 'Failed process data');
                    }
                }else {
                    throw new Exception($client->body->message ?? 'Failed process data');
                }
            }else {
                throw new Exception('Form required: Name, E-Mail, Password, Group');
            }

        }catch(Exception $e){
            echo json_encode([
                'status'    =>  false,
                'message'   =>  $e->getMessage()      
            ]);
        }
    }

    public function deleteUser()
    {
        try {
            $request = \Config\Services::request();
            if($request->getPost('userId')){
                $client = Request::post(env('API').'users/delete_user')
                        ->method(Http::POST)
                        ->expectsJson()
                        ->addHeader('X-Requested-With', 'XMLHttpRequest')
                        ->addHeader('Authorization', session()->get('token'))
                        ->sendsType(Mime::UPLOAD)
                        ->body([
                            'appId'     =>  session()->get('appId'),
                            'userId'    =>  $request->getPost('userId'),
                            'force'     =>  $request->getPost('force') ?? null,
                        ])
                        ->send();

                if($client->code == 200){
                    if($client->body->status){
                        return json_encode([
                            'status'    =>  true,
                            'message'   =>  $request->getPost('force') ? 'Success delete User' : 'Success deactivate User'
                        ]);
                    }else {
                        throw new Exception($client->body->message ?? 'Failed process data');
                    }
                }else {
                    throw new Exception($client->body->message ?? 'Failed process data');
                }
            }else {
                throw new Exception('Form required: UserId');
            }

        }catch(Exception $e){
            echo json_encode([
                'status'    =>  false,
                'message'   =>  $e->getMessage()      
            ]);
        }
    }

    public function changePassword()
    {
        try {
            $request = \Config\Services::request();
            if($request->getPost('userId') && $request->getPost('password')){
                $client = Request::post(env('API').'auth/change_password')
                        ->method(Http::POST)
                        ->expectsJson()
                        ->addHeader('X-Requested-With', 'XMLHttpRequest')
                        ->addHeader('Authorization', session()->get('token'))
                        ->sendsType(Mime::UPLOAD)
                        ->body([
                            'userId'            =>  $request->getPost('userId'),
                            'password'          =>  $request->getPost('password') ?? null,
                            'confirm_password'  =>  $request->getPost('password') ?? null,
                        ])
                        ->send();

                if($client->code == 200){
                    if($client->body->status){
                        return json_encode([
                            'status'    =>  true,
                            'message'   =>  'Success change password'
                        ]);
                    }else {
                        throw new Exception($client->body->message ?? 'Failed process data');
                    }
                }else {
                    throw new Exception($client->body->message ?? 'Failed process data');
                }
            }else {
                throw new Exception('Form required: UserId, Password');
            }

        }catch(Exception $e){
            echo json_encode([
                'status'    =>  false,
                'message'   =>  $e->getMessage()      
            ]);
        }
    }

    public function saveCompanyProfile($companyProfileId = 0)
    {
		if(!in_array("WEB.VIEW.SETTING.COMPANY", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        if ($_POST) {
            $log = new Log();
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

                $log->save([
                    'user'      =>  session()->get('user_name'),
                    'address'   =>  $this->IP,
                    'activity'  =>  'Update Company Profile',
                    'data'      =>  json_encode($builder->where("companyProfileId", $companyProfileId)->get()->getResultArray())
                ]);

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

                    $log->save([
                        'user'      =>  session()->get('user_name'),
                        'address'   =>  $this->IP,
                        'activity'  =>  'Update Company Profile',
                        'data'      =>  json_encode($builder->where("companyProfileId", $getData[0]->companyProfileId)->get()->getResultArray())
                    ]);

                    echo json_encode(array(
                        "status"    => 200,
                        "Message"   => "Success Update",
                        "data"  => $data
                    ));
                } else {
                    $builder->insert($data);
                    // $builder_backup->insert($data);

                    $log->save([
                        'user'      =>  session()->get('user_name'),
                        'address'   =>  $this->IP,
                        'activity'  =>  'Add Company Profile',
                        'data'      =>  json_encode($builder->where('companyProfileId', $db->insertID())->get()->getResultArray())
                    ]);

                    echo json_encode(array(
                        "status"    => 200,
                        "Message"   => "Success Add",
                        "reload"    => true
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

    public function saveSispek($sispekId = 0)
    {
		if(!in_array("WEB.VIEW.SETTING.SISPEK", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        if ($_POST) {
            $log = new Log();
            $db = \Config\Database::connect();
            $builder = $db->table("tblm_sispek");

            $data["baseURL"] = $_POST["baseURL"];
            $data["appId"] = $_POST["appId"];
            $data["appSecret"] = $_POST["appSecret"];
            $data["klhkAddress"] = $_POST["klhkAddress"];
            $data["backendIP"] = $_POST["backendIP"];
            $data["appIP"] = $_POST["appIP"];

            if ($sispekId > 0) {
                $builder->where("sispekId", $sispekId)->update($data);
                // $builder_backup->where("sispekId", $sispekId)->update($data);

                $log->save([
                    'user'      =>  session()->get('user_name'),
                    'address'   =>  $this->IP,
                    'activity'  =>  'Update Sispek',
                    'data'      =>  json_encode($builder->where("sispekId", $sispekId)->get()->getResultArray())
                ]);
                echo json_encode(array(
                    "status"    => 200,
                    "Message"   => "Success Update",
                    "data"  => $data
                ));
            } else {
                $builder->insert($data);
                // $builder_backup->insert($data);

                $log->save([
                    'user'      =>  session()->get('user_name'),
                    'address'   =>  $this->IP,
                    'activity'  =>  'Add Sispek',
                    'data'      =>  json_encode($builder->where('sispekId', $db->insertID())->get()->getResultArray())
                ]);
                echo json_encode(array(
                    "status"    => 200,
                    "Message"   => "Success Add",
                    "reload"    => true
                ));
            }
        } else {
            echo json_encode(array(
                "status"    => 400,
                "Message"   => "Wrong Method"
            ));
        }
    }

    public function updateChimneyCode()
    {
		if(!in_array("WEB.VIEW.SETTING.SISPEK", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        if($_POST['data']){
            $log = new Log();
            $db = \Config\Database::connect();
            $builder = $db->table("tblm_cems");
            try{
                foreach($_POST['data'] as $data){
                    // return json_encode($data);
                    $check = $builder->where('cemsId', $data['cemsId']);
                    if($check){
                        $builder->update([
                            'chimneyCode'   =>  $data['chimneyCode'],
                            'syncLog'       =>  $data['syncLog']
                        ], [
                            'cemsId'        =>  $data['cemsId']
                        ]);
                        $log->save([
                            'user'      =>  session()->get('user_name'),
                            'address'   =>  $this->IP,
                            'activity'  =>  'Update Chimney Code',
                            'data'      =>  $data['chimneyCode']
                        ]);
                        $log->save([
                            'user'      =>  session()->get('user_name'),
                            'address'   =>  $this->IP,
                            'activity'  =>  'Update Automation Sync Log',
                            'data'      =>  $data['syncLog']
                        ]);
                    }
                }
                echo json_encode(array(
                    "status"    => 200,
                    "Message"   => 'Success Update Data'
                ));
            }catch(Exception $e){
                echo json_encode(array(
                    "status"    => 500,
                    "Message"   => $e->getMessage()
                ));
            }
        } else {
            echo json_encode(array(
                "status"    => 500,
                "Message"   => "Parameter Empty"
            ));
        }

    }

    public function saveNotification($notificationId = 0)
    {
		if(!in_array("WEB.VIEW.SETTING.NOTIFICATION", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        if ($_POST) {
            $log = new Log();
            $db = \Config\Database::connect();
            $builder = $db->table("tblm_notification");
            // $db_backup = \Config\Database::connect('backup', false);
            // $builder_backup = $db_backup->table("tblm_notification");

            $data["telegramName"] = $_POST["telegramName"];
            $data["telegramToken"] = $_POST["telegramToken"];
            $data["chanelName"] = $_POST["chanelName"];
            $data["chatId"] = $_POST["chatId"];

            if ($notificationId > 0) {
                $builder->where("notificationId", $notificationId)->update($data);
                // $builder_backup->where("notificationId", $notificationId)->update($data);

                $log->save([
                    'user'      =>  session()->get('user_name'),
                    'address'   =>  $this->IP,
                    'activity'  =>  'Update Notification Data',
                    'data'      =>  json_encode($builder->where("notificationId", $notificationId)->get()->getResultArray())
                ]);
                echo json_encode(array(
                    "status"    => 200,
                    "Message"   => "Success Update",
                    "data"  => $data
                ));
            } else {
                $builder->insert($data);

                $log->save([
                    'user'      =>  session()->get('user_name'),
                    'address'   =>  $this->IP,
                    'activity'  =>  'Add Notification Data',
                    'data'      =>  json_encode($builder->where('notificationId', $db->insertID())->get()->getResultArray())
                ]);
                echo json_encode(array(
                    "status"    => 200,
                    "Message"   => "Success Add",
                    "reload"    => true
                ));
            }
        } else {
            echo json_encode(array(
                "status"    => 400,
                "Message"   => "Wrong Method"
            ));
        }
    }


    public function ajax_list()
    {
        $table = 'tblm_parameter';
        $column_order = array('name', 'type', 'created_at');
        $column_search = array('name', 'type', 'created_at');
        $order = array('created_at' => 'asc');

		$request = \Config\Services::request();
        $datatable = new \App\Models\Datatable($table, $column_order, $column_search, $order);;
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

    public function ajax_list_schedule()
    {
        $table = 'vw_schedule';
        $column_order = array('parameterName', 'created_at', 'cemsId', 'hour', 'timestamp_executed', 'timestamp_end');
        $column_search = array('parameterName', 'created_at', 'cemsId', 'hour', 'timestamp_executed', 'timestamp_end');
        $order = array('created_at' => 'asc');

		$request = \Config\Services::request();
        $datatable = new \App\Models\Datatable($table, $column_order, $column_search, $order);
        $cemsParameter = explode(',', $request->getPost('cemsParameter'));
        $where = [
            'cemsId'    =>  $request->getPost('cemsId'),
			'hour >='   => date('Y-m-d H:i:s', strtotime($request->getPost('from'))),
			'hour <='   => date('Y-m-d H:i:s', strtotime($request->getPost('to'))),
        ];
        // foreach($cemsParameter as $key => $value){
        //     if($key == 0){
        //         $where['cemsParameterId LIKE'] = $value;
        //     }else {
        //         $where['cemsParameterId OR'] = $value;
        //     }
        // }
        $list = $datatable->datatable($where);
        $output = array(
            "draw" => $request->getPost('draw'),
            "recordsTotal" => $datatable->count_all($where),
            "recordsFiltered" => $datatable->count_filtered($where),
            "data" => $list
        );
        echo json_encode($output);
	}

    public function ajax_list_email()
    {
        $table = 'tblm_email';
        $column_order = array('id', 'created_at', 'email', 'cemsId');
        $column_search = array('id', 'created_at', 'email', 'cemsId');
        $order = array('created_at' => 'asc');

		$request = \Config\Services::request();
        $datatable = new \App\Models\Datatable($table, $column_order, $column_search, $order);;
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

    public function saveParameter()
    {
		if(!in_array("WEB.VIEW.SETTING.PARAMETER", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        $request = \Config\Services::request();
        $model = new \App\Models\MParameter();
        $log = new Log();
        if($request->getPost('parameterId') && $request->getPost('parameterId') != '' && $request->getPost('parameterId') != null && $request->getPost('parameterId') != 'null'){
            try {
                $model->save([
                    'parameterId'   =>  $request->getPost('parameterId'),
                    'name'          =>  $request->getPost('name'),
                    'type'          =>  $request->getPost('type'),
                ]);
                $log->save([
                    'user'      =>  session()->get('user_name'),
                    'address'   =>  $this->IP,
                    'activity'  =>  'Update Parameter',
                    'data'      =>  json_encode([
                        'name'  =>  $request->getPost('name'),
                        'type'  =>  $request->getPost('type'),
                    ])
                ]);
                return json_encode([
                    'status'    =>  true
                ]);
            }catch(Exception $e){
                return json_encode([
                    'status'    =>  false,
                    'message'   =>  $e->getMessage()
                ]);
            }
        }else {
            try {
                $model->save([
                    'name'  =>  $request->getPost('name'),
                    'type'  =>  $request->getPost('type'),
                ]);
                $log->save([
                    'user'      =>  session()->get('user_name'),
                    'address'   =>  $this->IP,
                    'activity'  =>  'Save Parameter',
                    'data'      =>  json_encode([
                        'name'  =>  $request->getPost('name'),
                        'type'  =>  $request->getPost('type'),
                    ])
                ]);
                return json_encode([
                    'status'    =>  true
                ]);
            }catch(Exception $e){
                return json_encode([
                    'status'    =>  false,
                    'message'   =>  $e->getMessage()
                ]);
            }
        }
    }

    public function saveEmail()
    {
		if(!in_array("WEB.VIEW.SETTING.EMAIL.ADD", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        $request = \Config\Services::request();
        $model = new \App\Models\Email();
        $log = new Log();
        if($_POST){
            if($request->getPost('id')){
                try {
                    $model->save([
                        'id'   =>  $request->getPost('id'),
                        'email'  =>  $request->getPost('email'),
                        'name'  =>  $request->getPost('name')
                    ]);
                    $log->save([
                        'user'      =>  session()->get('user_name'),
                        'address'   =>  $this->IP,
                        'activity'  =>  'Update E-Mail',
                        'data'      =>  json_encode([
                            'email'  =>  $request->getPost('email')
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
            }else {
                try {
                    $model->save([
                        'email'  =>  $request->getPost('email'),
                        'name'  =>  $request->getPost('name')
                    ]);
                    $log->save([
                        'user'      =>  session()->get('user_name'),
                        'address'   =>  $this->IP,
                        'activity'  =>  'Save E-Mail',
                        'data'      =>  json_encode([
                            'email'  =>  $request->getPost('email')
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
                'message'   =>  'Wrong method'
            ]);
        }
    }

    public function deleteEmail()
    {
		if(!in_array("WEB.VIEW.SETTING.EMAIL.DELETE", session()->get('role'))){
            throw PageNotFoundException::forPageNotFound();
		}
        $request = \Config\Services::request();
        $model = new \App\Models\Email();
        // $modelBackup = new \App\Models\EmailBackup();
        $log = new Log();
        if($_POST){
            $id = $request->getPost('id');
            if(!$id){
                echo json_encode([
                    'status'    => false,
                    'message'   => 'ID empty'
                ]); 
            }else {
                $check = $model->where('id', $id)->get()->getRow();
                if(!$check){
                    echo json_encode([
                        'status'    => false,
                        'message'   => 'Data not found'
                    ]); 
                }else {
                    !$check->deleted_at ? $model->delete($id, false) : $model->update($id, [ 'deleted_at'   => null ]);
                    // !$check->deleted_at ? $modelBackup->delete($id, false) : $modelBackup->update($id, [ 'deleted_at'   => null ]);
                    $log->save([
                        'user'      =>  session()->get('user_name'),
                        'address'   =>  $this->IP,
                        'activity'  =>  $check->deleted_at ? 'Delete E-Mail' : 'Restore E-Mail',
                        'data'      =>  json_encode([
                            'email'  =>  $check->email
                        ])
                    ]);
                    echo json_encode([
                        'status'    => true
                    ]);
                }
            }
        }else {
            echo json_encode([
                'status'    => false,
                'message'   => 'Method not supported'
            ]);
        }
    }
    
    public function saveSchedule()
    {
        $request = \Config\Services::request();
        $model = new \App\Models\Schedule();
        $log = new Log();
        if($_POST){
            if($request->getPost('scheduleId')){
                try {
                    foreach(explode(',', $request->getPost('cemsParameterId')) as $id){
                        $model->save([
                            'scheduleId'        =>  $request->getPost('scheduleId'),
                            'hour'              =>  $request->getPost('hour'),
                            'cemsParameterId'   =>  intval($id),
                            'executed'          =>  0
                        ]);
                    }
                    $log->save([
                        'user'      =>  session()->get('user_name'),
                        'address'   =>  $this->IP,
                        'activity'  =>  'Update Schedule',
                        'data'      =>  json_encode([
                            'scheduleId'        =>  $request->getPost('scheduleId'),
                            'hour'              =>  $request->getPost('hour'),
                            'cemsParameterId'   =>  $request->getPost('cemsParameterId'),
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
            }else {
                try {
                    $i = 0;
                    foreach(explode(',', $request->getPost('cemsParameterId')) as $id){
                        $model->save([
                            'hour'              =>  $request->getPost('hour'),
                            'cemsParameterId'   =>  intval($id),
                            'executed'          =>  0,
                            'created_by'        =>  session()->get('user_name')
                        ]);
                    }
                    $log->save([
                        'user'      =>  session()->get('user_name'),
                        'address'   =>  $this->IP,
                        'activity'  =>  'Save Schedule',
                        'data'      =>  json_encode([
                            'hour'              =>  $request->getPost('hour'),
                            'cemsParameterId'   =>  $request->getPost('cemsParameterId'),
                        ])
                    ]);
                    echo json_encode([
                        'status'    =>  true,
                        'id'        =>  $request->getPost('cemsParameterId')
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
                'message'   =>  'Wrong method'
            ]);
        }
    }

    public function removeSchedule()
    {
        $request = \Config\Services::request();
        $model = new \App\Models\Schedule();
        $log = new Log();
        if($_POST){
            if($request->getPost('id')){
                $check = $model->find($request->getPost('id'));
                if($check){
                    try {
                        $model->delete($request->getPost('id'));
                        $log->save([
                            'user'      =>  session()->get('user_name'),
                            'address'   =>  $this->IP,
                            'activity'  =>  'Delete Schedule',
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

    
    public function doneSchedule()
    {
        $db = \Config\Database::connect();
        $request = \Config\Services::request();
        $model = new \App\Models\Schedule();
        $parameter = $db->table('tblm_parameter');
        $tparameter = $db->table('tblt_parameter');
        $cemsParameter = new \App\Models\CemsParameter();
        $log = new Log();
        if($_POST){
            if($request->getPost('id')){
                $check = $model->where('scheduleId', $request->getPost('id'))->get()->getRow();
                if($check){
                    try {
                        $checkParameter = $parameter->where('name', 'maintenance')->get()->getRow();
                        $checkTParameter = $tparameter->where([
                            'm_parameterId'      =>  $checkParameter->parameterId,
                            'cemsParameterId'   =>  $check->cemsParameterId
                        ])->get()->getRow();

                        $tparameter->update(['values' => 0], ['parameterId' => $checkTParameter->parameterId]);

                        $log->save([
                            'user'      =>  session()->get('user_name'),
                            'address'   =>  $this->IP,
                            'activity'  =>  'Done Maintenance Parameter',
                            'data'      =>  $cemsParameter->where('cemsParameterId', $check->cemsParameterId)->get()->getRow()->parameterName
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
    public function executeScheduleMaintenance()
    {
        $db = \Config\Database::connect();
        $schedule = $db->query("SELECT ts.cemsParameterId FROM db_cems.tblt_schedule ts WHERE date_format(ts.`hour`, '%Y-%m-%d %H:%i:00') = date_format(now(), '%Y-%m-%d %H:%i:00')")->getResult();
        
        $log = new Log();
        try {
            if(count($schedule) > 0){
                $exec = $db->query("CALL sp_scheduleMaintenance()")->getResult();
                
                $log->save([
                    'user'      =>  session()->get('user_name'),
                    'address'   =>  $this->IP,
                    'activity'  =>  'Execute Maintenance Scheduling'
                ]);
                return json_encode([
                    'status'    => true
                ]);
            }else {
                return json_encode([
                    'status'    => false,
                    'message'   => 'No Schedule at this time'
                ]);
            }
        }catch(Exception $e){
            $log->save([
                'user'      =>  session()->get('user_name'),
                'address'   =>  $this->IP,
                'activity'  =>  'Failed Executing Maintenance Schedule',
                'data'      =>  $e->getMessage()
            ]);
            return json_encode([
                'status'    =>  false,
                'message'   =>  $e->getMessage()
            ]);
        }
    }
}
