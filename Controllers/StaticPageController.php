<?php namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class StaticPageController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | Static Page Controller
    |--------------------------------------------------------------------------
    |
    */
    
    /**
     * Show the listing of static pages
     *
     * @return Response
     */
    public function index() {
	if (!Session::has('ADMIN_USER')) {
            return Redirect::to('/admin/user/login');
        }

        $arrPages = DB::table('static_pages')
		    ->select('static_pages.id', 'static_pages.keyword', 'static_pages.title', 'static_pages.description', 'static_pages.created_at')
		    ->orderBy('static_pages.id', 'DESC')
		    ->paginate(Config::get('constants.PAGE_LIMIT'));
	// rendering view
	return view('StaticPage.index')->with(compact('arrPages'));
    }
    
    /**
     * To edit static page
     *
     * @return Response
     */
    public function update($sid=null) {
        if (!Session::has('ADMIN_USER')) {
            return Redirect::to('/admin/user/login');
        }

        if (Request::isMethod('post')) {
            $rules = array(
		'title' => 'required|min:3|max:100',
		'description' => 'required|min:50'
	    );
	    // run the validation rules on the inputs from the form
	    $validator = Validator::make(Input::all(), $rules);
	    if ($validator->fails()) {
		return Redirect::to('/admin/staticpage/update/'.$sid)->withErrors($validator)->withInput();
	    } else {
                DB::table('static_pages')->where('id', $sid)->update(['title' => Input::get('title'), 'description' => Input::get('description')]);
                return Redirect::to('/admin/manage/staticPages')->with('message', 'Page successfully updated.');
            }
        }
        
        $arrPage = DB::table('static_pages')
		    ->select('static_pages.id', 'static_pages.keyword', 'static_pages.title', 'static_pages.description', 'static_pages.created_at')
		    ->where('id', $sid)
		    ->first();
	// rendering view
	return view('StaticPage.update')->with(compact('arrPage', 'sid'));
    }
    
    /**
     * To show particular static page
     *
     * @return Response
     */
    public function showPage($page=null) {
	$arrPage = DB::table('static_pages')->where('keyword', $page)
		    ->select('static_pages.id', 'static_pages.keyword', 'static_pages.title', 'static_pages.description', 'static_pages.created_at')
		    ->first();
	// rendering view
	return view('StaticPage/info')->with(compact('arrPage'));
    }
}