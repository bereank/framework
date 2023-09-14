<?php

namespace Leysco100\MarketingDocuments\Http\Controllers\API\V2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Leysco100\Shared\Models\Administration\Models\OUDG;
use Leysco100\Shared\Models\Administration\Models\User;
use Leysco100\Shared\Models\BusinessPartner\Models\OCLG;
use Leysco100\MarketingDocuments\Actions\MapApiFieldAction;
use Leysco100\Shared\Models\MarketingDocuments\Models\ORDR;
use Leysco100\MarketingDocuments\Http\Controllers\Controller;



class DocumentControllerPOC extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $fields = (new MapApiFieldAction())->handle($request);
        ORDR::create($fields);

      
      
    }

}