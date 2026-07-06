<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use App\Models\User;

class Order extends Model
{
    public const STATUS_PENDING_TRANSFER = 'pendiente_transferencia';
    public const STATUS_PENDING_CLIP = 'pendiente_clip';
    public const STATUS_PAID = 'pagado';
    public const STATUS_REJECTED = 'rechazado';
    public const STATUS_PREPARING = 'preparando';
    public const STATUS_SHIPPED = 'enviado';
    public const STATUS_DELIVERED = 'entregado';
    public const STATUS_CANCELLED = 'cancelado';
    public const STATUS_EXPIRED = 'expirado';

    protected $fillable = [
        'folio',
        'customer_name',
        'customer_email',
        'customer_phone',
        'street',
        'external_number',
        'internal_number',
        'neighborhood',
        'city',
        'state',
        'postal_code',
        'references',
        'subtotal',
        'shipping_cost',
        'discount',
        'total',
        'coupon_code',
        'payment_method',
        'status',
        'customer_notes',
        'internal_notes',
        'tracking_number',
        'rejection_reason',
        'transfer_reference',
        'stock_discounted_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'stock_discounted_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'folio';
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function webhookLogs(): HasMany
    {
        return $this->hasMany(PaymentWebhookLog::class);
    }

    public static function makeFolio(): string
    {
        do {
            $folio = 'GYM-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (self::where('folio', $folio)->exists());

        return $folio;
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING_TRANSFER => 'Pendiente transferencia',
            self::STATUS_PENDING_CLIP => 'Pendiente Clip',
            self::STATUS_PAID => 'Pago recibido',
            self::STATUS_REJECTED => 'Rechazado',
            self::STATUS_PREPARING => 'Preparando',
            self::STATUS_SHIPPED => 'Enviado',
            self::STATUS_DELIVERED => 'Entregado',
            self::STATUS_CANCELLED => 'Cancelado',
            self::STATUS_EXPIRED => 'Expirado',
        ];
    }

    public static function statusesForUser(?User $user, ?self $order = null): array
    {
        if ($user?->isSuperAdmin()) {
            return self::statuses();
        }

        if ($order?->payment_method === 'clip' && $order->status === self::STATUS_PENDING_CLIP) {
            return [
                self::STATUS_PENDING_CLIP => self::statuses()[self::STATUS_PENDING_CLIP],
            ];
        }

        return collect(self::statuses())
            ->only([
                self::STATUS_PAID,
                self::STATUS_PREPARING,
                self::STATUS_SHIPPED,
                self::STATUS_DELIVERED,
            ])
            ->all();
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? Str::headline($this->status);
    }

    /**
     * Estados en los que el pedido sigue pendiente y puede pagarse en linea.
     */
    public function isPayable(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_CLIP,
            self::STATUS_PENDING_TRANSFER,
            self::STATUS_EXPIRED,
        ], true);
    }

    public function transferNumericReference(): string
    {
        return str_pad((string) $this->id, 8, '0', STR_PAD_LEFT);
    }
}
