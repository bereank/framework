<?php

namespace Leysco100\Shared\Models\Administration\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Leysco100\Shared\Models\FormSetting\Models\FM100;

class CreateMenuForUser implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    protected $user_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $menuJsonString = file_get_contents(base_path('resources/testfiles/formsettings/menu_items.json'));
        $menuitems = json_decode($menuJsonString, true);
        foreach ($menuitems as $key => $item1) {
            $menu1 = FM100::create([
                'UserSign' => $this->user_id,
                'Label' => $item1['Label'],
                'Visible' => "N",
                'icon' => array_key_exists('icon', $item1) ? $item1['icon'] : null,
                'link' => array_key_exists('link', $item1) ? $item1['link'] : null,
            ]);

            $IsArray1 = is_array($item1['children']) ? 'Yes' : 'No';
            if ($IsArray1 == "Yes") {
                foreach ($item1['children'] as $key => $item2) {
                    $menu2 = FM100::create([
                        'UserSign' => $this->user_id,
                        'ParentID' => $menu1->id,
                        'Label' => $item2['Label'],
                        'Visible' => "N",
                        'icon' => array_key_exists('icon', $item2) ? $item2['icon'] : null,
                        'link' => array_key_exists('link', $item2) ? $item2['link'] : null,
                    ]);
                    // Settings
                    $IsArray2 = is_array($item2['children']) ? 'Yes' : 'No';
                    if ($IsArray2 == "Yes") {
                        foreach ($item2['children'] as $key => $item3) {
                            $menu3 = FM100::create([
                                'UserSign' => $this->user_id,
                                'ParentID' => $menu2->id,
                                'Label' => $item3['Label'],
                                'Visible' => "N",
                                'icon' => array_key_exists('icon', $item3) ? $item3['icon'] : null,
                                'link' => array_key_exists('link', $item3) ? $item3['link'] : null,
                            ]);

                            $IsArray3 = is_array($item3['children']) ? 'Yes' : 'No';
                            if ($IsArray3 == "Yes") {
                                foreach ($item3['children'] as $key => $item4) {
                                    $menu4 = FM100::create([
                                        'UserSign' => $this->user_id,
                                        'ParentID' => $menu3->id,
                                        'Label' => $item4['Label'],
                                        'Visible' => "N",
                                        'icon' => array_key_exists('icon', $item4) ? $item4['icon'] : null,
                                        'link' => array_key_exists('link', $item4) ? $item4['link'] : null,
                                    ]);

                                    $IsArray4 = is_array($item4['children']) ? 'Yes' : 'No';
                                    if ($IsArray4 == "Yes") {
                                        foreach ($item4['children'] as $key => $item5) {
                                            $menu5 = FM100::create([
                                                'UserSign' => $this->user_id,
                                                'ParentID' => $menu4->id,
                                                'Label' => $item5['Label'],
                                                'Visible' => "N",
                                                'icon' => array_key_exists('icon', $item5) ? $item5['icon'] : null,
                                                'link' => array_key_exists('link', $item5) ? $item5['link'] : null,
                                            ]);
                                            $IsArray5 = is_array($item5['children']) ? 'Yes' : 'No';
                                            if ($IsArray5 == "Yes") {
                                                foreach ($item5['children'] as $key => $item6) {
                                                    $menu6 = FM100::create([
                                                        'UserSign' => $this->user_id,
                                                        'ParentID' => $menu5->id,
                                                        'Label' => $item6['Label'],
                                                        'Visible' => "N",
                                                        'icon' => array_key_exists('icon', $item6) ? $item6['icon'] : null,
                                                        'link' => array_key_exists('link', $item6) ? $item6['link'] : null,
                                                    ]);
                                                    $IsArray6 = is_array($item6['children']) ? 'Yes' : 'No';
                                                    if ($IsArray6 == "Yes") {
                                                        foreach ($item6['children'] as $key => $item7) {
                                                            $menu7 = FM100::create([
                                                                'UserSign' => $this->user_id,
                                                                'ParentID' => $menu6->id,
                                                                'Label' => $item7['Label'],
                                                                'Visible' => "N",
                                                                'icon' => array_key_exists('icon', $item7) ? $item7['icon'] : null,
                                                                'link' => array_key_exists('link', $item7) ? $item7['link'] : null,
                                                            ]);
                                                            $IsArray7 = is_array($item7['children']) ? 'Yes' : 'No';
                                                            if ($IsArray7 == "Yes") {
                                                                foreach ($item7['children'] as $key => $item8) {
                                                                    $menu8 = FM100::create([
                                                                        'UserSign' => $this->user_id,
                                                                        'ParentID' => $menu7->id,
                                                                        'Label' => $item8['Label'],
                                                                        'Visible' => "N",
                                                                        'icon' => array_key_exists('icon', $item8) ? $item8['icon'] : null,
                                                                        'link' => array_key_exists('link', $item8) ? $item8['link'] : null,
                                                                    ]);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
