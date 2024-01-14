<?php

namespace Leysco100\MarketingDocuments\Console;

use Illuminate\Console\Command;
use Leysco100\Shared\Models\Shared\Models\FM100;
use Leysco100\Shared\Services\CommonService;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;



class MaketingDocumentsInstallCommand extends Command
{

    use TenantAware;
    protected $signature = 'leysco100:marketing-documents:install {--tenant=*}';

    protected $description = 'Installing Marketing Documents Package';

    public function handle()
    {

   

        $menuJsonString = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'MenuItem.Json');
        $menuitems = json_decode($menuJsonString, true);

        (new CommonService())->createOrUpdateMenu($menuitems);

//        foreach ($menuitems as $key => $item1) {
//
//
//            $menu1 = FM100::updateOrCreate([
//                'UserSign' => 1,
//                'Label' => $item1['Label'],
//                'Visible' => $item1['Visible'],
//                'icon' => array_key_exists('icon', $item1) ? $item1['icon'] : null,
//                'link' => array_key_exists('link', $item1) ? $item1['link'] : null,
//            ]);
//
//            $IsArray1 = is_array($item1['children']) ? 'Yes' : 'No';
//            if ($IsArray1 == "Yes") {
//                foreach ($item1['children'] as $key => $item2) {
//                    $menu2 = FM100::firstOrCreate([
//                        'UserSign' => 1,
//                        'ParentID' => $menu1->id,
//                        'Label' => $item2['Label'],
//                        'Visible' => $item2['Visible'],
//                        'icon' => array_key_exists('icon', $item2) ? $item2['icon'] : null,
//                        'link' => array_key_exists('link', $item2) ? $item2['link'] : null,
//                    ]);
//                    // Settings
//                    $IsArray2 = is_array($item2['children']) ? 'Yes' : 'No';
//                    if ($IsArray2 == "Yes") {
//                        foreach ($item2['children'] as $key => $item3) {
//                            $menu3 = FM100::firstOrCreate([
//                                'UserSign' => 1,
//                                'ParentID' => $menu2->id,
//                                'Label' => $item3['Label'],
//                                'Visible' => $item3['Visible'],
//                                'icon' => array_key_exists('icon', $item3) ? $item3['icon'] : null,
//                                'link' => array_key_exists('link', $item3) ? $item3['link'] : null,
//                            ]);
//
//                            $IsArray3 = is_array($item3['children']) ? 'Yes' : 'No';
//                            if ($IsArray3 == "Yes") {
//                                foreach ($item3['children'] as $key => $item4) {
//                                    $menu4 = FM100::firstOrCreate([
//                                        'UserSign' => 1,
//                                        'ParentID' => $menu3->id,
//                                        'Label' => $item4['Label'],
//                                        'Visible' => $item4['Visible'],
//                                        'icon' => array_key_exists('icon', $item4) ? $item4['icon'] : null,
//                                        'link' => array_key_exists('link', $item4) ? $item4['link'] : null,
//                                    ]);
//
//                                    $IsArray4 = is_array($item4['children']) ? 'Yes' : 'No';
//                                    if ($IsArray4 == "Yes") {
//                                        foreach ($item4['children'] as $key => $item5) {
//                                            $menu5 = FM100::firstOrCreate([
//                                                'UserSign' => 1,
//                                                'ParentID' => $menu4->id,
//                                                'Label' => $item5['Label'],
//                                                'Visible' => $item5['Visible'],
//                                                'icon' => array_key_exists('icon', $item5) ? $item5['icon'] : null,
//                                                'link' => array_key_exists('link', $item5) ? $item5['link'] : null,
//                                            ]);
//                                            $IsArray5 = is_array($item5['children']) ? 'Yes' : 'No';
//                                            if ($IsArray5 == "Yes") {
//                                                foreach ($item5['children'] as $key => $item6) {
//                                                    $menu6 = FM100::firstOrCreate([
//                                                        'UserSign' => 1,
//                                                        'ParentID' => $menu5->id,
//                                                        'Label' => $item6['Label'],
//                                                        'Visible' => $item6['Visible'],
//                                                        'icon' => array_key_exists('icon', $item6) ? $item6['icon'] : null,
//                                                        'link' => array_key_exists('link', $item6) ? $item6['link'] : null,
//                                                    ]);
//                                                    $IsArray6 = is_array($item6['children']) ? 'Yes' : 'No';
//                                                    if ($IsArray6 == "Yes") {
//                                                        foreach ($item6['children'] as $key => $item7) {
//                                                            $menu7 = FM100::firstOrCreate([
//                                                                'UserSign' => 1,
//                                                                'ParentID' => $menu6->id,
//                                                                'Label' => $item7['Label'],
//                                                                'Visible' => $item7['Visible'],
//                                                                'icon' => array_key_exists('icon', $item7) ? $item7['icon'] : null,
//                                                                'link' => array_key_exists('link', $item7) ? $item7['link'] : null,
//                                                            ]);
//                                                            $IsArray7 = is_array($item7['children']) ? 'Yes' : 'No';
//                                                            if ($IsArray7 == "Yes") {
//                                                                foreach ($item7['children'] as $key => $item8) {
//                                                                    $menu8 = FM100::firstOrCreate([
//                                                                        'UserSign' => 1,
//                                                                        'ParentID' => $menu7->id,
//                                                                        'Label' => $item8['Label'],
//                                                                        'Visible' => $item8['Visible'],
//                                                                        'icon' => array_key_exists('icon', $item8) ? $item8['icon'] : null,
//                                                                        'link' => array_key_exists('link', $item8) ? $item8['link'] : null,
//                                                                    ]);
//                                                                }
//                                                            }
//                                                        }
//                                                    }
//                                                }
//                                            }
//                                        }
//                                    }
//                                }
//                            }
//                        }
//                    }
//                }
//            }
//        }
//
//
//        FM100::query()->update([
//            'Visible' => 'Y',
//        ]);


    }

}
