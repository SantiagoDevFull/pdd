<?php

namespace App\Http\Controllers\Backend;

use App\Clients\EfactClient;
use App\Clients\EfactCordClient;
use App\Http\Controllers\Controller;

class TockenController extends Controller
{

    private $billingClientPunto;
    private $billingClientCordia;

    public function __construct(EfactClient $billingClientPunto, EfactCordClient $billingClientCordia)
    {
        $this->billingClientPunto = $billingClientPunto;
        $this->billingClientCordia = $billingClientCordia;
    }

    public function index()
    {

        echo "entre<br>";

        echo $this->billingClientPunto->getAccessToken()."<br>";
        sleep(10);
        echo $this->billingClientPunto->getTokenRemainingTime();

    }
}
