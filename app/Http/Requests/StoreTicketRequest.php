<?php

namespace App\Http\Requests;

use App\Enums\TicketPriority;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Tout employé connecté peut créer un ticket
    }

    public function rules(): array
    {
        return[
            // Le ticket doit absolument être lié à un matériel existant (Règle 4.5)
            'asset_id'           =>['required', 'exists:assets,id'],
            'ticket_category_id' =>['required', 'exists:ticket_categories,id'],
            'priority'           => ['required', new Enum(TicketPriority::class)],
            'description'        => ['required', 'string', 'min:10'],
        ];
    }
}