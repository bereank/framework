<?php
namespace Leysco100\Shared\Http\Controllers\API;



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;

use Leysco100\Gpm\Services\DocumentsService;
use Leysco100\Shared\Jobs\FormSettingUpdate;
use Leysco100\Shared\Models\Administration\Jobs\CreateMenuForUser;
use Leysco100\Shared\Models\CUFD;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Http\Controllers\Controller;
use Leysco100\Shared\Models\Shared\Models\FI100;
use Leysco100\Shared\Models\Shared\Models\FM100;
use Leysco100\Shared\Models\Shared\Models\FT100;
use Leysco100\Shared\Models\Shared\Models\FTR100;
use Leysco100\Shared\Models\Administration\Models\NNM1;
use Leysco100\Shared\Models\Administration\Models\NNM2;
use Leysco100\Shared\Models\Administration\Models\ONNM;
use Leysco100\Shared\Models\Administration\Models\OUDP;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Actions\Helpers\HideTableRowsFieldsPerDocumentAction;


class FormSettingsController extends Controller
{
    public function getFormSettings($ObjType)
    {
        $form = APDI::with('pdi1')
            ->where('ObjectID', $ObjType)
            ->first();

        if (!$form) {
            return response()->json([
                'Message' => "Object",
            ], 404);
        }

        $headerFields = FI100::where('FormID', $form->id)
            ->where('Location', 0)
            ->where('TabID', null)
            ->get();

        foreach ($headerFields as $key => $value) {
            if ($ObjType == 205 && $value->FieldName == "DocDueDate") {
                $value->Label = "Valid Until";
            }

            if ($ObjType == 13 && $value->FieldName == "DocDueDate") {
                $value->Label = "Due Date";
            }

            if ($ObjType == 14 && $value->FieldName == "DocDueDate") {
                $value->Label = "Due Date";
            }
        }

        $footerFields = FI100::where('FormID', $form->id)
            ->where('Location', 1)
            ->where('TabID', null)
            ->get();
//        $line_table = (new $form->pdi1[0]['ChildTable'])->getTable();
        $header_table = (new $form->ObjectHeaderTable)->getTable();

        $UDFs = CUFD::where('ObjType', $ObjType)
            ->where("TableName",$header_table)
            ->get();

        $tabs = FT100::where('FormID', $form->id)->get();

        foreach ($tabs as $key => $value) {
            $Fields = FI100::where('TabID', $value->id)->get();
            $tableRows = FTR100::where('TabID', $value->id)->get();
            foreach ($tableRows as $key => $tableRow) {
                $tableRow = (new HideTableRowsFieldsPerDocumentAction($ObjType, $tableRow))->handle();
            }
            $value->tableRows = $tableRows;
            $value->tableRowsModalVisible = FTR100::where('TabID', $value->id)
                ->where('modalVisible', 'Y')
                ->get();
        }

        //default Numembering Seires
        $documentDefaultSeries = NNM1::where('id', ONNM::where('ObjectCode', $form->id)
                ->value('DfltSeries'))
            ->where('Locked', 'N')
            ->first();
        $documentDefaultSeries->NextNumber = sprintf("%0" . $documentDefaultSeries->NumSize . "s", $documentDefaultSeries->NextNumber);
        $Series = NNM1::where('ObjectCode', ONNM::where('ObjectCode', $form->id)
                ->value('id'))
            ->where('Locked', 'N')
            ->get();
        foreach ($Series as $key => $nnm1) {
            $nnm1->NextNumber = sprintf("%0" . $nnm1->NumSize . "s", $nnm1->NextNumber);
        }

        $currentUserDefaultSeries = NNM2::with('nnm1')->where('ObjectCode', $form->id)
            ->whereHas('nnm1', function ($q) {
                $q->where('Locked', 'N');
            })
            ->where('UserSign', Auth::user()->id)
            ->first();

        if ($currentUserDefaultSeries) {
            $nnm1Data = NNM1::where('id', $currentUserDefaultSeries['Series'])
                ->where('Locked', 'N')
                ->first();
            $nnm1Data->NextNumber = sprintf("%0" . $nnm1Data->NumSize . "s", $nnm1Data->NextNumber);

            $documentDefaultSeries = $nnm1Data;
        }

        /**
         * Check if Service Call
         */

//        $serviceCallOrigins = [];
//        if ($ObjType) {
//            $serviceCallOrigins = OSCO::where('Locked', 'N')->get();
//        }

        return response()->json([
            'FormName' => $form->DocumentName,
            'ObjType' => $ObjType,
            'HeaderFields' => $headerFields,
            'FooterFields' => $footerFields,
            'UDFs' => $UDFs,
            'tabs' => $tabs,
            'DfltSeries' => $documentDefaultSeries,
            'Series' => $Series,
            'users' => User::where('type', 'NU')->with('oudg.branch')->get(),
            'departments' => OUDP::get(),
            'DefaultBPLId' => Auth::user()->oudg->BPLId, // Default Branch
//            'serviceCallOrigins' => $serviceCallOrigins,
        ]);
    }

