<?php

namespace App\Http\Requests\Cbt;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCbtQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_text' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'difficulty' => 'required|in:mudah,sedang,sulit',
            'topic' => 'nullable|string|max:255',
            'competency' => 'nullable|string|max:255',
            'answer_key' => 'nullable|string',
            'question_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'question_audio' => 'nullable|file|mimes:mp3,wav,ogg,m4a|max:10240',
            'question_video' => 'nullable|file|mimes:mp4,webm,ogv|max:40960',
            'remove_question_image' => 'nullable|boolean',
            'remove_question_audio' => 'nullable|boolean',
            'remove_question_video' => 'nullable|boolean',
            'options' => 'nullable|array',
            'options.*.label' => 'required_with:options|string|max:1',
            'options.*.text' => 'required_with:options|string',
            'options.*.is_correct' => 'boolean',
            'options.*.image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'question_text.required' => 'Teks soal wajib diisi.',
            'points.required' => 'Poin wajib diisi.',
            'points.min' => 'Poin minimal 1.',
            'difficulty.required' => 'Tingkat kesulitan wajib dipilih.',
            'question_image.image' => 'File harus berupa gambar.',
            'question_image.max' => 'Gambar soal maksimal 2MB.',
        ];
    }
}
