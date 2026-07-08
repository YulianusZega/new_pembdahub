<?php

namespace App\Http\Requests\Cbt;

use Illuminate\Foundation\Http\FormRequest;

class StoreCbtQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller
    }

    public function rules(): array
    {
        return [
            'question_type' => 'required|in:multiple_choice,true_false,essay,fill_blank',
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
            'options' => 'nullable|array',
            'options.*.label' => 'required_with:options|string|max:1',
            'options.*.text' => 'required_with:options|string',
            'options.*.is_correct' => 'boolean',
            'options.*.image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'tf_options' => 'nullable|array',
            'tf_options.*.label' => 'required_with:tf_options|string|max:1',
            'tf_options.*.text' => 'required_with:tf_options|string',
            'tf_options.*.is_correct' => 'boolean',
            'tf_answer' => 'nullable|in:true,false',
        ];
    }

    public function messages(): array
    {
        return [
            'question_type.required' => 'Tipe soal wajib dipilih.',
            'question_type.in' => 'Tipe soal tidak valid.',
            'question_text.required' => 'Teks soal wajib diisi.',
            'points.required' => 'Poin wajib diisi.',
            'points.min' => 'Poin minimal 1.',
            'difficulty.required' => 'Tingkat kesulitan wajib dipilih.',
            'question_image.image' => 'File harus berupa gambar.',
            'question_image.max' => 'Gambar soal maksimal 2MB.',
            'question_audio.max' => 'Audio soal maksimal 10MB.',
            'question_video.max' => 'Video soal maksimal 40MB.',
        ];
    }
}
