<?php

namespace Leysco100\MarketingDocuments\Actions;

use Leysco100\Shared\Models\UserDict;

class MapApiFieldAction
{
  public static function handle($request)
  {
    $fieldsJsonString = file_get_contents(base_path('resources/setupdata/field_header_footer_details.json'));
    $fields = json_decode($fieldsJsonString, true);
    $documentData = [];

    foreach ($fields as $key => $field) {
      $documentData[$field['FieldName']] = $request[$field['FieldName']];
    }

    $headerUdfs = UserDict::where('ObjType',$request['ObjType'])->get();


    foreach ( $headerUdfs as $key =>  $headerUdf) {
      $documentData[$headerUdf['FieldName']] = $request[$headerUdf['FieldName']];
    }




    return $documentData;
  }
}
