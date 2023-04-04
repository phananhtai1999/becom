<?php

namespace Database\Seeders;

use App\Models\ActivityHistory;
use App\Models\Campaign;
use App\Models\Contact;
use App\Models\MailSendingHistory;
use App\Models\Note;
use App\Models\Remind;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityHistorySeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('email', 'user1@sendemail.techupcorp')->first();
        $contact = Contact::where('user_uuid', $user->uuid)->first();
        $note = Note::where([
            ['user_uuid', $user->uuid],
            ['contact_uuid', $contact->uuid],
        ])->first();
        $remind = Remind::where('user_uuid', $user->uuid)->first();
        $campaign = Campaign::where([
            ['user_uuid', $user->uuid],
            ['send_type', 'email'],
        ])->first();
        $mailSendingHistory = MailSendingHistory::factory()->create([
            'campaign_uuid' => $campaign->uuid,
            'status' => 'sent',
        ]);

        $activityHistories = [
            [
                'type' => 'note',
                'type_id' => $note->uuid,
                'content' => ['langkey' => 'created' . '.' . 'note', 'type' => Note::NOTE_TYPE, 'contact' => $contact->email, 'date' => $note->created_at],
                'date' => $note->created_at,
                'contact_uuid' => $contact->uuid,
            ],
            [
                'type' => 'remind',
                'type_id' => $remind->uuid,
                'content' => ['langkey' => 'created' . '.' . 'remind', 'type' => Remind::REMIND_TYPE, 'contact' => $contact->email, 'date' => $remind->created_at],
                'date' => $remind->created_at,
                'contact_uuid' => $contact->uuid,
            ],
            [
                'type' => $campaign->send_type,
                'type_id' => $mailSendingHistory->uuid,
                'content' => ['langkey' => $mailSendingHistory->status === 'sent' ? 'sent.success' : 'sent.failed', 'send_type' => $mailSendingHistory->campaign->send_type === 'email' ? 'email' : 'messages', 'email' => $mailSendingHistory->email, 'status' => $mailSendingHistory->status === 'sent' ? 'success' : 'failed', 'date' => $mailSendingHistory->time],
                'date' => $mailSendingHistory->time,
                'contact_uuid' => $contact->uuid,
            ]
        ];

        foreach ($activityHistories as $activityHistory) {
            ActivityHistory::firstOrCreate([
                'contact_uuid' => $contact->uuid,
                'type' => $activityHistory['type'],
            ], $activityHistory);
        }
    }
}
