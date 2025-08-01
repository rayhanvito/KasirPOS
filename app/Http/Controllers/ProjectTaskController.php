<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\Utility;
use App\Models\TaskFile;
use App\Models\Bug;
use App\Models\BugStatus;
use App\Models\TaskStage;
use App\Models\ActivityLog;
use App\Models\ProjectTask;
use App\Models\TaskComment;
use App\Models\TaskChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProjectTaskController extends Controller
{

    public function index($project_id)
    {

        $usr = \Auth::user();
        if (\Auth::user()->can('manage project task')) {
            $project = Project::where('id', $project_id)->where('created_by', \Auth::user()->creatorId())->first();

            if ($project != null) {

                $stages = TaskStage::orderBy('order')->where('created_by', \Auth::user()->creatorId())->get();
                foreach ($stages as $status) {
                    $stageClass[] = 'task-list-' . $status->id;
                    $task = ProjectTask::where('project_id', '=', $project_id);
                    // check project is shared or owner

                    //end
                    $task->orderBy('order');
                    $status['tasks'] = $task->where('stage_id', '=', $status->id)->get();
                }

                return view('project_task.index', compact('stages', 'stageClass', 'project'));
            } else {
                return redirect()->route('projects.index')->with('error', __('Projeat not found'));
            }

        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create($project_id)
    {
        if (\Auth::user()->can('create project task')) {
            $project = Project::find($project_id);
            $hrs = Project::projectHrs($project_id);
            $settings = Utility::settings();
            $stages = TaskStage::orderBy('order')->where('created_by', \Auth::user()->creatorId())->get()->pluck('name','id');
            $stages->prepend(__('Select task stage'), '');

            return view('project_task.create', compact('project_id', 'stages', 'project', 'hrs', 'settings'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function store(Request $request, $project_id)
    {
        if (\Auth::user()->can('create project task')) {
            $validator = Validator::make(
                $request->all(), [
                    'name' => 'required',
                    'estimated_hrs' => 'required',
                    'priority' => 'required',
                    'stage_id' => 'required',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', Utility::errorFormat($validator->getMessageBag()));
            }

            $usr = Auth::user();
            $project = Project::find($project_id);
            $last_stage = $project->first()->id;
            $post = $request->all();
            $post['project_id'] = $project->id;
            $post['stage_id'] = $request->stage_id;
            $post['assign_to'] = $request->assign_to;
            $post['created_by'] = \Auth::user()->creatorId();
            $post['start_date'] = date("Y-m-d H:i:s", strtotime($request->start_date));
            $post['end_date'] = isset($request->end_date) ? date("Y-m-d H:i:s", strtotime($request->end_date)) : null;

            if ($request->stage_id == $last_stage) {
                $post['marked_at'] = date('Y-m-d');
            }
            $task = ProjectTask::create($post);

            //Make entry in activity log
            ActivityLog::create(
                [
                    'user_id' => $usr->id,
                    'project_id' => $project_id,
                    'task_id' => $task->id,
                    'log_type' => 'Create Task',
                    'remark' => json_encode(['title' => $task->name]),
                ]
            );



            //For Notification
            $setting = Utility::settings(\Auth::user()->creatorId());
            $project_name = Project::find($project_id);
            $project = Project::where('id', $project_name->id)->first();

            $users = explode(',',$task->assign_to);

            if(isset($setting['new_task']) && $setting['new_task'] ==1)
            {
                foreach ($users as $key => $user) {
                    $user = User::find($user);
                    if(!empty($user)){
                        $taskArr = [
                            'task_user' => $user->name,
                            'task_name' => $task->name,
                            'project_name' => $project->project_name,
                            'task_start_date' => $task->start_date,
                            'task_end_date' => $task->end_date,
                            'hours' => $task->estimated_hrs,
                        ];
                        $resp = Utility::sendEmailTemplate('new_task', [$user->id => $user->email], $taskArr);
                    }
                }
            }



            $taskNotificationArr = [
                'task_name' => $task->name,
                'project_name' => $project->project_name,
                'user_name' => \Auth::user()->name,
            ];
            //Slack Notification
            if (isset($setting['task_notification']) && $setting['task_notification'] == 1) {
                Utility::send_slack_msg('new_task', $taskNotificationArr);
            }
            //Telegram Notification
            if (isset($setting['telegram_task_notification']) && $setting['telegram_task_notification'] == 1) {
                Utility::send_telegram_msg('new_task', $taskNotificationArr);
            }


            //For Google Calendar
            if ($request->get('synchronize_type') == 'google_calender') {
                $type = 'task';
                $request1 = new ProjectTask();
                $request1->title = $request->name;
                $request1->start_date = $request->start_date;
                $request1->end_date = $request->end_date;
                Utility::addCalendarData($request1, $type);
            }

            //webhook
            $module = 'New Task';
            $webhook = Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($task);
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                if ($status == true) {
                    return redirect()->back()->with('success', __('Task added successfully.'));
                } else {
                    return redirect()->back()->with('error', __('Task added successfully, Webhook call failed.'));
                }
            }

            return redirect()->back()->with('success', __('Task added successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // For Taskboard View
    public function taskBoard($view)
    {
        if ($view == 'list')
        {
            return view('project_task.taskboard', compact('view'));
        }
        else
        {
            $usr = Auth::user();
            if (\Auth::user()->type == 'client')
            {
                $user_projects = Project::where('client_id', \Auth::user()->id)->pluck('id', 'id')->toArray();
            }
            elseif (\Auth::user()->type != 'client')
            {
                $user_projects = $usr->projects()->pluck('project_id', 'project_id')->toArray();
            }

            $tasks = ProjectTask::whereIn('project_id', $user_projects);
            if (\Auth::user()->type != 'company')
            {
                if (\Auth::user()->type == 'client')
                {
                    $tasks->where('created_by', \Auth::user()->creatorId());

                }
                else
                {
                    $tasks->whereRaw("find_in_set('" . $usr->id . "',assign_to)");
                }
            }
            else
            {
                $tasks->where('created_by', \Auth::user()->creatorId());
            }

            $tasks = $tasks->get();
            return view('project_task.grid', compact('tasks', 'view'));

        }

        return redirect()->back()->with('error', __('Permission Denied.'));

    }


    // For Load Task using ajax
    public function taskboardView(Request $request)
    {

        $usr = Auth::user();
        if (\Auth::user()->type == 'client') {
            $user_projects = Project::where('client_id', \Auth::user()->id)->pluck('id', 'id')->toArray();
        } elseif (\Auth::user()->type != 'client') {
            $user_projects = $usr->projects()->pluck('project_id', 'project_id')->toArray();
        }
        if ($request->ajax() && $request->has('view') && $request->has('sort')) {
            $sort = explode('-', $request->sort);
            $tasks = ProjectTask::whereIn('project_id', $user_projects)->orderBy($sort[0], $sort[1]);
            if (\Auth::user()->type != 'company') {
                if (\Auth::user()->type == 'client') {
                    $tasks->where('created_by', \Auth::user()->creatorId());

                } else {
                    $tasks->whereRaw("find_in_set('" . $usr->id . "',assign_to)");
                }
            } else {
                $tasks->where('created_by', \Auth::user()->creatorId());
            }
            if (!empty($request->keyword)) {
                $tasks->where('name', 'LIKE', $request->keyword . '%');
            }
            if (!empty($request->status)) {
                $todaydate = date('Y-m-d');

                // For Optimization
                $status = $request->status;
                foreach ($status as $k => $v) {
                    if ($v == 'due_today' || $v == 'over_due' || $v == 'starred' || $v == 'see_my_tasks') {
                        unset($status[$k]);
                    }
                }
                // end

                if (count($status) > 0) {
                    $tasks->whereIn('priority', $status);
                }



                if (in_array('due_today', $request->status)) {
                    $tasks->where('end_date', $todaydate);
                }

                if (in_array('over_due', $request->status)) {
                    $tasks->where('end_date', '<', $todaydate);
                }

                if (in_array('starred', $request->status)) {
                    $tasks->where('is_favourite', '=', 1);
                }
            }

            $tasks = $tasks->with(['project'])->get();
            $view=$request->view;
            $returnHTML = view('project_task.' . $request->view, compact('tasks','view'))->render();

            return response()->json(
                [
                    'success' => true,
                    'html' => $returnHTML,
                ]
            );
        }
    }


    // For Taskboard View
    public function allBugList($view)
    {
        $bugStatus = BugStatus::where('created_by', \Auth::user()->creatorId())->get();
        if (Auth::user()->type == 'company') {
            $bugs = Bug::where('created_by', \Auth::user()->creatorId())->with(['project','createdBy' , 'projectBUg'])->get();
        } elseif (Auth::user()->type != 'company') {
            if (\Auth::user()->type == 'client') {
                $user_projects = Project::where('client_id', \Auth::user()->id)->pluck('id', 'id')->toArray();
                $bugs = Bug::whereIn('project_id', $user_projects)->where('created_by', \Auth::user()->creatorId())->with(['project','createdBy'])->get();
            } else {
                $bugs = Bug::where('created_by', \Auth::user()->creatorId())->whereRaw("find_in_set('" . \Auth::user()->id . "',assign_to)")->with(['project','createdBy'])->get();
            }
        }
        if ($view == 'list') {
            return view('projects.allBugListView', compact('bugs', 'bugStatus', 'view'));
        } else {
            return view('projects.allBugGridView', compact('bugs', 'bugStatus', 'view'));
        }
        return redirect()->back()->with('error', __('Permission Denied.'));
    }

    public function show($project_id, $task_id)
    {

        if (\Auth::user()->can('view project task')) {
            $allow_progress = Project::find($project_id)->task_progress;
            $task = ProjectTask::find($task_id);

            return view('project_task.view', compact('task', 'allow_progress'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function edit($project_id, $task_id)
    {
        if (\Auth::user()->can('edit project task')) {
            $project = Project::find($project_id);
            $task = ProjectTask::find($task_id);
            $hrs = Project::projectHrs($project_id);
            $stages = TaskStage::orderBy('order')->where('created_by', \Auth::user()->creatorId())->get()->pluck('name','id');
            $stages->prepend(__('Select task stage'), '');

            return view('project_task.edit', compact('project', 'task', 'hrs', 'stages'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function update(Request $request, $project_id, $task_id)
    {

        if (\Auth::user()->can('edit project task')) {
            $validator = Validator::make(
                $request->all(), [
                    'name' => 'required',
                    'estimated_hrs' => 'required',
                    'priority' => 'required',
                    'stage_id' => 'required',
                ]
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', Utility::errorFormat($validator->getMessageBag()));
            }

            $post = $request->all();
            $task = ProjectTask::find($task_id);
            $task->update($post);

            return redirect()->back()->with('success', __('Task Updated successfully.'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy($project_id, $task_id)
    {

        if (\Auth::user()->can('delete project task')) {
            ProjectTask::deleteTask([$task_id]);

            return redirect()->back()->with('success', __('Task Deleted successfully.'));

            echo json_encode(['task_id' => $task_id]);
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function getStageTasks(Request $request)
    {
        if (\Auth::user()->can('view project task')) {
            $stage_id = $request->stage_id;
            $project_id = $request->project_id;
            
            $count = ProjectTask::where('stage_id', $stage_id);
            if ($project_id) {
                $count = $count->where('project_id', $project_id);
            }
            $count = $count->count();
            
            return response()->json($count);
        } else {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function changeCom($projectID, $taskId)
    {

        if (\Auth::user()->can('view project task')) {
            $project = Project::find($projectID);
            $task = ProjectTask::find($taskId);

            if ($task->is_complete == 0) {
                $last_stage = TaskStage::orderBy('order', 'DESC')->where('created_by', \Auth::user()->creatorId())->first();
                $task->is_complete = 1;
                $task->marked_at = date('Y-m-d');
                $task->stage_id = $last_stage->id;
            } else {
                $first_stage = TaskStage::orderBy('order', 'ASC')->where('created_by', \Auth::user()->creatorId())->first();
                $task->is_complete = 0;
                $task->marked_at = NULL;
                $task->stage_id = $first_stage->id;
            }

            $task->save();

            return [
                'com' => $task->is_complete,
                'task' => $task->id,
                'stage' => $task->stage_id,
            ];
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function changeFav($projectID, $taskId)
    {
        if (\Auth::user()->can('view project task')) {
            $task = ProjectTask::find($taskId);
            if ($task->is_favourite == 0) {
                $task->is_favourite = 1;
            } else {
                $task->is_favourite = 0;
            }

            $task->save();

            return [
                'fav' => $task->is_favourite,
            ];
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function changeProg(Request $request, $projectID, $taskId)
    {
        if (\Auth::user()->can('view project task')) {
            $task = ProjectTask::find($taskId);
            $task->progress = $request->progress;
            $task->save();

            return ['task_id' => $taskId];
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function checklistStore(Request $request, $projectID, $taskID)
    {

        if (\Auth::user()->can('view project task')) {
            $request->validate(
                ['name' => 'required']
            );

            $post = [];
            $post['name'] = $request->name;
            $post['task_id'] = $taskID;
            $post['user_type'] = 'User';
            $post['created_by'] = \Auth::user()->id;
            $post['status'] = 0;

            $checkList = TaskChecklist::create($post);
            $user = $checkList->user;
            $checkList->updateUrl = route(
                'checklist.update', [
                    $projectID,
                    $checkList->id,
                ]
            );
            $checkList->deleteUrl = route(
                'checklist.destroy', [
                    $projectID,
                    $checkList->id,
                ]
            );

            return $checkList->toJson();
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function checklistUpdate($projectID, $checklistID)
    {

        if (\Auth::user()->can('view project task')) {
            $checkList = TaskChecklist::find($checklistID);
            if ($checkList->status == 0) {
                $checkList->status = 1;
            } else {
                $checkList->status = 0;
            }
            $checkList->save();

            return $checkList->toJson();
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function checklistDestroy($projectID, $checklistID)
    {
        if (\Auth::user()->can('view project task')) {
            $checkList = TaskChecklist::find($checklistID);
            $checkList->delete();

            return "true";
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function commentStoreFile(Request $request, $projectID, $taskID)
    {

        if (\Auth::user()->can('view project task')) {
            $request->validate(
                ['file' => 'required']
            );
            if ($request->hasFile('file')) {
                $filenameWithExt = $request->file('file')->getClientOriginalName();
                $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension = $request->file('file')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;

                $settings = Utility::getStorageSetting();
                if ($settings['storage_setting'] == 'local') {
                    $dir = 'uploads/tasks/';
                } else {
                    $dir = 'uploads/tasks';
                }


                $url = '';
                $path = Utility::upload_file($request, 'file', $fileNameToStore, $dir, []);
                if ($path['flag'] == 1) {
                    $url = $path['url'];
                } else {
                    return redirect()->route('file', \Auth::user()->id)->with('error', __($path['msg']));
                }
            }

            $post['task_id'] = $taskID;
            $post['file'] = $request->hasFile('file') ? $fileNameToStore : '';
            $post['name'] = $request->file->getClientOriginalName();
            $post['extension'] = $request->file->getClientOriginalExtension();
            // $post['file_size'] = round(($request->file->getSize() / 1024) / 1024, 2) . ' MB';
            $post['created_by'] = \Auth::user()->id;
            $post['user_type'] = 'User';
            $TaskFile = TaskFile::create($post);
            $user = $TaskFile->user;
            $TaskFile->deleteUrl = '';
            $TaskFile->deleteUrl = route(
                'comment.destroy.file', [
                    $projectID,
                    $taskID,
                    $TaskFile->id,
                ]
            );

            return $TaskFile->toJson();
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function commentDestroyFile(Request $request, $projectID, $taskID, $fileID)
    {
        if (\Auth::user()->can('view project task')) {
            $commentFile = TaskFile::find($fileID);
            $path = storage_path('tasks/' . $commentFile->file);
            if (file_exists($path)) {
                \File::delete($path);
            }
            $commentFile->delete();

            return "true";
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function commentDestroy(Request $request, $projectID, $taskID, $commentID)
    {

        if (\Auth::user()->can('view project task')) {
            $comment = TaskComment::find($commentID);
            $comment->delete();

            return "true";
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function commentStore(Request $request, $projectID, $taskID)
    {

        if (\Auth::user()->can('view project task')) {
            $post = [];
            $post['task_id'] = $taskID;
            $post['user_id'] = \Auth::user()->id;
            $post['comment'] = $request->comment;
            $post['created_by'] = \Auth::user()->creatorId();
            $post['user_type'] = \Auth::user()->type;

            $comment = TaskComment::create($post);
            $user = $comment->user;
            $user_detail = $comment->userdetail;

            $comment->deleteUrl = route(
                'comment.destroy', [
                    $projectID,
                    $taskID,
                    $comment->id,
                ]
            );

            //For Notification
            $setting = Utility::settings(\Auth::user()->creatorId());
            $commentOfTask = ProjectTask::find($taskID);
            $project = Project::find($projectID);
            $CommentNotificationArr = [
                'task_name' => $commentOfTask->name,
                'project_name' => $project->project_name,
                'user_name' => \Auth::user()->name,
            ];
            //Slack Notification
            if (isset($setting['taskcomment_notification']) && $setting['taskcomment_notification'] == 1) {
                Utility::send_slack_msg('new_task_comment', $CommentNotificationArr);
            }

            //Telegram Notification
            if (isset($setting['telegram_taskcomment_notification']) && $setting['telegram_taskcomment_notification'] == 1) {
                Utility::send_telegram_msg('new_task_comment', $CommentNotificationArr);

            }


            $comment->current_time = $comment->created_at->diffForHumans();
            $comment->default_img = asset(\Storage::url("uploads/avatar/".\Auth::user()->avatar.""));

            //webhook
            $module = 'New Task Comment';
            $webhook = Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($comment);
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);

                if ($status == true) {
                    return redirect()->back()->with('success', __('Comment added successfully.'));
                } else {
                    return redirect()->back()->with('error', __('Comment added successfully, Webhook call failed.'));
                }
            }
            return $comment->toJson();
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function updateTaskPriorityColor(Request $request)
    {
        if (\Auth::user()->can('view project task')) {
            $task_id = $request->input('task_id');
            $color = $request->input('color');

            $task = ProjectTask::find($task_id);

            if ($task && $color) {
                $task->priority_color = $color;
                $task->save();
            }
            echo json_encode(true);
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function taskOrderUpdate(Request $request, $project_id)
    {

        if (\Auth::user()->can('view project task')) {

            $user = \Auth::user();
            $project = Project::find($project_id);
            // Save data as per order

            if (isset($request->sort)) {
                foreach ($request->sort as $index => $taskID) {
                    if (!empty($taskID)) {
                        echo $index . "-" . $taskID;
                        $task = ProjectTask::find($taskID);

                        $task->order = $index;
                        $task->save();

                    }
                }
            }

            // Update Task Stage
            if ($request->new_stage != $request->old_stage) {

                $new_stage = TaskStage::find($request->new_stage);
                $old_stage = TaskStage::find($request->old_stage);
                $last_stage = TaskStage::where('created_by', \Auth::user()->creatorId())->orderBy('order', 'DESC')->first();
                $last_stage = $last_stage->id;

                $task = ProjectTask::find($request->id);

                $task->stage_id = $request->new_stage;

                if ($request->new_stage == $last_stage) {
                    $task->is_complete = 1;
                    $task->marked_at = date('Y-m-d');
                } else {
                    $task->is_complete = 0;
                    $task->marked_at = NULL;
                }
                $task->save();

                //For Notification
                $setting = Utility::settings(\Auth::user()->creatorId());
                $old_stage = TaskStage::find($request->old_stage);
                $new_stage = TaskStage::find($request->new_stage);
                $task = ProjectTask::find($request->id);
                $users = isset($task->assign_to) ? explode(',',$task->assign_to) : [];

                if(isset($setting['task_stage_updated']) && $setting['task_stage_updated'] ==1)
                {
                    foreach ($users as $key => $user) {
                        $user = User::find($user);
                        $projectArr = [
                            'task_user' => $user->name,
                            'task_name' => $task->name,
                            'old_stage_name' => $old_stage->name,
                            'new_stage_name' => $new_stage->name,
                        ];
                        $resp = Utility::sendEmailTemplate('task_stage_updated', [$user->id => $user->email], $projectArr);
                    }
                }

                $projectTaskNotificationArr = [
                    'task_name' => $task->name,
                    'old_stage_name' => $old_stage->name,
                    'new_stage_name' => $new_stage->name,
                ];
                //Slack Notification
                if (isset($setting['taskmove_notification']) && $setting['taskmove_notification'] == 1) {
                    Utility::send_slack_msg('task_stage_updated', $projectTaskNotificationArr);
                }
                //Telegram Notification
                if (isset($setting['telegram_taskmove_notification']) && $setting['telegram_taskmove_notification'] == 1) {
                    Utility::send_telegram_msg('task_stage_updated', $projectTaskNotificationArr);
                }

                //webhook
                $module = 'Task Stage Updated';
                $webhook = Utility::webhookSetting($module);
                if ($webhook) {
                    $parameter = json_encode($task);
                    $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                    if ($status == true) {
                        return redirect()->back()->with('success', __('Task successfully updated!'));
                    } else {
                        return redirect()->back()->with('error', __('Task successfully updated, Webhook call failed.'));
                    }
                }


                // Make Entry in activity log
                ActivityLog::create(
                    [
                        'user_id' => $user->id,
                        'project_id' => $project_id,
                        'task_id' => $request->id,
                        'log_type' => 'Move Task',
                        'remark' => json_encode(
                            [
                                'title' => $task->name,
                                'old_stage' => $old_stage->name,
                                'new_stage' => $new_stage->name,
                            ]
                        ),
                    ]

                );

                return $task->toJson();
            }
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function taskGet($task_id)
    {
        if (\Auth::user()->can('view project task')) {
            $task = ProjectTask::find($task_id);

            $html = '';
            $html .= '<div class="card-body"><div class="row align-items-center mb-2">';
            $html .= '<div class="col-6">';
            $html .= '<span class="badge badge-pill badge-xs badge-' . ProjectTask::$priority_color[$task->priority] . '">' . ProjectTask::$priority[$task->priority] . '</span>';
            $html .= '</div>';
            $html .= '<div class="col-6 text-end">';
            if (\Auth::user()->can('view project task') || \Auth::user()->can('edit project task') || \Auth::user()->can('delete project task')) {
                $html .= '<div class="dropdown action-item">
                                                            <a href="#" class="action-item" data-toggle="dropdown"><i class="ti ti-ellipsis-h"></i></a>
                                                            <div class="dropdown-menu dropdown-menu-right">';
                if (\Auth::user()->can('view project task')) {
                    $html .= '<a href="#" data-url="' . route(
                            'projects.tasks.show', [
                                $task->project_id,
                                $task->id,
                            ]
                        ) . '" data-ajax-popup="true" class="dropdown-item">' . __('View') . '</a>';
                }
                if (\Auth::user()->can('edit project task')) {
                    $html .= '<a href="#" data-url="' . route(
                            "projects.tasks.edit", [
                                $task->project_id,
                                $task->id,
                            ]
                        ) . '" data-ajax-popup="true" data-size="lg" data-title="' . __("Edit ") . $task->name . '" class="dropdown-item">' . __('Edit') . '</a>';
                }
                if (\Auth::user()->can('delete project task')) {
                    $html .= '<a href="#" class="dropdown-item del_task" data-url="' . route(
                            'projects.tasks.destroy', [
                                $task->project_id,
                                $task->id,
                            ]
                        ) . '">' . __('Delete') . '</a>';
                }
                $html .= '                                 </div>
                                                        </div>
                                                    </div>';
                $html .= '</div>';
            }
            $html .= '<a class="h6" href="#" data-url="' . route(
                    "projects.tasks.show", [
                        $task->project_id,
                        $task->id,
                    ]
                ) . '" data-ajax-popup="true">' . $task->name . '</a>';
            $html .= '<div class="row align-items-center">';
            $html .= '<div class="col-12">';
            $html .= '<div class="actions d-inline-block">';
            if (count($task->taskFiles) > 0) {
                $html .= '<div class="action-item mr-2"><i class="ti ti-file text-primary mr-2"></i>' . count($task->taskFiles) . '</div>';
            }
            if (count($task->comments) > 0) {
                $html .= '<div class="action-item mr-2"><i class="ti ti-message text-primary mr-2"></i>' . count($task->comments) . '</div>';
            }
            if ($task->checklist->count() > 0) {
                $html .= '<div class="action-item mr-2"><i class="ti ti-list text-primary mr-2"></i>' . $task->countTaskChecklist() . '</div>';
            }
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="col-5">';
            if (!empty($task->end_date) && $task->end_date != '0000-00-00') {
                $clr = (strtotime($task->end_date) < time()) ? 'text-danger' : '';
                $html .= '<small class="' . $clr . '">' . date("d M Y", strtotime($task->end_date)) . '</small>';
            }
            $html .= '</div>';
            $html .= '<div class="col-7 text-end">';

            if ($users = $task->users()) {
                $html .= '<div class="avatar-group">';
                foreach ($users as $key => $user) {
                    if ($key < 3) {
                        $html .= ' <a href="#" class="avatar rounded-circle avatar-sm">';
                        $html .= '<img class="hweb" src="' . $user->getImgImageAttribute() . '" title="' . $user->name . '">';
                        $html .= '</a>';
                    }
                }

                if (count($users) > 3) {
                    $html .= '<a href="#" class="avatar rounded-circle avatar-sm"><img avatar="';
                    $html .= count($users) - 3;
                    $html .= '"></a>';
                }
                $html .= '</div>';
            }
            $html .= '</div></div></div>';

            print_r($html);
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function getDefaultTaskInfo(Request $request, $task_id)
    {

        if (\Auth::check()) {
            if (\Auth::user()->can('view project task')) {
                $response = [];
                $task = ProjectTask::find($task_id);
                if ($task) {
                    $response['task_name'] = $task->name;
                    $response['task_due_date'] = $task->due_date;
                }

                return json_encode($response);
            } else {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        } else {
            $response = [];
            $task = ProjectTask::find($task_id);
            if ($task) {
                $response['task_name'] = $task->name;
                $response['task_due_date'] = $task->due_date;
            }

            return json_encode($response);
        }


    }

    // Calendar View
    public function calendarView($task_by, $project_id = NULL)
    {
        $usr = Auth::user();
        $transdate = date('Y-m-d', time());

        if ($usr->type != 'admin') {
            if (\Auth::user()->type == 'client') {
                $user_projects = Project::where('client_id', \Auth::user()->id)->pluck('id', 'id')->toArray();
            } else {
                $user_projects = $usr->projects()->pluck('project_id', 'project_id')->toArray();
            }
            $user_projects = (!empty($project_id) && $project_id > 0) ? [$project_id] : $user_projects;

            if (\Auth::user()->type == 'company') {
                $tasks = ProjectTask::whereIn('project_id', $user_projects);
            } elseif (\Auth::user()->type != 'company') {
                if (\Auth::user()->type == 'client') {

                    $tasks = ProjectTask::whereIn('project_id', $user_projects);
                } else {
                    $tasks = ProjectTask::whereIn('project_id', $user_projects)->whereRaw("find_in_set('" . \Auth::user()->id . "',assign_to)");
                }
            }
            if (\Auth::user()->type == 'client') {
                if ($task_by == 'all') {
                    $tasks->where('created_by', \Auth::user()->creatorId());
                }
            } else {
                if ($task_by == 'my') {
                    $tasks->whereRaw("find_in_set('" . $usr->id . "',assign_to)");
                }
            }
            $tasks = $tasks->get();
            $arrTasks = [];

            foreach ($tasks as $task) {
                $arTasks = [];
                if ((!empty($task->start_date) && $task->start_date != '0000-00-00') || !empty($task->end_date) && $task->end_date != '0000-00-00') {
                    $arTasks['id'] = $task->id;
                    $arTasks['title'] = $task->name;

                    if (!empty($task->start_date) && $task->start_date != '0000-00-00') {
                        $arTasks['start'] = $task->start_date;
                    } elseif (!empty($task->end_date) && $task->end_date != '0000-00-00') {
                        $arTasks['start'] = $task->end_date;
                    }
                    if (!empty($task->end_date) && $task->end_date != '0000-00-00') {
                        $arTasks['end'] = $task->end_date;
                    } elseif (!empty($task->start_date) && $task->start_date != '0000-00-00') {
                        $arTasks['end'] = $task->start_date;
                    }
                    $arTasks['allDay'] = !0;
                    $arTasks['className'] = 'event-' . ProjectTask::$priority_color[$task->priority];
                    $arTasks['description'] = $task->description;
                    $arTasks['url'] = route('task.calendar.show', $task->id);
                    $arTasks['resize_url'] = route('task.calendar.drag', $task->id);
                    $arrTasks[] = $arTasks;


                }
            }

            return view('tasks.calendar', compact('arrTasks', 'project_id', 'task_by', 'transdate'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    // Calendar Show
    public function calendarShow($id)
    {
        $task = ProjectTask::find($id);

        return view('tasks.calendar_show', compact('task'));
    }

    // Calendar Drag
    public function calendarDrag(Request $request, $id)
    {
        $task = ProjectTask::find($id);
        $task->start_date = $request->start;
        $task->end_date = $request->end;
        $task->save();
    }

    //for Google Calendar
    public function get_task_data(Request $request)
    {
        if ($request->get('calender_type') == 'goggle_calender') {
            $type = 'task';
            $arrayJson = Utility::getCalendarData($type);
        } else {
            if (Auth::user()->type == 'client') {
                $user_projects = Project::where('client_id', \Auth::user()->id)->pluck('id', 'id')->toArray();
                $data = ProjectTask::whereIn('project_id', $user_projects)->get();
            } else {
                if (Auth::user()->type == 'company') {
                    $data = ProjectTask::where('created_by', \Auth::user()->creatorId())->get();
                } else {
                    $usr = Auth::user();
                    $user_projects = $usr->projects()->pluck('project_id', 'project_id')->toArray();
                    $data = ProjectTask::whereIn('project_id', $user_projects)
                        ->where('created_by', \Auth::user()->creatorId())
                        ->whereRaw("find_in_set('" . \Auth::user()->id . "',assign_to)")->get();
                }

            }

            $arrayJson = [];
            foreach ($data as $val) {
                $end_date = date_create($val->end_date);
                date_add($end_date, date_interval_create_from_date_string("1 days"));
                $arrayJson[] = [
                    "id" => $val->id,
                    "title" => $val->name,
                    "start" => $val->start_date,
                    "end" => date_format($end_date, "Y-m-d H:i:s"),
                    "className" => 'event-primary',
                    "textColor" => '#51459d',
                    "allDay" => true,
                    'url' => route('task.calendar.show', $val->id),
                    'resize_url' => route('task.calendar.drag', $val->id),
                ];
            }
        }

        return $arrayJson;
    }
}
