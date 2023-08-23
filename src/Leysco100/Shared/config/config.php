<?php
return [
  'prefix' => 'api/v1',
  'middleware' => ['tenant','auth:sanctum'],
];