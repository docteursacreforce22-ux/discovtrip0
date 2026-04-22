<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'offer' => new OfferResource($this->whenLoaded('offer')),
            'booking_date' => $this->booking_date?->toISOString(),
            'status' => [
                'value' => $this->status,
                'label' => $this->getStatusLabel(),
                'color' => $this->getStatusColor(),
            ],
            'participants' => [
                'adults' => $this->adults,
                'children' => $this->children ?? 0,
                'infants' => $this->infants ?? 0,
                'total' => $this->adults + ($this->children ?? 0) + ($this->infants ?? 0),
                'formatted' => $this->formatParticipants(),
            ],
            'price' => [
                'amount' => (float) $this->total_price,
                'currency' => $this->currency,
                'formatted' => $this->formatPrice(),
            ],
            'cancellation_policy' => [
                'value' => $this->cancellation_policy,
                'label' => $this->getCancellationPolicyLabel(),
            ],
            'is_paid' => (bool) $this->is_paid,
            'paid_at' => $this->paid_at?->toISOString(),
            'special_requests' => $this->special_requests,
            'can_cancel' => $this->canCancel(),
            'can_review' => $this->canReview(),
            'expires_at' => $this->expires_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'cancellation_reason' => $this->cancellation_reason,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }

    private function canCancel(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) 
            && $this->booking_date > now();
    }

    private function canReview(): bool
    {
        return $this->status === 'completed' 
            && $this->booking_date < now();
    }

    private function formatPrice(): string
    {
        $amount = number_format($this->total_price, 0, ',', ' ');
        $symbol = match($this->currency) {
            'XOF', 'XAF' => 'FCFA',
            'EUR' => '€',
            'USD' => '$',
            'GBP' => '£',
            default => $this->currency,
        };
        
        return in_array($this->currency, ['EUR', 'USD', 'GBP']) 
            ? $symbol . ' ' . $amount 
            : $amount . ' ' . $symbol;
    }

    private function formatParticipants(): string
    {
        $parts = [];
        if ($this->adults > 0) $parts[] = $this->adults . ($this->adults > 1 ? ' adultes' : ' adulte');
        if ($this->children > 0) $parts[] = $this->children . ($this->children > 1 ? ' enfants' : ' enfant');
        if ($this->infants > 0) $parts[] = $this->infants . ($this->infants > 1 ? ' bébés' : ' bébé');
        return implode(', ', $parts);
    }

    private function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'En attente de paiement',
            'confirmed' => 'Confirmé',
            'processing' => 'En cours',
            'completed' => 'Terminé',
            'cancelled_by_user' => 'Annulé par vous',
            'cancelled_by_partner' => 'Annulé par le partenaire',
            'cancelled_by_system' => 'Annulé',
            'refunded' => 'Remboursé',
            default => $this->status,
        };
    }

    private function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'green',
            'processing' => 'blue',
            'completed' => 'gray',
            'cancelled_by_user', 'cancelled_by_partner', 'cancelled_by_system' => 'red',
            'refunded' => 'purple',
            default => 'gray',
        };
    }

    private function getCancellationPolicyLabel(): string
    {
        return match($this->cancellation_policy) {
            'flexible' => 'Flexible',
            'moderate' => 'Modérée',
            'strict' => 'Stricte',
            'very_strict' => 'Très stricte',
            'non_refundable' => 'Non remboursable',
            default => $this->cancellation_policy,
        };
    }
}
