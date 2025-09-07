<?php

namespace App\Services\Admin\WebSiteSettings;

use App\Models\Term;
use App\Traits\TranslateTrait;

class TermsService
{
    use TranslateTrait;


    function getAll()
    {
        return Term::first();
    }

    function updateSettings($request)
    {
        $data = $request->validated();
        $term = Term::first();
        $data['term_description'] = $this->translate($data['term_description_ar'], $data['term_description_en']);
        $data['policy_description'] = $this->translate($data['policy_description_ar'], $data['policy_description_en']);
        $term->update($data);
        return $term;
    }
}
