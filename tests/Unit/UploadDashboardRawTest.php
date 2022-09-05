<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Admin\Coaching\DashboardRaw;

class UploadDashboardRawTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected $connectionsToTransact = ['mysql-coaching', 'mysql-customdb1'];

    private $user;

    protected function setup()
    {
        parent::setup();

        $this->user = factory(User::class)->create();
    }

    public function testUploadDashboard()
    {
        $old_dashboard_count = DashboardRaw::count();
        $file = new UploadedFile(
            'tests/csv' . '/sample-upload-dashboard.csv',
            'sample-upload-dashboard.csv'
        );
        $response = $this->actingAs($this->user)->json(
            'POST',
            route('upload_dashboard'),
            ['attachments' => [$file]],
            [],
            [],
            ['Accept' => 'application/json']
        );
        $response->assertStatus(200);
        $new_dashboard_count = DashboardRaw::count();
        $this->assertNotEquals($old_dashboard_count, $new_dashboard_count);
    }
}
