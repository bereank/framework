<?php

namespace Leysco100\Shared\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Leysco100\Shared\Models\FormSetting\Models\FM100;
use Leysco100\Shared\Models\Administration\Jobs\CreateMenuForUser;

class FormSettingUpdate implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $AllIds;
    protected $SelectedIds;
    protected $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($AllIds, $SelectedIds, $user_id)
    {
        $this->AllIds = $AllIds;
        $this->SelectedIds = $SelectedIds;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $userfm100 = FM100::where('UserSign', $this->user_id)
                ->first();
        if(!$userfm100){
            CreateMenuForUser::dispatch($this->user_id);
        }
         FM100::where('UserSign', $this->user_id)
                ->update([
                    'Visible' => 'N',
                ]);

        foreach ($this->SelectedIds as $key => $value) {
            $fm100 = FM100::where('id', $value)
                ->where('UserSign', $this->user_id)
                ->first();
            $details = [
                'Visible' => 'Y',
            ];
            if ($fm100){
                $fm100->update($details);
                if ($fm100->ParentID != null) {
                    $this->updateParent($fm100->ParentID);
                }
            }
        }
    }

    public function updateParent($formID)
    {
        $fm100 = FM100::where('id', $formID)
            ->where('UserSign', $this->user_id)
            ->first();

        $details = [
            'Visible' => 'Y',
        ];

        $fm100->update($details);

        if ($fm100->ParentID != null) {
            $this->updateParent($fm100->ParentID);
        }
    }
}
