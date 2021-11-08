<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Log;
use App\Libs\Services\WagerService;

/**
 * Wager controller
 *
 * @package App\Http\Controller;
 * @author Khoa Pham <phamdangkhoa31@gmail.com>
 */

class WagerController extends Controller
{
    const ROWS_PER_PAGE_LIMIT = 10;
    public $wagersService = null;

    public function __construct(WagerService $wagerService)
    {
        $this->wagerService = $wagerService;
    }

    protected function createWagerValidation($data)
    {
        $validator = Validator::make($data , [
            'total_wager_value' => [
                'required',
                'integer',
                'min:1',
            ],
            'odds' => [
                'required',
                'integer',
                'min:1'
            ],
            'selling_percentage' => [
                'required',
                'integer',
                'min:1',
                'max:100'
            ],
            'selling_price' => [
                'required',
                'regex:/^\d+(\.\d{1,2})?$/',
                function ($attribute , $value , $fail) use ($data) {
                    if ($value <= ($data['total_wager_value'] * ($data['selling_percentage'] / 100))) {
                        return $fail('The selling price value is invalid');
                    }
                }
            ]
        ] , ['selling_price.regex' => 'The selling price format is invalid']);

        return $validator;
    }

    public function store(Request $request)
    {
        Log::info('Entry : WagersController@create');
        try {
            //Get input data
            $wagerData = (!empty($request->json()->all())) ? $request->json()->all() : $request->all();

            //Validation before save to DB
            $validator = $this->createWagerValidation($wagerData);
            if ($validator->fails()) {
                Log::info('Exit : WagersController@create');
                return response()->json(['error' => $validator->errors()])->header('HTTP' , Response::HTTP_BAD_REQUEST);
            }

            //Create a wager
            $wagerObj = $this->wagerService->storeWager($wagerData);

            Log::info('Exit : WagersController@create');

            return response()->json($wagerObj)->header('HTTP' , Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error when create a wager' , ['message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
            return response()->json(['error' => 'Error when create a wager'])->header('HTTP' , Response::HTTP_BAD_REQUEST);
        }
    }

    protected function buyAWagerValidation($data , $wagerCurrentSellingPrie)
    {
        $validator = Validator::make($data , [
            'buying_price' => [
                'required',
                'regex:/^\d+(\.\d{1,2})?$/',
                function ($attribute , $value , $fail) use ($wagerCurrentSellingPrie) {
                    if ($value > $wagerCurrentSellingPrie) {
                        return $fail('The buying price must be lesser or equal to current selling price of this wager');
                    }
                }
            ]
        ] , ['buying_price.regex' => 'The buying price format is invalid']);

        return $validator;
    }

    public function buyAWager($wagerId , Request $request)
    {
        Log::info('Entry : WagersController@buyAWager');
        try {
            //Validation before do action : buy a wager
           if (empty($wagerId)) {
                Log::info('Exit : WagersController@buyAWager');
               return response()->json(['error' => 'Wager id is empty'])->header('HTTP' , Response::HTTP_BAD_REQUEST);
           }

           $wagerObj = \App\Wager::find($wagerId);
           if (empty($wagerObj)) {
                Log::info('Exit : WagersController@buyAWager');
               return response()->json(['error' => 'Wager id is invalid . This wager does not existed'])->header('HTTP' , Response::HTTP_BAD_REQUEST);
           }

           //Get input data
            $wagerBuyingData = (!empty($request->json()->all())) ? $request->json()->all() : $request->all();
            $wagerBuyingData['buyer_id'] = JWTAuth::parseToken()->authenticate()->id;

           $validator = $this->buyAWagerValidation($wagerBuyingData , $wagerObj->current_selling_price);
            if ($validator->fails()) {
                Log::info('Exit : WagersController@buyAWager');
                return response()->json(['error' => $validator->errors()])->header('HTTP' , Response::HTTP_BAD_REQUEST);
            }

            //Do acation : buy a wager (wagers)
            $wagers = $this->wagerService->buyAWager($wagerId , $wagerBuyingData);

            Log::info('Exit : WagersController@buyAWager');

            return response()->json($wagers)->header('HTTP' , Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error when buy a wager' , ['message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
            return response()->json(['error' => 'Error when buy a wager'])->header('HTTP' , Response::HTTP_BAD_REQUEST);
        }
    }

    public function index(Request $request)
    {
        Log::info('Entry : WagersController@index');
        try {
            $queryString = $request->all();
            $limit = (!empty($queryString['limit'])) ? $queryString['limit'] : ROWS_PER_PAGE_LIMIT;

            $wagerList = $this->wagerService->getWagerList($limit);

            Log::info('Exit : WagersController@index');

            return response()->json($wagerList)->header('HTTP' , Response::HTTP_OK);
        } catch (\Exception $e) {
            Log::error('Error when get wager list' , ['message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
            return response()->json(['error' => 'Error when get wager list'])->header('HTTP' , Response::HTTP_BAD_REQUEST);
        }
    }
}
