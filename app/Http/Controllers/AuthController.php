<?php
namespace app\Http\Controllers;

use App\Helpers\Validation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    private $title;
    private $generalUri;

    public function __construct()
    {
        $this->title = 'Login';
        $this->generalUri = 'login';
    }


    public function login()
    {
        if(Auth::user()){
            return redirect()->route('dashboard.index');
        }
        $data['title'] = $this->title;

        return view('frontend.login', $data);
    }


    public function authenticate(Request $request)
    {
        $canLogin = true;
        $loginWith = "Email";
        $input = $request->all();

        $fieldType = 'email'; //filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $rules = [
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $canLogin = false;
            $message_errors = (new Validation)->modify($validator, $rules);

            return response()->json([
                'status' => false,
                'alert' => 'danger',
                'message' => 'Required Form',
                'validation_errors' => $message_errors,
            ], 200);
        }

        if ($canLogin) {
            if (Auth::attempt(array($fieldType => $input['email'], 'password' => $input['password']))) {
                return response()->json([
                    'status' => true,
                    'alert' => 'success',
                    'message' => 'Login successfully',
                    'redirect_to' => route('dashboard.index'),
                    'validation_errors' => []
                ], 200);
            } else {
                return response()->json([
                    'status' => false,
                    'alert' => 'danger',
                    'message' => $loginWith . " and password didn't match",
                ], 200);
            }
        }
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required'],
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
