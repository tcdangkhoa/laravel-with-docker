<?php

namespace app\Libs\Services;

use DB;
use App\Wager;
use App\Wagers;
use Illuminate\Support\Facades\Log;

/**
 * Wager service
 *
 * @package App\Libs\Services;
 * @author Khoa Pham <phamdangkhoa31@gmail.com>
 */

class WagerService
{
    public $wagerModel = null;
    public $wagersModel = null;

    public function __construct(Wager $wagerModel , Wagers $wagersModel)
    {
        $this->wagerModel = $wagerModel;
        $this->wagersModel = $wagersModel;
    }

    public function storeWager($wagerData)
    {
        try {
            $this->wagerModel->total_wager_value = $wagerData['total_wager_value'];
            $this->wagerModel->odds = $wagerData['odds'];
            $this->wagerModel->selling_percentage = $wagerData['selling_percentage'];
            $this->wagerModel->selling_price = $wagerData['selling_price'];
            $this->wagerModel->current_selling_price = $wagerData['selling_price'];
            $this->wagerModel->placed_at = date('Y-m-d H:i:s');
            $this->wagerModel->save();

            return $this->wagerModel->refresh();
        } catch (\Exception $e) {
            Log::error('Error when store wager to DB' , ['message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
            throw $e;
        }
    }

    public function buyAWager($wagerId , $wagerBuyingData)
    {
        try {
            DB::beginTransaction();

            //Save to wagers table
            $this->wagersModel->wager_id = $wagerId;
            $this->wagersModel->buying_price = $wagerBuyingData['buying_price'];
            $this->wagersModel->buyer_id = $wagerBuyingData['buyer_id'];
            $this->wagersModel->bought_at = date('Y-m-d H:i:s');
            $this->wagersModel->save();

            //Update current_selling_price , percentage_sold , amount_sold on wager table
            //Lock this wager record in DB to handle case : multiple action buy a wager at the same time
            $wagerObj = $this->wagerModel->lockForUpdate()->find($wagerId);
            $amountSold = $this->wagersModel->where('wager_id' , $wagerId)->sum('buying_price');
            $currentSellingPrice = $wagerObj->selling_price - $amountSold;
            $percentageSold = ($amountSold / $wagerObj->selling_price) * 100; 

            $wagerObj->current_selling_price = $currentSellingPrice;
            $wagerObj->percentage_sold = $percentageSold;
            $wagerObj->amount_sold = $amountSold;
            $wagerObj->save();

            DB::commit();

            return $this->wagersModel->refresh();
        } catch (\Exception $e) {
            Log::error('Error when store the action : buy a wager (wagers) to DB' , ['message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
            DB::rollBack();
            throw $e;
        }
    }

    public function getWagerList($limit)
    {
        try {
           return $this->wagerModel->select('*')->paginate($limit);
        } catch (\Exception $e) {
            Log::error('Error when get wager list from DB' , ['message' => $e->getMessage() , 'file' => $e->getFile() , 'line' => $e->getLine()]);
            throw $e;
        }
    }
}
