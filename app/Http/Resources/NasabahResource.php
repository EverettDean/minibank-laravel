<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NasabahResource extends JsonResource
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
            'id'          => $this->id,
            'nama'        => $this->user->name, // Mengambil nama dari relasi user
            'nisn'        => $this->nisn,
            'kelas'       => $this->kelas,
            'jurusan'     => $this->jurusan,
            'saldo'       => (int) $this->saldo_tabungan, // Kirim sebagai integer agar mudah dihitung di HP
            // Kamu bisa tambahkan data lain jika perlu
        ];
    }
}
