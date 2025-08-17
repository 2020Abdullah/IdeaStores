<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphOne;

trait HasBalance
{
    /**
     * ربط الحساب العام
     */
    public function account(): MorphOne
    {
        return $this->morphOne(\App\Models\Account::class, 'accountable');
    }

    /**
     * إرجاع جميع الحركات الخاصة بالحساب
     */
    public function transactions()
    {
        return $this->account
            ? $this->account->transactions()
            : collect(); // لو مفيش حساب بيرجع كولكشن فاضي
    }

    /**
     * الرصيد الحالي = مجموع الحركات (in موجب / out سالب)
     */
    public function getBalanceAttribute(): float
    {
        if (!$this->account) {
            return 0;
        }

        return $this->transactions()
            ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'in' THEN amount ELSE -amount END),0) as balance")
            ->value('balance') ?? 0;
    }

    /**
     * رصيد حتى تاريخ معين
     */
    public function balanceUntil($date): float
    {
        if (!$this->account) {
            return 0;
        }

        return $this->transactions()
            ->whereDate('date', '<=', $date)
            ->selectRaw("COALESCE(SUM(CASE WHEN direction = 'in' THEN amount ELSE -amount END),0) as balance")
            ->value('balance') ?? 0;
    }
}
