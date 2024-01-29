<?php

namespace Leysco100\MarketingDocuments\Actions;

use Leysco100\Shared\Models\CUFD;
use Illuminate\Support\Facades\Log;
use Leysco100\Shared\Services\ApiResponseService;


class MapApiFieldAction
{
  public  function handle($data, $TargetTables)
  {

    // Header UDF validation
    $table = (new $TargetTables->ObjectHeaderTable)->getTable();
    $headerUdfs = CUFD::where('TableName', $table)->get(['FieldName', 'NotNull'])->keyBy('FieldName')->toArray();

    if ($headerUdfs) {
      $documentData = [];

      if (!isset($data['udfs'])) {
        return (new ApiResponseService())->apiSuccessAbortProcessResponse("UDF is a required field");
      }

      foreach ($data['udfs'] as $item) {
        $key = array_key_first($item);
        $exists = array_key_exists($key, $headerUdfs);

        if ($exists) {
          $documentData[$key] = $item[$key];
        }
      }

      $data['udfs'] = $documentData;

      $notNullUdfs = array_filter($headerUdfs, function ($item) {
        return $item['NotNull'] == 1;
      });

      $keysOfInnerArrays = array_map('array_keys', $notNullUdfs);
      $keysNotInArrayB = array_diff(array_keys($keysOfInnerArrays), array_keys($data['udfs']));

      $commonKeys = array_intersect(array_keys($keysOfInnerArrays), array_keys($data['udfs']));

      foreach ($commonKeys as  $key => $value) {

        if (empty($data['udfs'][$value])) {
          $missingField = $value;
          return (new ApiResponseService())->apiSuccessAbortProcessResponse("$missingField Cannot be null");
        }
      }
      if (!empty($keysNotInArrayB)) {
        $missingField = $keysNotInArrayB[0];
        return (new ApiResponseService())->apiSuccessAbortProcessResponse("$missingField is a required field");
      }
      // $udfsArrayOfObjects = [];

      // foreach ($data['udfs'] as $key => $value) {
      //   $udfsArrayOfObjects[] = [
      //     $key => $value,
      //   ];
      // }

      // $data['udfs'] = $udfsArrayOfObjects;
    }


    // Lines UDF validation
    $line_table = (new $TargetTables->pdi1[0]['ChildTable'])->getTable();
    $line_fields =  CUFD::where('TableName', $line_table)->get(['FieldName', 'NotNull'])->keyBy('FieldName')->toArray();

    if ($line_fields) {

      foreach ($data['document_lines'] as &$doc_lines) {
        $lineData = [];
        if (!isset($doc_lines['udfs'])) {

          return (new ApiResponseService())->apiSuccessAbortProcessResponse("UDF is required Field");
        }

        foreach ($doc_lines['udfs'] as $item) {

          $key = array_key_first($item);
          $exists = array_key_exists($key,  $line_fields);

          if ($exists) {

            $lineData[$key] = $item[$key];
          }
        }

        $doc_lines['udfs'] = $lineData;
        $notNullUdfs = array_filter($line_fields, function ($item) {
          return $item['NotNull'] == 1;
        });

        $keysOfInnerArrays = array_map('array_keys', $notNullUdfs);
        $keysNotInArrayB = array_diff(array_keys($keysOfInnerArrays), array_keys($doc_lines['udfs']));

        $commonKeys = array_intersect(array_keys($keysOfInnerArrays), array_keys($doc_lines['udfs']));

        foreach ($commonKeys as  $key => $value) {

          if (empty($doc_lines['udfs'][$value])) {
            $missingField = $value;
            return (new ApiResponseService())->apiSuccessAbortProcessResponse("$missingField Cannot be null");
          }
        }
        if (!empty($keysNotInArrayB)) {
          $missingField = $keysNotInArrayB[array_key_first($keysNotInArrayB)];
          return (new ApiResponseService())->apiSuccessAbortProcessResponse("$missingField is a required field");
        }
        // $udfsArrayOfObjects = [];

        // foreach ($doc_lines['udfs'] as $key => $value) {
        //   $udfsArrayOfObjects[] = [
        //     $key => $value,
        //   ];
        // }

        // $doc_lines['udfs'] = $udfsArrayOfObjects;
      }
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
