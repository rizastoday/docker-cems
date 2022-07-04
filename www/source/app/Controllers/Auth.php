<?php

namespace App\Controllers;

use App\Models\Log;
use App\Models\Users;
use Exception;
use Httpful\Http;
use Httpful\Mime;
use Httpful\Request;


class Auth extends BaseController
{
    public function index()
    {
        if(session()->get('logged_in')){
            return redirect()->to(base_url('/dashboard'));
        }
        $session = session();
        helper(['form']);
        $data['title'] = 'Login';
        return view('pages/auth/login', $data);
    }

    public function auth_http()
    {
        try {
            $request = \Config\Services::request();
            $email = $request->getPost('email');
            $password = $request->getPost('password');
            $session = session();

            $client = Request::post(env('API').'auth/auth_client')
            ->method(Http::POST)
            ->expectsJson()
            ->addHeader('X-Requested-With', 'XMLHttpRequest')
            ->sendsType(Mime::UPLOAD)
            ->body([
                'email'     =>  $email,  
                'password'  =>  $password,
                'platform'  =>  env('PLATFORM')
            ])
            ->send();

            if($client->code == 200){
                if($client->body->status){
                    $data = $client->body->data;
                    $session->set([
                        'logged_in'     =>  true,
                        'userId'        =>  $data->userId,
                        'appId'         =>  $data->appId,
                        'user_name'     =>  $data->name,
                        'email'         =>  $data->email,
                        'group'         =>  explode(',', $data->group),
                        'role'          =>  explode(',', $data->roles),
                        'parameter'     =>  $data->parameter,
                        'token'         =>  $data->token
                    ]);
    
                    $log = new Log();
                    $log->save([
                        'user'      =>  session()->get('user_name'),
                        'address'   =>  $this->IP,
                        'activity'  =>  'Login to application'
                    ]);
                    return json_encode([
                        'status'    =>  true
                    ]);
                }else {
                    throw new Exception($client->body->message ?? 'Failed to login');
                }
            }else {
                throw new Exception($client->body->message ?? 'Failed to login');
            }

        }catch(Exception $e){
            echo json_encode([
                'status'    =>  false,
                'message'   =>  $e->getMessage()      
            ]);
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to(base_url()); 
    }
}