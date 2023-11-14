<?php

namespace Leysco100\MarketingDocuments\Actions;

use Illuminate\Support\Arr;
use Leysco100\Shared\Models\CUFD;
use Leysco100\Shared\Services\ApiResponseService;

class MapApiFieldAction
{
  public  function handle($data, $TargetTables)
  {

    //Header UDF  validation
    $table = (new $TargetTables->ObjectHeaderTable)->getTable();
    $headerUdfs = CUFD::where('TableName', $table)->get();

    if (!$headerUdfs->isEmpty()) {
      $documentData = [];

      foreach ($data['udfs'] as $item) {
        foreach ($headerUdfs as $headerUdf) {
          $exists = Arr::has($item, $headerUdf['FieldName']);

          if (!$exists && $headerUdf->NotNull) {
            return (new ApiResponseService())->apiSuccessAbortProcessResponse($headerUdf['FieldName'] . " Is a required Field");
          }

          if ($exists) {
            $documentData[$headerUdf['FieldName']] = $item[$headerUdf['FieldName']];
          }
        }
      }

      $data['udfs'] = $documentData;
    } else {
      return;
    }

    // Lines UDF validation
    $line_table = (new $TargetTables->pdi1[0]['ChildTable'])->getTable();
    $line_fields = CUFD::where('TableName', $line_table)->get();

    if (!$line_fields->isEmpty()) {
      foreach ($data['document_lines'] as &$doc_lines) {
        $lineData = [];

        foreach ($line_fields as $line_field) {
          $exists = false;

          foreach ($doc_lines['udfs'] as $item) {
            if (Arr::has($item, $line_field['FieldName'])) {
              $exists = true;
              break;
            }

            if (!$exists && $line_field->NotNull) {
              return (new ApiResponseService())->apiSuccessAbortProcessResponse($line_field['FieldName'] . " Is a required Field");
            }
          }

          if ($exists) {
            $lineData[$line_field['FieldName']] = Arr::get($doc_lines['udfs'], $line_field['FieldName']);
          }
        }

        $doc_lines['udfs'] = $lineData;
      }
    } else {

      return;
    }

    return $data;
  }

  // public function docValidator($data, $table, $fields)
  // {

  //   $documentData = [];

  //   $rules = [];
  //   $attributes = [];

  //   foreach ($fields as $key => $field) {

  //     $fieldName = $field['FieldName'];
  //     $fieldType = $field['FieldType'];
  //     $fieldMaxLength = $field['FieldMaxLength'];
  //     $fieldMinLength = $field['FieldMinLength'];
  //     $isNullable = $field['IsNullable'];
  //     $isUnique = $field['IsUnique'];
  //     $starts_with = $field['StartsWith'] ?? [];
  //     $ends_with = $field['EndsWith'] ?? [];
  //     $size = $field['Size'] ?? '';
  //     $regex = $field['Regwex'] ?? '';
  //     $rfield = $field['RField'];
  //     $rtable = $field['RTable'];

  //     $rule = [];

  //     if (!$isNullable) {
  //       $rule[] = 'required';
  //     }
  //     if (isset($isUnique) && $isUnique) {
  //       $rule[] = 'unique:tenant.' . $table;
  //     }

  //     if (isset($rfield) && isset($rtable)) {
  //       $rule[] = 'exists:tenant.' .$rtable. ',' .$rfield;
  //     }
  //     if (isset($starts_with) && !empty(array_filter($starts_with))) {

  //       $rule[] = 'starts_with:' . implode(',', $starts_with);
  //     }
  //     if (isset($ends_with) && !empty(array_filter($ends_with))) {
  //       $rule[] = 'ends_with:' . implode(',', $ends_with);
  //     }
  //     if (isset($size) && !empty($size)) {
  //       $rule[] = 'size:' . $size;
  //     }
  //     if (isset($regex) && !empty($regex)) {
  //       $rule[] = 'regex:' . $regex;
  //     }
  //     if ($fieldType === 'string' && is_numeric($fieldMaxLength)) {
  //       $rule[] = 'max:' . $fieldMaxLength;
  //     }
  //     if ($fieldType === 'string' && is_numeric($fieldMinLength)) {
  //       $rule[] = 'min:' . $fieldMinLength;
  //     }

  //     if (isset($fieldType) && $fieldType != 'string') {
  //       $rule[] = $fieldType;
  //     }

  //     $rules[$fieldName] = implode('|', $rule);

  //     $attributes[$fieldName] = $fieldName;
  //     Log::info($rules);
  //     if (array_key_exists($field['FieldName'], $data)) {
  //       $documentData[$field['FieldName']] = $data[$field['FieldName']];
  //     }
  //   }
  //   $validator = Validator::make($data, $rules);

  //   $validator->setAttributeNames($attributes);

  //   if ($validator->fails()) {
  //     return (new ApiResponseService())->apiSuccessAbortProcessResponse($validator->errors());
  //   } else {
  //     return  $documentData;
  //   }

  // }
  // $headerUdfs = UserDict::where('ObjType', $request['ObjType'])->get();


  // foreach ($headerUdfs as $key => $headerUdf) {
  //   $documentData[$headerUdf['FieldName']] = $request[$headerUdf['FieldName']];
  // }
}
