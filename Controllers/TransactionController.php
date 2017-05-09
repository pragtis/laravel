<?php

namespace App\Http\Controllers;

use Illuminate\Html\HtmlServiceProvider;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Mail;
use Auth;
use Config;
use Fbpost;
use App\Libraries\TwitterPost;

class TransactionController extends Controller {
    /**
     * Method to get all transactions
     * @param void
     * @return view
     */
    public function getTransactions() {
        // checking user authentication
        if (Auth::check()) {
            // fetching search parameters
            // code for searching
            $searchBy = Input::has('SearchBy') ? Input::get('SearchBy') : '';
            $keyword = Input::has('keyword') ? Input::get('keyword') : '';
    
            // fetching transactions listing
            $transactions = DB::table('transactions')
                    ->join('users', 'transactions.user_id', '=', 'users.id')
                    ->join('profiles', 'users.id', '=', 'profiles.user_id')
                    ->select('transactions.*', 'users.username', 'profiles.name', 'transactions.ACK', 'transactions.PAYMENTTYPE', 'transactions.AMT', 'transactions.PAYMENTSTATUS', 'transactions.created_at','transactions.subscription_type')
                    ->where(function($que) use ($keyword, $searchBy) {
                        if (!empty($searchBy) && !empty($keyword) && $searchBy == 'name') {
                            $que->whereRaw("profiles.name like '%" . $keyword . "%'");
                        } elseif (!empty($searchBy) && !empty($keyword) && $searchBy == 'date') {
                            $que->whereRaw("DATE_FORMAT(transactions.created_at,'%Y-%m-%d') = '" . $keyword . "'");
                        } elseif (!empty($searchBy) && !empty($keyword) && $searchBy == 'tranid') {
                            $que->where('transactions.TRANSACTIONID', $keyword);
                        } elseif (!empty($searchBy) && ($searchBy == 'Completed' || $searchBy == 'Pending')) {
                            $que->where('transactions.PAYMENTSTATUS', $searchBy);
                        }
                    })
                    ->orderBy('transactions.created_at', 'DESC')
                    ->paginate(Config::get('constants.PAGE_LIMIT'));
            return view('transactions.index', ['transactions' => $transactions->appends(Input::except('page'))])->with(compact('transactions', 'searchBy', 'keyword'));
        } else {
            return redirect('/user/login');
        }
    }

    /**
     * Method to view a transaction info
     * @param int transactionId
     * @return view
     */
    public function viewTransaction($transactionId) {
        // checking user authentication
        if (Auth::check()) {
            // fetching particular transaction info
            $transaction = DB::table('transactions')
                    ->join('users', 'transactions.user_id', '=', 'users.id')
                    ->join('profiles', 'users.id', '=', 'profiles.user_id')
                    ->select('transactions.*', 'users.username', 'profiles.name')
                    ->where('transactions.id', $transactionId)
                    ->first();
            // rendering view
            return view('transactions.viewtransaction')->with(compact('transaction'));
        } else {
            return redirect('/user/login');
        }
    }

    /**
     * Method to view transactions by user
     * @param void
     * @return view
     */
    public function userTransactions() {
        // checking user authentication
        if (Auth::check()) {
            // fetching search parameters
            $searchBy = Input::has('SearchBy') ? Input::get('SearchBy') : '';
            $keyword = Input::has('keyword') ? Input::get('keyword') : '';

            // fetching transactions listing by user
            $transactions = DB::table('transactions')
                    ->where('user_id', Auth::id())
                    ->where(function($que) use ($keyword, $searchBy) {
                        if (!empty($searchBy) && !empty($keyword) && $searchBy == 'tranid') {
                            $que->where('transactions.TRANSACTIONID', $keyword);
                        } elseif (!empty($searchBy) && !empty($keyword) && $searchBy == 'date') {
                            $keyword = date('Y-m-d', strtotime($keyword));
                            $que->whereRaw("DATE(transactions.created_at) = '" . $keyword . "'");
                        } elseif (!empty($searchBy) && ($searchBy == 'Completed' || $searchBy == 'Pending')) {
                            $que->where('transactions.PAYMENTSTATUS', $searchBy);
                        }
                    })
                    ->orderBy('transactions.created_at', 'DESC')
                    ->paginate(Config::get('constants.PAGE_LIMIT'));
            // rendering view
            return view('transactions.viewusertran', ['transactions' => $transactions->appends(Input::except('page'))])->with(compact('transactions', 'searchBy', 'keyword'));
        } else {
            return redirect('/user/login');
        }
    }

}
