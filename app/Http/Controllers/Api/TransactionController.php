<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Base\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Api\Owner;
use App\Models\Api\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends BaseController
{
    /**
     * Return Outlet's Transaction
     *
     * @return \Illuminate\Http\Response
     */
    public function index($user)
    {
        $user = User::find($user);
        $id = $user->owner->outlet->id;

        $transaction = Transaction::with('customer', 'outlet')
            ->where('outlet_id', $id)
            ->orderBy('created_at')
            ->get();
        
        if ($transaction == '[]') 
            return $this->sendError('Transaction Empty', Response::HTTP_NOT_FOUND);

        return $this->sendResponse('List Transaction', Response::HTTP_OK, $transaction);
    }

    /**
     * Store a newly created Transaction in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $transaction_no = Transaction::whereYear('created_at', '=', Carbon::now()->format('Y'))->withTrashed()->count() + 1;
        $transaction_no_format = 'LNDRY' . '-' . Carbon::now()->format('Y') . '/'
            . Carbon::now()->format('m') . '/'
            . str_pad($transaction_no, 5, '0', STR_PAD_LEFT);

        $validator = Validator::make($request->all(), [
            'address' => 'required',
            'laundry_type' => 'required|json',
        ]);

        $isFails = $this->isFails($validator);
        
        if ($isFails == false) { 
            $user = new Transaction([
                'po_number' => $transaction_no_format,
                'address' => $request->address,
                'laundry_type' => $request->laundry_type,
                'customer_id' => Auth::user()->id,
                'outlet_id' => $request->outlet_id,
                'outlet_google_id' => $request->outlet_google_id,
            ]);

            $user->save();
            
            return $this->sendResponse('Transaction Create Successfully', Response::HTTP_CREATED, $user);
        } else
            return $isFails;
    }

    /**
     * Display the specified Transaction.
     *
     * @param  int  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show($transaction)
    {
        $id = Auth::user()->owner->outlet->id;

        $data = Transaction::with('customer', 'outlet')
            ->where([
                ['id', $transaction],
                ['outlet_id', $id]
            ])
            ->orderBy('created_at')
            ->get();
        
        if ($data == '[]') 
            return $this->sendError('Transaction Unknown', Response::HTTP_NOT_FOUND);

        return $this->sendResponse('Show Transaction' , Response::HTTP_OK, $data);
    }

    // public function showTransactionByCustomerId($customer_id){
    //     $data = json_decode(Transaction::where('customer_id', $customer_id)->get(), true);

    //     return $this->sendResponse('Transaction by Customer Id', Response::HTTP_OK, $data);
    // }

    /**
     * Display the specified Transaction by User.
     *
     * @param  int  $user
     * @return \Illuminate\Http\Response
     */
    public function history($user)
    {
        $data = Transaction::with('customer', 'outlet')
            ->where('customer_id', $user)
            ->orderBy('created_at')
            ->get();
        
        if ($data == '[]') 
            return $this->sendError('Transaction Unknown', Response::HTTP_NOT_FOUND);

        return $this->sendResponse('History Transaction' , Response::HTTP_OK, $data);
    }

    /**
     * Update the specified Transaction in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $transaction)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric',
            'amount' => 'required|numeric',
            'status' => 'nullable',
        ]);

        $isFails = $this->isFails($validator);
        
        if ($isFails == false) { 
            $transaction = Transaction::where('id', $transaction)->update([
                'price' => $request->price,
                'amount' => $request->amount,
                'status' => $request->status,
            ]);

            $transaction = Transaction::find($transaction);
            
            return $this->sendResponse('Transaction Update Successfully', Response::HTTP_CREATED, $transaction);
        } else
            return $isFails;
    }

    /**
     * Remove the specified Transaction from storage.
     *
     * @param  int  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();
        
        return $this->sendResponse('Transaction Delete Successfully', Response::HTTP_OK);
    }
}
