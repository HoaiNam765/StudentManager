<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\App;

/** Only registered for local environment via routes/fe-preview.php. */
class FePreviewController extends Controller
{
    public function index(): View
    {
        abort_unless(App::environment('local'), 404);
        return view('fe_preview.index', ['screens' => config('fe_preview', [])]);
    }

    public function show(string $screen): View
    {
        abort_unless(App::environment('local'), 404);
        $item = collect(config('fe_preview', []))->firstWhere('key', $screen);
        abort_if($item === null, 404);
        return view($item['view']);
    }
}
