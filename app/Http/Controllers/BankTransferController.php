<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankTransfer;
use App\Models\Utility;
use Illuminate\Http\Request;

class BankTransferController extends Controller
{

    public function index(Request $request)
    {

        if(\Auth::user()->can('manage bank transfer'))
        {
            $account = BankAccount::where('created_by', '=', \Auth::user()->creatorId())->get()->pluck('holder_name', 'id');
            $account->prepend('Select Account', '');

            $query = BankTransfer::where('created_by', '=', \Auth::user()->creatorId());

            if(count(explode('to', $request->date)) > 1)
            {
                $date_range = explode(' to ', $request->date);
                $query->whereBetween('date', $date_range);
            }
            elseif(!empty($request->date))
            {
                $date_range = [$request->date , $request->date];
                $query->whereBetween('date', $date_range);
            }


            if(!empty($request->f_account))
            {
                $query->where('from_account', '=', $request->f_account);
            }
            if(!empty($request->t_account))
            {
                $query->where('to_account', '=', $request->t_account);
            }
            $transfers = $query->with(['fromBankAccount','toBankAccount'])->get();

            return view('bank-transfer.index', compact('transfers', 'account'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create bank transfer'))
        {
            $bankAccount = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $bankAccount->prepend(__('Select Bank'), '');

            return view('bank-transfer.create', compact('bankAccount'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create bank transfer'))
        {
            $validator = \Validator::make(
                $request->all(), [
                                   'from_account' => 'required|numeric',
                                   'to_account' => 'required|numeric',
                                   'amount' => 'required|numeric',
                                   'date' => 'required',
                                   'description' => 'required'
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $from_bank = BankAccount::where('id', $request->from_account)->where('created_by', \Auth::user()->creatorId())->first();
            if (isset($from_bank->opening_balance) && $from_bank->opening_balance < $request->amount) {
                return redirect()->back()->with('error', __('You cannot transfer more than the available balance in the from account.'));
            }

            $transfer                 = new BankTransfer();
            $transfer->from_account   = $request->from_account;
            $transfer->to_account     = $request->to_account;
            $transfer->amount         = $request->amount;
            $transfer->date           = $request->date;
            $transfer->payment_method = 0;
            $transfer->reference      = $request->reference;
            $transfer->description    = $request->description;
            $transfer->created_by     = \Auth::user()->creatorId();
            $transfer->save();

            Utility::bankAccountBalance($request->from_account, $request->amount, 'debit');

            Utility::bankAccountBalance($request->to_account, $request->amount, 'credit');

            return redirect()->route('bank-transfer.index')->with('success', __('Amount successfully transfer.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show()
    {
        return redirect()->route('bank-transfer.index');
    }

    public function edit(BankTransfer $transfer,$id)
    {
        if(\Auth::user()->can('edit bank transfer'))
        {
            $transfer = BankTransfer::where('id',$id)->first();
            $bankAccount = BankAccount::select('*', \DB::raw("CONCAT(bank_name,' ',holder_name) AS name"))->where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            $bankAccount->prepend(__('Select Bank'), '');
            
            return view('bank-transfer.edit', compact('bankAccount', 'transfer'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, BankTransfer $transfer,$id)
    {
        if(\Auth::user()->can('edit bank transfer'))
        {
            $transfer = BankTransfer::find($id);
            $validator = \Validator::make(
                $request->all(), [
                                   'from_account' => 'required|numeric',
                                   'to_account' => 'required|numeric',
                                   'amount' => 'required|numeric',
                                   'date' => 'required',
                                   'description' => 'required'
                               ]
            );
            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $from_bank = BankAccount::where('id', $request->from_account)->where('created_by', \Auth::user()->creatorId())->first();
            if (isset($from_bank->opening_balance) && $from_bank->opening_balance < $request->amount) {
                return redirect()->back()->with('error', __('You cannot transfer more than the available balance in the from account.'));
            }

            Utility::bankAccountBalance($transfer->from_account, $transfer->amount, 'credit');
            Utility::bankAccountBalance($transfer->to_account, $transfer->amount, 'debit');

            $transfer->from_account   = $request->from_account;
            $transfer->to_account     = $request->to_account;
            $transfer->amount         = $request->amount;
            $transfer->date           = $request->date;
            $transfer->payment_method = 0;
            $transfer->reference      = $request->reference;
            $transfer->description    = $request->description;
            $transfer->save();


            Utility::bankAccountBalance($request->from_account, $request->amount, 'debit');
            Utility::bankAccountBalance($request->to_account, $request->amount, 'credit');

            return redirect()->route('bank-transfer.index')->with('success', __('Amount successfully transfer updated.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy(BankTransfer $BankTransfer)
    {

        if(\Auth::user()->can('delete bank transfer'))
        {
            if($BankTransfer->created_by == \Auth::user()->creatorId())
            {
                $BankTransfer->delete();

                Utility::bankAccountBalance($BankTransfer->from_account, $BankTransfer->amount, 'credit');
                Utility::bankAccountBalance($BankTransfer->to_account, $BankTransfer->amount, 'debit');

                return redirect()->route('bank-transfer.index')->with('success', __('Amount transfer successfully deleted.'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
