<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Employee;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ComplaintController extends Controller
{
    public function index()
    {
        if(\Auth::user()->can('manage complaint'))
        {
            if(Auth::user()->type == 'Employee')
            {
                $emp        = Employee::where('user_id', '=', \Auth::user()->id)->first();
                $complaints = Complaint::where('complaint_from', '=', $emp->id)->with(['complaintFrom'])->get();
            }
            else
            {
                $complaints = Complaint::where('created_by', '=', \Auth::user()->creatorId())->with(['complaintFrom'])->get();
            }

            return view('complaint.index', compact('complaints'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create()
    {
        if(\Auth::user()->can('create complaint'))
        {
            if(Auth::user()->type == 'Employee')
            {
                $user             = \Auth::user();
                $current_employee = Employee::where('user_id', $user->id)->get()->pluck('name', 'id');
                $employees        = Employee::where('created_by', Auth::user()->creatorId())->where('user_id', '!=', $user->id)->get()->pluck('name', 'id');
            }
            else
            {
                $user             = \Auth::user();
                $current_employee = Employee::where('user_id', $user->id)->get()->pluck('name', 'id');
                $employees = Employee::where('created_by', Auth::user()->creatorId())->get()->pluck('name', 'id');
            }

            $employees->prepend(__('Select Employee'), '');


            return view('complaint.create', compact('employees', 'current_employee'));
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('create complaint'))
        {
            if(\Auth::user()->type != 'Employee')
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'complaint_from' => 'required',
                                   ]
                );
            }

            $validator = \Validator::make(
                $request->all(), [
                                   'complaint_against' => 'required',
                                   'title' => 'required',
                                   'complaint_date' => 'required',
                               ]
            );

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            if(\Auth::user()->type != 'Employee')
            {
                if($request->complaint_from == $request->complaint_against)
                {
                    return redirect()->back()->with('error', __('Complaint from and against cannot be the same employee.'));
                }
            }

            $complaint = new Complaint();
            if(\Auth::user()->type == 'Employee')
            {
                $emp                       = Employee::where('user_id', '=', \Auth::user()->id)->first();
                $complaint->complaint_from = $emp->id;
            }
            else
            {
                $complaint->complaint_from = $request->complaint_from;
            }
            $complaint->complaint_against = $request->complaint_against;
            $complaint->title             = $request->title;
            $complaint->complaint_date    = $request->complaint_date;
            $complaint->description       = $request->description;
            $complaint->created_by        = \Auth::user()->creatorId();
            $complaint->save();



            // Send Email
            $setings = Utility::settings();
            if($setings['complaint_resent'] == 1)
            {

                $employee         = Employee::find($complaint->complaint_against);
                $complaintArr = [

                    'complaint_name'=> $employee->name,
                    'complaint_title' => $complaint->title,
                    'complaint_against' =>  $complaint->complaint_against,
                    'complaint_date' => $complaint->complaint_date,
                    'complaint_description' => $complaint->description,

                ];


                $resp = Utility::sendEmailTemplate('complaint_resent', [$employee->id => $employee->email], $complaintArr);

                return redirect()->route('complaint.index')->with('success', __('Complaint  successfully created.') .(($resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));


            }

            return redirect()->route('complaint.index')->with('success', __('Complaint  successfully created.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function show(Complaint $complaint)
    {
        return redirect()->route('complaint.index');
    }

    public function edit($complaint)
    {
        $complaint = Complaint::find($complaint);
        if(\Auth::user()->can('edit complaint'))
        {
            if(Auth::user()->type == 'Employee')
            {
                $user             = \Auth::user();
                $current_employee = Employee::where('user_id', $user->id)->get()->pluck('name', 'id');
                $employees        = Employee::where('user_id', '!=', $user->id)->get()->pluck('name', 'id');
            }
            else
            {
                $user             = \Auth::user();
                $current_employee = Employee::where('user_id', $user->id)->get()->pluck('name', 'id');
                $employees = Employee::where('created_by', \Auth::user()->creatorId())->get()->pluck('name', 'id');
            }

            $employees->prepend(__('Select Employee'), '');
            
            if($complaint->created_by == \Auth::user()->creatorId())
            {
                return view('complaint.edit', compact('complaint', 'employees', 'current_employee'));
            }
            else
            {
                return response()->json(['error' => __('Permission denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission denied.')], 401);
        }
    }

    public function update(Request $request, Complaint $complaint)
    {
        if(\Auth::user()->can('edit complaint'))
        {
            if($complaint->created_by == \Auth::user()->creatorId())
            {
                if(\Auth::user()->type != 'Employee')
                {
                    $validator = \Validator::make(
                        $request->all(), [
                                           'complaint_from' => 'required',
                                       ]
                    );
                }

                $validator = \Validator::make(
                    $request->all(), [

                                       'complaint_against' => 'required',
                                       'title' => 'required',
                                       'complaint_date' => 'required',
                                   ]
                );

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->back()->with('error', $messages->first());
                }

                // Add validation to prevent same employee selection
                if(\Auth::user()->type != 'Employee')
                {
                    if($request->complaint_from == $request->complaint_against)
                    {
                        return redirect()->back()->with('error', __('Complaint from and against cannot be the same employee.'));
                    }
                }
                else
                {
                    $emp = Employee::where('user_id', '=', \Auth::user()->id)->first();
                    if($emp->id == $request->complaint_against)
                    {
                        return redirect()->back()->with('error', __('You cannot file a complaint against yourself.'));
                    }
                }

                if(\Auth::user()->type == 'Employee')
                {
                    $emp                       = Employee::where('user_id', '=', \Auth::user()->id)->first();
                    $complaint->complaint_from = $emp->id;
                }
                else
                {
                    $complaint->complaint_from = $request->complaint_from;
                }
                $complaint->complaint_against = $request->complaint_against;
                $complaint->title             = $request->title;
                $complaint->complaint_date    = $request->complaint_date;
                $complaint->description       = $request->description;
                $complaint->save();

                return redirect()->route('complaint.index')->with('success', __('Complaint successfully updated.'));
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

    public function destroy(Complaint $complaint)
    {
        if(\Auth::user()->can('delete complaint'))
        {
            if($complaint->created_by == \Auth::user()->creatorId())
            {
                $complaint->delete();

                return redirect()->route('complaint.index')->with('success', __('Complaint successfully deleted.'));
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
