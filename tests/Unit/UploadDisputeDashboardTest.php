<?php

namespace Tests\Unit;

use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Admin\Coaching\{DashboardRaw, DisputeDashboard};

class UploadDisputeDashboardTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    protected $connectionsToTransact = ['mysql-coaching', 'mysql-customdb1'];

    private $dashboard_raw;
    private $user;

    protected function setup()
    {
        parent::setup();

        $this->dashboard_raw = factory(DashboardRaw::class)->create();
        $this->user = factory(User::class)->create();
    }

    public function testUploadDisputeDashboard()
    {
        $old_dispute_count = DashboardRaw::where('update_status', 1)->count();
        $old_count_dispute_dashboard = DisputeDashboard::count();
        $file = new UploadedFile(
            'tests/csv' . '/sample-upload-dashboard-dispute.csv',
            'sample-upload-dashboard-dispute.csv'
        );
        $response = $this->actingAs($this->user)->json(
            'POST',
            route('upload_dispute'),
            ['attachments' => [$file]],
            [],
            [],
            ['Accept' => 'application/json']
        );
        $response->assertStatus(200);
        $new_count_dispute_dashboard = DisputeDashboard::count();
        $new_dispute_count = DashboardRaw::where('update_status', 1)->count();
        $this->assertNotEquals($old_dispute_count, $new_dispute_count);
        $this->assertNotEquals($old_count_dispute_dashboard, $new_count_dispute_dashboard);
    }
}
