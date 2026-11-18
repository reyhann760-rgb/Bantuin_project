<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceOfferRequest extends FormRequest
{
    /**
     * Tentukan otorisasi permintaan.
     */
    public function authorize(): bool
    {
        return true; // Otorisasi ditangani via Policy/Middleware
    }

    /**
     * Aturan validasi untuk penawaran jasa.
     */
    public function rules(): array
    {
        $rules = [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string', 'min:20'],
            'category_id'   => ['nullable', 'exists:categories,id'],
            'price'         => ['nullable', 'numeric', 'min:0'],
            'price_type'    => ['nullable', 'in:fixed,hourly,negotiable'],
            'work_mode'     => ['nullable', 'in:online,onsite,hybrid'],
            'location'      => ['nullable', 'string', 'max:255'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = ['sometimes', 'required', 'in:active,inactive'];
        }

        return $rules;
    }

    /**
     * Pesan kustom validasi dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'title.required'       => 'Judul penawaran jasa wajib diisi.',
            'title.max'            => 'Judul penawaran maksimal 255 karakter.',
            'description.required' => 'Deskripsi layanan wajib diisi.',
            'description.min'      => 'Deskripsi layanan minimal 20 karakter agar jelas bagi pelanggan.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid.',
            'price.numeric'        => 'Harga layanan harus berupa angka.',
            'price.min'            => 'Harga layanan tidak boleh negatif.',
            'price_type.in'        => 'Tipe harga harus berupa harga tetap, per jam, atau bisa nego.',
            'work_mode.in'         => 'Mode kerja harus berupa online, onsite, atau hybrid.',
            'portfolio_url.url'    => 'Link portofolio harus berupa tautan URL yang valid (misal: https://...).',
            'status.in'            => 'Status penawaran jasa tidak valid.',
        ];
    }
}

