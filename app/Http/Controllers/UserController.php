<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * UserController
 *
 * @author Pedro Terra <phterra083@gmail.com>
 * @since 01/06/2026
 */
class UserController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {
        
        $query = User::search($request);

        $n = $request->get('limit', 10);

        $list = $query->paginate($n);

        $data = [
            'list' => $list
        ];

        return view ('pages.user.index', $data);

    }

    public function create(Request $request) {
        $user = new User();

        return $this -> form($user);
    }

    public function insert (Request $request) {

        //TODO Valider

        $user = new User();

        $this->save($user, $request);

        Session::flash('sucess', 'O usuário foi criado com sucesso!');

        return redirect('/usuarios');
    }

    public function edit(Request $request, int $id){

        $user = User::find($id);
        if($user){
            return $this->form($user);
        }

        else{
            #TODO . . .
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request) {
        
        $user = User::find($request->id);

        $this->save($user, $request);

        Session::flash('sucess', 'O usuário foi criado com sucesso!');

        return redirect('/usuarios');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(int $id) {
        //
    }


    private function form(User $user){

        $data = [
            'user' => $user,
            
        ];
        return view('pages.user.form', $data);

    }




    /**
     * Salvar alterações no usuário
     *
     * @param User $user
     * @param Request $request
     * @return void
     */
    private function save(User $user,Request $request) {

         $user->name = $request->name;

         $user->email= $request->email;

         if($request -> password){
            $user->password = bcrypt ($request->password);
         }

         $user->save();
    }

    private function validation(Resquest $request) {

        

    }
}
