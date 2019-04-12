<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function index()
    {
//        $this->authorize('isAdmin');
        if(Gate::allows('isAdmin') || Gate::allows('isAuthor')){
            return user::all()->first()->paginate(2);

            //todo change pagination number

        }
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]);


        return User::create([
            'name'=> $request['name'],
            'email'=> $request['email'],
            'type' => $request['type'],
            'bio' => $request['bio'],
            'photo' => $request['photo'],
            'password' => Hash::make($request['password']),
        ]);
    }

    public function show($id)
    {
        //
    }

    public function profile()
    {
        return Auth('api')->user();
    }

    public function updateProfile(Request $request)
    {
        $user = Auth('api')->user();

        $currentPhoto = $user->photo;
        if ($request->photo != $currentPhoto){
            $name = time(). '909'. $user->id . '.'. explode('/',explode(':', substr($request->photo, 0,
                    strpos($request->photo,';')))[1])[1];
            \Image::make($request->photo)->save(public_path('img/profile/').$name);

            $request->merge(['photo' => $name]);

            $userPhotoLocation = public_path('img/profile/').$currentPhoto;  //Old photo path

            @unlink($userPhotoLocation); //Deletes old photo
        }
        $this->validate($request,[
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email,'.$user->id],
            'password' => ['sometimes', 'string', 'min:6'],
        ]);

        if(!empty($request->password)){
            $request->merge(['password' => Hash::make($request['password'])]);
        }

        $user->update($request->all());
    }

    public function update(Request $request, $id)
    {
        $this->authorize('isAdmin');

        $user = User::findOrFail($id);

        $this->validate($request,[
            'name' => ['required', 'string', 'max:191'],
            'type' => ['required'],
            'email' => ['required', 'string', 'email', 'max:191', 'unique:users,email,'.$user->id],
            'password' => ['sometimes', 'string', 'min:6'],
        ]);

        if(!empty($request->password)){
            $request->merge(['password' => Hash::make($request['password'])]);
        }

        $user->update($request->all());
    }

    public function search(){
        if($search = \Request::get('q')){
            $users = User::where(function($query) use ($search){
                $query->where('name','LIKE',"%$search%")
                    ->orWhere('email','LIKE',"%$search%")
                    ->orWhere('type','LIKE',"%$search%");
            })->paginate(3);

            //todo change pagination number
        } else {
            $users = user::all()->first()->paginate(3);
        }

        return $users;
    }

    public function destroy($id)
    {
        $this->authorize('isAdmin');

        $user = User::findOrFail($id);
        $user->delete();

        return ['message' => 'User Deleted'];
    }
}
