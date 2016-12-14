<?php

namespace App\Models;

class MassSending
{
    function __construct()
    {

    }

    public $cardIds = [];
    public $queryConditions = [];
    public $jobs = [];
    public $message = [];
    public $reason;
}
