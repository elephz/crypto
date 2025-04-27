<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'seller' => $this->user->name,
            'buyer' => $this->salesOrder->map(fn ($order) => [
                'name' => $order->user->name,
                'amount' => $order->amount,
                'price' => $order->price,
            ]),
            'currency' => $this->currency->name,
            'total' => $this->amount_total,
            'available' => (string)($this->amount_available - $this->amount_locked < 0 ? 0 : $this->amount_available - $this->amount_locked),
            'created_at' => $this->created_at,
        ];
    }
}
