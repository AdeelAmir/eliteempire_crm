<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Notification;
use App\Mail\FollowUpNotificationEmail;

class FollowUpNotification extends Command
{
    protected $signature = 'command:name';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Get all the leads
        $Leads = DB::table('leads')
            ->where('deleted_at', '=', null)
            ->select('leads.*')
            ->get();

        foreach($Leads as $lead)
        {
            $LeadId = $lead->id;
            $LeadFollowUp = $lead->appointment_time;

            $now = Carbon::now();
            $followuptime = new Carbon($LeadFollowUp);
            $diff_time = $now->diffInMinutes($followuptime);

            // Send follow up notification before 2 hours, 10 minutes and at the time of follow up
            if($LeadFollowUp != "" && ($followuptime >= $now))
            {
                if($diff_time == 120)
                {
                    $Message = "Lead #". $lead->lead_number ." follow up scheduled in 2 hours. Please be ready 😎!";
                    $FollowUpType = 1;
                    $this->SendFirstFollowUpNotification($LeadId, $Message, $FollowUpType);
                }
                if($diff_time == 10)
                {
                    $Message = "Lead #". $lead->lead_number ." follow up scheduled in 10 minutes. Please be ready 😎!";
                    $FollowUpType = 2;
                    $this->SendLeadStatusChangeNotification($LeadId, $Message, $FollowUpType);
                }
                if($diff_time == 0)
                {
                    $Message = "Lead #". $lead->lead_number ." have a follow up schedule in " . Carbon::parse($LeadFollowUp)->format('m/d/Y g:i a') . ". Please call now 📕!";
                    $FollowUpType = 3;
                    $this->SendLeadStatusChangeNotification($LeadId, $Message, $FollowUpType);
                }
            }
        }
    }

    function SendFirstFollowUpNotification($LeadId, $Message, $FollowUpType){

      $SenderId = 1;

      // First get lead state
      $lead_details = DB::table('leads')
          ->where('id', '=', $LeadId)
          ->get();

      $State = $lead_details[0]->state;

      // Get State Aquisition Manager
      $StateAqusitionManagerDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 3)
          ->where('profiles.state', '=', $State)
          ->select('users.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
          ->get();

      if ($StateAqusitionManagerDetails != "" && count($StateAqusitionManagerDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => $SenderId,
              'reciever_id' => $StateAqusitionManagerDetails[0]->id,
              'message' => $Message,
              'followup_type' => $FollowUpType,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }

          // Send Email
          $data = array(
            'Name' => $StateAqusitionManagerDetails[0]->firstname . " " . $StateAqusitionManagerDetails[0]->lastname,
            'Message' => $Message
          );

          $Email = $StateAqusitionManagerDetails[0]->email;
          // Mail::to($Email)->send(new FollowUpNotificationEmail($data));
      }

      // Get State Disposition Manager
      $StateDispositionManagerDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 4)
          ->where('profiles.state', '=', $State)
          ->select('users.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
          ->get();

      if ($StateDispositionManagerDetails != "" && count($StateDispositionManagerDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => $SenderId,
              'reciever_id' => $StateDispositionManagerDetails[0]->id,
              'message' => $Message,
              'followup_type' => $FollowUpType,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }

          // Send Email
          $data = array(
            'Name' => $StateDispositionManagerDetails[0]->firstname . " " . $StateDispositionManagerDetails[0]->lastname,
            'message' => $Message
          );

          $Email = $StateDispositionManagerDetails[0]->email;
          // Mail::to($Email)->send(new FollowUpNotificationEmail($data));
      }

      // Get Global Manager
      $GlobalManagerDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 2)
          ->select('users.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
          ->get();

      if ($GlobalManagerDetails != "" && count($GlobalManagerDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => $SenderId,
              'reciever_id' => $GlobalManagerDetails[0]->id,
              'message' => $Message,
              'followup_type' => $FollowUpType,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }

          // Send Email
          $data = array(
            'Name' => $GlobalManagerDetails[0]->firstname . " " . $GlobalManagerDetails[0]->lastname,
            'message' => $Message
          );

          $Email = $GlobalManagerDetails[0]->email;
          // Mail::to($Email)->send(new FollowUpNotificationEmail($data));
      }

      // Get Admin
      $AdminDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 1)
          ->select('users.*', 'profiles.firstname', 'profiles.middlename', 'profiles.lastname')
          ->get();

      if ($AdminDetails != "" && count($AdminDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => $SenderId,
              'reciever_id' => $AdminDetails[0]->id,
              'message' => $Message,
              'followup_type' => $FollowUpType,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }

          // Send Email
          $data = array(
            'Name' => $AdminDetails[0]->firstname . " " . $AdminDetails[0]->lastname,
            'message' => $Message
          );

          $Email = $AdminDetails[0]->email;
          Mail::to($Email)->send(new FollowUpNotificationEmail($data));
      }
    }

    function SendLeadStatusChangeNotification($LeadId, $Message, $FollowUpType) {

      $SenderId = 1;

      // First get lead state
      $lead_details = DB::table('leads')
          ->where('id', '=', $LeadId)
          ->get();

      $State = $lead_details[0]->state;

      // Get State Aquisition Manager
      $StateAqusitionManagerDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 3)
          ->where('profiles.state', '=', $State)
          ->select('users.id')
          ->get();

      if ($StateAqusitionManagerDetails != "" && count($StateAqusitionManagerDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => $SenderId,
              'reciever_id' => $StateAqusitionManagerDetails[0]->id,
              'message' => $Message,
              'followup_type' => $FollowUpType,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }
      }

      // Get State Disposition Manager
      $StateDispositionManagerDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 4)
          ->where('profiles.state', '=', $State)
          ->select('users.id')
          ->get();

      if ($StateDispositionManagerDetails != "" && count($StateDispositionManagerDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => $SenderId,
              'reciever_id' => $StateDispositionManagerDetails[0]->id,
              'message' => $Message,
              'followup_type' => $FollowUpType,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }
      }

      // Get Global Manager
      $GlobalManagerDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 2)
          ->select('users.id')
          ->get();

      if ($GlobalManagerDetails != "" && count($GlobalManagerDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => $SenderId,
              'reciever_id' => $GlobalManagerDetails[0]->id,
              'message' => $Message,
              'followup_type' => $FollowUpType,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }
      }

      // Get Admin
      $AdminDetails = DB::table('users')
          ->join('profiles', 'users.id', '=', 'profiles.user_id')
          ->where('users.deleted_at', '=', null)
          ->where('users.role_id', '=', 1)
          ->select('users.id')
          ->get();

      if ($AdminDetails != "" && count($AdminDetails) > 0) {
          DB::beginTransaction();
          $affected = Notification::create([
              'lead_id' => $LeadId,
              'sender_id' => $SenderId,
              'reciever_id' => $AdminDetails[0]->id,
              'message' => $Message,
              'followup_type' => $FollowUpType,
              'created_at' => Carbon::now()
          ]);
          if ($affected) {
            DB::commit();
          } else {
            DB::rollback();
          }
      }
    }
}
