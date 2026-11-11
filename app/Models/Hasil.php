<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hasil extends Model
{
    protected $table = 'hasil';

    protected $fillable = ['alternatif_id', 'nilai_akhir', 'peringkat'];

    protected $casts = ['nilai_akhir' => 'float'];

    public function alternatif(): BelongsTo
    {
        return $this->belongsTo(Alternatif::class);
    }
}
