<?php

namespace App;

use App\Contracts\CheckoutInterface;

class Checkout implements CheckoutInterface
{
    /**
     * @var array
     */
    protected $cart = [];

    /**
     * @var int[]
     */
    protected $pricing = [
        'A' => 50,
        'B' => 30,
        'C' => 20,
        'D' => 15,
        'E' => 5
    ];

    /**
     * @var int[][]
     */
    protected $discounts = [
        'A' => [
            [
                'threshold' => 3,
                'amount' => 130
            ]
        ],
        'B' => [
            [
                'threshold' => 2,
                'amount' => 45
            ]
        ],
        'C' =>  [
            [
                'threshold' => 3,
                'amount' => 50
            ],
            [
                'threshold' => 2,
                'amount' => 38
            ],

        ],
        'D' => [
            [
                'threshold' => 5,
                'amount' => 15
            ]
        ]
    ];

    /**
     * @var int[]
     */
    protected $stats = [
        'A' => 0,
        'B' => 0,
        'C' => 0,
        'D' => 0,
        'E' => 5
    ];

    /**
     * Adds an item to the checkout
     *
     * @param $sku string
     */
    public function scan(string $sku)
    {
        if (!array_key_exists($sku, $this->pricing)) {
            return;
        }

        $this->stats[$sku] = $this->stats[$sku] + 1;

        $this->cart[] = [
            'sku' => $sku,
            'price' => $this->pricing[$sku]
        ];
    }

    /**
     * Calculates the total price of all items in this checkout
     *
     * @return int
     */
    public function total(): int
    {
        $standardPrices = array_reduce($this->cart, function ($total, array $product) {
            $total += $product['price'];
            return $total;
        }) ?? 0;

        $totalDiscount = 0;

        foreach ($this->discounts as $key => $discount) {
            foreach($discount as $offer){
                if ($this->stats[$key] >= $offer['threshold']) {
                    $numberOfSets = floor($this->stats[$key] / $offer['threshold']);
                    $totalDiscount += ($offer['amount'] * $numberOfSets);
                }
            }
        }

        return ($totalDiscount > 0) ? $totalDiscount : $standardPrices;
    }
}
