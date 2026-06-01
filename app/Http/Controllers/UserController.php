<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


/**
 * UserController
 *
 * @author Pedro Terra <phterra083@gmail.com>
 * @since 01/06/2026
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $query = User::search($request);

        $n = $request->get('limit', 10);

        $list = $query->paginate($n);

        $data = [
            'list' => $list
        ];

        return view ('pages.user.index', $data);

    }

   /**
    * Visualizar vier de formulário para cadastrar um novo usuário
    *
    * @param Request $request
    * @return ($view is null ? \Illuminate\Contracts\View\Factory : \Illuminate\Contracts\View\View)
    */
    public function create(Request $request)
    {
        $user = new User();
        return $this -> form($user);
    }

    public function insert (Request $request)
    {
        //
    }

    /**
    * Visualizar vier de formulário para editar um usuário
    *
    * @param Request $request
    * @return ($view is null ? \Illuminate\Contracts\View\Factory : \Illuminate\Contracts\View\View)
    */
    public function edit(Request $request, int $id)
    {

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
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(int $id)
    {
        //
    }


    /**
     * Visualizar view de formulário
     *
     * @param User $user
     * @return ($view is null ? \Illuminate\Contracts\View\Factory : \Illuminate\Contracts\View\View)
     */
    private function form(User $user){

        $data = [
            'data' => $user,
            
        ];
        return view('pages.user.form', $data);

    }
}
