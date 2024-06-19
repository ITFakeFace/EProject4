<?php

namespace App\Http\Controllers;

use App\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Mail;
use DB;
use Illuminate\Support\Str;

class AuthenticateController extends Controller
{
    public function getLogin()
    {
        if (auth()->user()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function postDoLogin(Request $request)
    {
        $data = $request->all();
        $rule = [
            'email' => 'required|exists:staff,email',
            'password' => 'required',
        ];
        $message = [];
        $validate = Validator::make($data, $rule, $message);
        if ($validate->fails()) {
            return redirect()->back()->withErrors($validate)->withInput();
        }
        $email = $data['email'];
        $password = md5($data['password']);
        $user = Staff::where(['email' => $email, 'password' => $password])->first();

        if($user) {
            $params_get_department = [
                'id' => $user->id,
            ];
            $response_get_department = Http::get('http://localhost:8888/staff/findOneStaffDepartment', $params_get_department);
            $body_get_department = json_decode($response_get_department->body(), true);

            $request->session()->put('department_name', $body_get_department['data'][0][2]);
        }

        if ($user && Auth::login($user)) {
             // login thành công thì redirect tới trang nào đó tùy
            //return response(auth()->user()); // thông tin user
            return redirect('/');
        }
        return redirect()->back()->with('authentication', 'Không tìm thấy thông tin tài khoản');
    }

    public function getLogout(Request $request)
    {
        Auth::logout();
        $request->session()->flush();
        return redirect('auth/login');
    }

    public function getForgot()
    {
        if (auth()->user()) {
            return redirect('/forgot-password');
        }
        return view('auth.forgot');
    }

    public function postForgot(Request $request)
    {
        $email = $request->input('email');

        if(!DB::table('staff')->where('email', $email)->value('id')) {
            return redirect()->back()->with('error', 'Email không phải là email của nhân viên');
        }

        $token = Str::random(60);

        DB::table('reset_password')->insert(
            ['email' => $request->email, 'token' => $token, 'created_at' => Carbon::now()]
        );

        Mail::send('auth.email',['token' => $token], function($message) use ($request) {
            $message->from('nfsred2406@gmail.com');
            $message->to($request->email);
            $message->subject('Reset Password Notification');
        });
        return back()->with('success', 'Chúng tôi đã gửi hướng dẫn tới địa chỉ email của bạn!');
    }

    public function getReset(Request $request)
    {
        $token = $request->input('token');

        return view('auth.reset' ,['token' => $token]);
    }

    public function postReset(Request $request)
    {
        $token = $request->input('token');
        $password = md5($request->input('password'));
        $password_confirm = md5($request->input('password_confirm'));

        if ($request->input('password') != $request->input('password_confirm')) {
            return redirect()->back()->with('error', 'Mật khẩu mới và xác nhận mật khẩu không giống nhau');
        }

        if (strlen($request->input('pass_new')) > 20) {
            return redirect()->back()->with('error', 'Mật khẩu mới không được dài quá 20 kí tự');
        }

        $email = DB::table('reset_password')->where('token', $token)->value('email');

        if(!$email) {
            return back()->with('error', 'Lỗi sai token');
        }

        $id_staff = DB::table('staff')->where('email', $email)->value('id');

        $params = [
            'id' => $id_staff,
            'pass_new' => $password
        ];

        $response = Http::post('http://localhost:8888/staff/change-pass-forgot', $params);
        $body = json_decode($response->body(), true);

        if ($body['data'] == "Change password Success") {
            return redirect()->back()->with('success', 'Đổi mật khẩu thành công');
        } else {
            return redirect()->back()->with('success', 'Đổi mật khẩu thất bại');
        }
    }
}
