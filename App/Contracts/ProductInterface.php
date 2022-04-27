<?php

namespace App\Contracts;

interface ProductInterface
{
    public function getSku() : string;
    public function getPrice() : int;
}
