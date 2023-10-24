<?php

namespace Leysco100\MarketingDocuments\Actions;

use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\UserDict;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Models\MarketingDocuments\Models\ORDR;

class MapApiFieldAction
{
  public static function handle($request)
  {
    $fieldsJsonString = file_get_contents(base_path('resources/setupdata/MarketingDocuments/o_r_d_r_s.json'));

    $fields = json_decode($fieldsJsonString, true);
    $documentData = [];

    $rules = [];
    $attributes = [];

    $TargetTables = APDI::with('pdi1')
      ->where('ObjectID', 300)
      ->first();

    $table = (new $TargetTables->ObjectHeaderTable)->getTable();
    // return $table;
    foreach ($fields as $key => $field) {

      $fieldName = $field['FieldName'];
      $label = $field['Label'];
      $fieldType = $field['FieldType'];
      $fieldMaxLength = $field['FieldMaxLength'];
      $fieldMinLength = $field['FieldMinLength'];
      $isNullable = $field['IsNullable'];
      $isUnique = $field['IsUnique'];
      $starts_with = $field['StartsWith'] ?? "";
      $size =  $field['Size']?? "";

      $rule = [];

      if (!$isNullable) {
        $rule[] = 'required';
      }
      if ($isUnique) {
        $rule[] = 'unique:tenant.' . $table;
      }
      if (isset($starts_with)&& !empty($starts_with)) {
        $rule[] = 'starts_with:' . implode(',', $starts_with);
      }
      if (isset($size) && !empty($size)) {
        $rule[] = 'size:' . $size;
      }

      if ($fieldType === 'string' && is_numeric($fieldMaxLength)) {
        $rule[] = 'max:' . $fieldMaxLength;
      }
      if ($fieldType === 'string' && is_numeric($fieldMinLength)) {
        $rule[] = 'min:' . $fieldMinLength;
      }

      if (isset($fieldType) && $fieldType != 'string') {
        $rule[] = $fieldType;
      }


      $rules[$fieldName] = implode('|', $rule);

      $attributes[$fieldName] = $label;
      Log::info([$rules]);

      $documentData[$field['FieldName']] = $request[$field['FieldName']];
    }
    $validator = Validator::make($request->all(), $rules);

    $validator->setAttributeNames($attributes);

    if ($validator->fails()) {
      return $validator->errors();
    }

    $headerUdfs = UserDict::where('ObjType', $request['ObjType'])->get();


    foreach ($headerUdfs as $key =>  $headerUdf) {
      $documentData[$headerUdf['FieldName']] = $request[$headerUdf['FieldName']];
    }




    return $documentData;
  }
}
