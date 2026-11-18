<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequestRequest extends FormRequest
{
    /**
     * Tentukan otorisasi permintaan.
     */
    public function authorize(): bool
    {
        return true; // Otorisasi ditangani via Policy/Middleware
    }

    /**
     * Aturan validasi untuk permintaan jasa.
     */
    public function rules(): array
    {
        $rules = [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'budget_min'  => ['nullable', 'numeric', 'min:0'],
            'budget_max'  => ['nullable', 'numeric', 'min:0', 'gte:budget_min'],
            'work_mode'   => ['nullable', 'in:online,onsite,hybrid'],
            'location'    => ['nullable', 'string', 'max:255'],
            'deadline'    => ['nullable', 'date', 'after_or_equal:today'],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['status'] = ['sometimes', 'required', 'in:open,in_progress,completed,cancelled'];
        }

        return $rules;
    }

    /**
     * Pesan kustom validasi dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'title.required'             => 'Judul permintaan jasa wajib diisi.',
            'title.max'                  => 'Judul permintaan maksimal 255 karakter.',
            'description.required'       => 'Deskripsi kebutuhan wajib diisi.',
            'description.min'            => 'Deskripsi kebutuhan minimal 20 karakter agar mudah dipahami.',
            'category_id.exists'         => 'Kategori yang dipilih tidak valid.',
            'budget_min.numeric'         => 'Anggaran minimum harus berupa angka.',
            'budget_min.min'             => 'Anggaran minimum tidak boleh bernilai negatif.',
            'budget_max.numeric'         => 'Anggaran maksimum harus berupa angka.',
            'budget_max.min'             => 'Anggaran maksimum tidak boleh bernilai negatif.',
            'budget_max.gte'             => 'Anggaran maksimum harus lebih besar atau sama dengan anggaran minimum.',
            'work_mode.in'               => 'Mode kerja harus berupa online, onsite, atau hybrid.',
            'deadline.date'              => 'Format tanggal deadline tidak valid.',
            'deadline.after_or_equal'    => 'Batas waktu deadline tidak boleh tanggal di masa lalu.',
            'status.in'                  => 'Status permintaan tidak valid.',
        ];
    }
}

