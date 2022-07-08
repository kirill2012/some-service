<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function response;

class CouponController extends Controller
{

    public function apply(Request $request)
    {
        $coupon = Coupon::where('code', $request->get('code'))
            ->firstOrFail();

        $items = $this->getCalculatedItems($request->get('items'), $coupon->discount);

        return response()->json([
            'items' => $items,
            'code' => $coupon->code
        ]);
    }

    public function generate(Request $request)
    {
        $code = $this->generateCode();

        $coupon = Coupon::create([
            'code' => $code,
            'discount' => $request->get('discount')
        ]);

        return response()->json([
            'code' => $coupon->code
        ]);
    }

    private function generateCode()
    {
        $couponExists = true;

        while ($couponExists) {
            $code = Str::upper(Str::random('7'));
            $couponExists = Coupon::where('code', $code)->exists();
        }

        return $code;
    }

    private function getCalculatedItems($items, $discount)
    {
        $setFree = false;
        $itemsSum = 0;

        foreach ($items as $item) {
            $itemsSum += $item['price'];
        }

        $discountPerc = $discount / $itemsSum;
        if ($discountPerc >= 1) {
            $setFree = true;
        }

        $lastItem = count($items) - 1;
        $discountGived = 0;
        foreach ($items as $key => $item) {
            if($key === $lastItem) {
                $discPrice = $setFree
                    ? 0
                    : $item['price'] - ($discount - $discountGived);
            } else {
                $discPrice = $setFree
                    ? 0
                    : round($item['price'] * (1 - $discountPerc));
            }

            $items[$key]['price_with_discount'] = $discPrice;
            $discountGived += $item['price'] - $discPrice;
        }

        return $items;
    }
}
