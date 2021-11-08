<?php

use Tests\TestCase;
use Illuminate\Http\Response;
use App\Libs\Services\WagerService;

class WagerControllerTest extends TestCase
{
    public $wagersService = null;

    public function setUp() : void
    {
        parent::setUp();
        $this->refreshApplication();

        $this->wagerService = app(WagerService::class);
    }

    public function testCreateAWager()
    {
        //dd($this->post('/signup' , ['email' => substr(uniqid() , 1 , 9) . '@mail.com' , 'name' => 'testName' , 'password' => bcrypt('abc123')]));
    }
}
