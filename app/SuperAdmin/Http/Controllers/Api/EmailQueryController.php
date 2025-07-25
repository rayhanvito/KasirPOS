<?php

namespace App\SuperAdmin\Http\Controllers\Api;

use App\Classes\Notify;
use App\Http\Controllers\ApiBaseController;
use App\Notifications\MainNotificaiton;
use App\SuperAdmin\Http\Requests\Api\EmailQuery\IndexRequest;
use App\SuperAdmin\Http\Requests\Api\EmailQuery\SendMessageRequest;
use App\SuperAdmin\Models\EmailQuery;
use Examyou\RestAPI\ApiResponse;
use Examyou\RestAPI\Exceptions\ApiException;
use Illuminate\Support\Facades\DB;

class EmailQueryController extends ApiBaseController
{
    protected $model = EmailQuery::class;

    protected $indexRequest = IndexRequest::class;

    public function sendContactMessage(SendMessageRequest $request)
    {
        $id = $this->getIdFromHash($request->xid);
        $emailQuery = EmailQuery::find($id);

        $subject = $request->subject;
        $body = $request->body;

        $globalCompany = DB::table('companies')->where('is_global', 1)->first();
        $mailSetting = DB::table('settings')->where('setting_type', 'email')
            ->where('name_key', 'smtp')
            ->where('is_global', 1)
            ->where('company_id', $globalCompany->id)
            ->first();

        if ($mailSetting && $mailSetting->status && $mailSetting->verified) {
            $notficationData = [
                'mail' => [
                    'title' => $subject,
                    'content' => $body,
                    'setting' => $mailSetting,
                    'isAbleToSend' => 1,
                ],
            ];

            try {
                // Notifying to Warehouse
                $emailQuery->notify(new MainNotificaiton($notficationData));
                $emailQuery->replied = 1;
                $emailQuery->save();

                return ApiResponse::make('Success', []);
            } catch (\Exception $e) {
                throw new ApiException('Something went wrong... either check email settings or try again');
            }
        }

        throw new ApiException('Something went wrong... either check email settings or try again');
    }

}
