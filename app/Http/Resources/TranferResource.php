<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TranferResource extends JsonResource
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
            'from' => $this->from->user->name,
            'to' => $this->to->user->name,
            'amount' => $this->from->currency->symbol . $this->amount ,
            'currency' => $this->from->currency->name,
            'created_at' => $this->created_at,
        ];
    }
}