    public function updateFormSettings(Request $request)
    {
        try {
            $headerFields = $request['HeaderFields'];

            $footerFields = $request['FooterFields'];
            $tabs = $request['tabs'];

            foreach ($headerFields as $key => $value) {
                $field = FI100::findOrFail($value['id']);
                $details = [
                    'FieldName' => $value['FieldName'],
                    'Label' => $value['Label'],
                    'FieldType' => $value['FieldType'],
                    'ColumnWidth' => $value['ColumnWidth'],
                    'Visible' => $value['Visible'],
                    'Readonly' => $value['Readonly'],
                    'Required' => $value['Required'],
                    "data" => $value['data'],
                    "Location" => $value['Location'],
                    'TabID' => $value['TabID'],
                ];
                $field->update($details);
            }

            foreach ($footerFields as $key => $value) {
                $field = FI100::findOrFail($value['id']);
                $details = [
                    'FieldName' => $value['FieldName'],
                    'Label' => $value['Label'],
                    'FieldType' => $value['FieldType'],
                    'ColumnWidth' => $value['ColumnWidth'],
                    'Visible' => $value['Visible'],
                    'Readonly' => $value['Readonly'],
                    'Required' => $value['Required'],
                    "data" => $value['data'],
                    "Location" => $value['Location'],
                    'TabID' => $value['TabID'],
                ];
                $field->update($details);
            }

            foreach ($tabs as $key => $tab) {
                $fields = $tab['Fields'] ?? null;
                $tableRows = $tab['tableRows'];

                if ($fields) {
                    foreach ($fields as $key => $value) {
                        $field = FI100::findOrFail($value['id']);
                        $details = [
                            'FieldName' => $value['FieldName'],
                            'Label' => $value['Label'],
                            'FieldType' => $value['FieldType'],
                            'ColumnWidth' => $value['ColumnWidth'],
                            'Visible' => $value['Visible'],
                            'Readonly' => $value['Readonly'],
                            'Required' => $value['Required'],
                            "data" => $value['data'],
                            "Location" => $value['Location'],
                            'TabID' => $value['TabID'],
                        ];
                        $field->update($details);
                    }
                }

                foreach ($tableRows as $key => $tablerow) {
                    $field = FTR100::findOrFail($tablerow['id']);
                    $details = [
                        'FieldType' => $tablerow['FieldType'],
                        'text' => $tablerow['text'],
                        'value' => $tablerow['value'],
                        "data" => $tablerow['data'],
                        "itemText" => $tablerow['itemText'],
                        "itemValue" => $tablerow['itemValue'],
                        'ColumnWidth' => $tablerow['ColumnWidth'],
                        'Visible' => $tablerow['Visible'],
                        'modalVisible' => $tablerow['modalVisible'],
                        'readonly' => $tablerow['readonly'],
                    ];
                    $field->update($details);
                }
            }
            return response()->json([
                'Message' => "Updated Successfully",
            ]);
        } catch (\Throwable$th) {
            Log::info($th);
        }
    }

    public function formSettingsMenu()
    {
        $user = Auth::user();

        $AllTreeview = FM100::whereNull('ParentID')
            ->where('UserSign', 1)
            ->with('GrandChildren')
            ->get();
        $VisibleIds = FM100::where('Visible', 'Y')
            ->where('UserSign', 1)
            ->pluck('id');
        $AllIds = FM100::where('UserSign', 1)
            ->pluck('id');

        return response()->json([
            'AllTreeview' => $AllTreeview,
            'VisibleIds' => $VisibleIds,
            'AllIds' => $AllIds,
        ]);
    }

    public function getUserMenuSettings($user_id)
    {
        $userfm100 = FM100::where('UserSign', $user_id)
            ->first();
        $AllTreeview = FM100::whereNull('ParentID')
            ->where('UserSign', $user_id)
            ->with('GrandChildren')
            ->get();
        $VisibleIds = FM100::where('Visible', 'Y')
            ->where('UserSign', $user_id)
            ->pluck('id');
        $AllIds = FM100::where('UserSign', $user_id)
            ->pluck('id');

        return response()->json([
            'AllTreeview' => $AllTreeview,
            'VisibleIds' => $VisibleIds,
            'AllIds' => $AllIds,
        ]);
    }

    public function updateFormSettingsMenu(Request $request)
    {
        
        $this->validate($request, [
            'UserSign' => 'required',
        ]);

        FormSettingUpdate::dispatchSync($request['AllIds'],
         $request['SelectedIds'],
          $request['UserSign']);

        return response()->json([
            'Message' => "Updated Successfully",
        ]);
    }

    public function updateSingleMenu(Request $request, $id)
    {
        $details = [
            'Label' => $request['Label'],
        ];

        FM100::where('id', $id)->update($details);

        return response()->json([
            'Message' => "Updated Successfully",
        ]);
    }
}
