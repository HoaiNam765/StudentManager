<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/** Smoke test for the 88 local-only Stitch mock screens. */
class FePreviewRoutesTest extends TestCase
{
    public function test_every_mock_preview_route_returns_http_200(): void
    {
        // The preview routes are intentionally absent in the normal testing
        // environment. Register them explicitly for this local-only smoke test.
        // Ép môi trường thành local trong phạm vi ứng dụng test.
        // Không thay đổi APP_ENV hoặc cấu hình production.
        $this->app->instance('env', 'local');

        $this->assertTrue(
            $this->app->environment('local'),
            'Preview smoke test requires local environment.'
        );

        require base_path('routes/fe-preview.php');

        Route::getRoutes()->refreshNameLookups();

        $screens = config('fe_preview', []);
        $this->assertCount(88, $screens, 'The preview manifest should list 88 screens.');
        $this->assertTrue(Route::has('fe.preview.index'));
        $this->get(route('fe.preview.index'))->assertOk();

        foreach ($screens as $screen) {
            $this->assertTrue(Route::has($screen['route']), $screen['route'].' not registered');
            $this->get(route($screen['route']))->assertOk();
        }
    }
}
