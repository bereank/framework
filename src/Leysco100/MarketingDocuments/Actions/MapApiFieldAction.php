<?php

namespace Leysco100\MarketingDocuments\Actions;

use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Models\UserDict;
use Illuminate\Support\Facades\Validator;
use Leysco100\Shared\Models\Shared\Models\APDI;
use Leysco100\Shared\Services\ApiResponseService;
use Leysco100\Shared\Models\MarketingDocuments\Models\ORDR;
use Leysco100\Shared\Models\MarketingDocuments\Models\DocsValidation;

class MapApiFieldAction
{
  public  function handle($request, $TargetTables)
  {
    $data = $request->all();

    //Header table validation
    $filtered_data = [];

    $table = (new $TargetTables->ObjectHeaderTable)->getTable();
    $fields = DocsValidation::where('TableName', $table)->where('RtrnOnCreate', 1)->get();
    if (!$fields->isEmpty()) {
      $headerResult = $this->docValidator($data, $table, $fields);
    } else {
      return (new ApiResponseService())->apiSuccessAbortProcessResponse("Document header Validations Not set");
    }

    // Lines validation
    $TargetTables = APDI::with('pdi1')
      ->where('ObjectID', $request['ObjType'])
      ->first();

    $line_table = (new $TargetTables->pdi1[0]['ChildTable'])->getTable();
    $line_fields = DocsValidation::where('TableName', $line_table)->where('RtrnOnCreate', 1)->get();
    $lines = [];
    if (!$line_fields->isEmpty()) {
      foreach ($data['document_lines'] as $doc_lines) {
        $line_result = $this->docValidator($doc_lines, $line_table, $line_fields);
        $lines[] = $line_result;
      }
    } else {
      return (new ApiResponseService())->apiSuccessAbortProcessResponse("Lines Validations Not set");
    }
    // $headerUdfs = UserDict::where('ObjType', $request['ObjType'])->get();


    // foreach ($headerUdfs as $key => $headerUdf) {
    //   $documentData[$headerUdf['FieldName']] = $request[$headerUdf['FieldName']];
    // }

    return ["header_data" => $headerResult, "document_lines" => $lines];
  }

  public function docValidator($data, $table, $fields)
  {

    $documentData = [];

    $rules = [];
    $attributes = [];

    foreach ($fields as $key => $field) {

      $fieldName = $field['FieldName'];
      $fieldType = $field['FieldType'];
      $fieldMaxLength = $field['FieldMaxLength'];
      $fieldMinLength = $field['FieldMinLength'];
      $isNullable = $field['IsNullable'];
      $isUnique = $field['IsUnique'];
      $starts_with = $field['StartsWith'] ?? [];
      $ends_with = $field['EndsWith'] ?? [];
      $size = $field['Size'] ?? '';
      $regex = $field['Regwex'] ?? '';
      $rfield = $field['RField'];
      $rtable = $field['RTable'];

      $rule = [];

      if (!$isNullable) {
        $rule[] = 'required';
      }
      if (isset($isUnique) && $isUnique) {
        $rule[] = 'unique:tenant.' . $table;
      }

      if (isset($rfield) && isset($rtable)) {
        $rule[] = 'exists:tenant.' .$rtable. ',' .$rfield;
      }
      if (isset($starts_with) && !empty(array_filter($starts_with))) {

        $rule[] = 'starts_with:' . implode(',', $starts_with);
      }
      if (isset($ends_with) && !empty(array_filter($ends_with))) {
        $rule[] = 'ends_with:' . implode(',', $ends_with);
      }
      if (isset($size) && !empty($size)) {
        $rule[] = 'size:' . $size;
      }
      if (isset($regex) && !empty($regex)) {
        $rule[] = 'regex:' . $regex;
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

      $attributes[$fieldName] = $fieldName;
      Log::info($rules);
      if (array_key_exists($field['FieldName'], $data)) {
        $documentData[$field['FieldName']] = $data[$field['FieldName']];
      }
    }
    $validator = Validator::make($data, $rules);

    $validator->setAttributeNames($attributes);

    if ($validator->fails()) {
      return (new ApiResponseService())->apiSuccessAbortProcessResponse($validator->errors());
    } else {
      return  $documentData;
    }
  }
}
