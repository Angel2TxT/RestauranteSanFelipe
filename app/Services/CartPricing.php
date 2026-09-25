<?php

namespace App\Services;

use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Collection;

class CartPricing
{
    public static function instance(): string
    {
        return 'shopping';
    }

    /**
     * Recalcula precios del carrito desde la BD (fuente de verdad).
     */
    public static function syncFromDatabase(): void
    {
        $cart = Cart::instance(self::instance());

        foreach ($cart->content() as $row) {
            $product = Product::query()->find($row->id);
            if (!$product) {
                $cart->remove($row->rowId);
                continue;
            }

            $cart->update($row->rowId, [
                'name' => $product->name,
                'price' => (float) $product->price,
                'qty' => max(1, (int) $row->qty),
                'options' => [
                    'image' => $product->image,
                ],
            ]);
        }
    }

    /**
     * @return array{lines: Collection, items_count: int, qty_total: int, total: float}
     */
    public static function summarize(): array
    {
        self::syncFromDatabase();

        $cart = Cart::instance(self::instance());
        $lines = collect();
        $total = 0.0;
        $qtyTotal = 0;

        foreach ($cart->content() as $row) {
            $unit = round((float) $row->price, 2);
            $qty = max(1, (int) $row->qty);
            $subtotal = round($unit * $qty, 2);
            $total += $subtotal;
            $qtyTotal += $qty;

            $lines->push([
                'row_id' => $row->rowId,
                'product_id' => (int) $row->id,
                'name' => $row->name,
                'image' => $row->options->image ?? null,
                'qty' => $qty,
                'unit_price' => $unit,
                'subtotal' => $subtotal,
            ]);
        }

        return [
            'lines' => $lines,
            'items_count' => $lines->count(),
            'qty_total' => $qtyTotal,
            'total' => round($total, 2),
        ];
    }

    public static function formattedTotal(): string
    {
        return number_format(self::summarize()['total'], 2);
    }
}
