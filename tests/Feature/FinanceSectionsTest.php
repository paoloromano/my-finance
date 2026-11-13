<?php

namespace Tests\Feature;

use App\Models\Deadline;
use App\Models\FamilyExpense;
use App\Models\Income;
use App\Models\Movement;
use App\Models\Setting;
use App\Models\User;
use App\Services\FinanceBackup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FinanceSectionsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
        app(FinanceBackup::class)->resetToDefaults();
        $this->actingAs($this->admin);
    }

    public function test_all_pages_render(): void
    {
        foreach (['/', '/entrate-lavoro', '/entrate-lavoro/create', '/partita-iva', '/movimenti', '/famiglia', '/scadenze', '/impostazioni'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_dashboard_shows_defaults(): void
    {
        // 1602 + 1280 + 1770 = 4652
        $this->get('/')->assertOk()->assertSee('4.652 €')->assertSee('F24 piano 2 · rata 1');
    }

    public function test_business_settings_update(): void
    {
        $this->put('/partita-iva', [
            'ps' => 2000, 'abifin' => 1000, 'visio' => 1000,
            'tax_rate' => 25, 'personal_transfer' => 2500, 'tax_reserve' => 7000,
        ])->assertRedirect();

        $s = Setting::values();
        $this->assertEquals(2000, $s['ps']);
        $this->assertEquals(25, $s['tax_rate']);
        $this->get('/partita-iva')->assertSee('4.000 €');
    }

    public function test_movement_crud(): void
    {
        $this->post('/movimenti', [
            'description' => 'Spesa test', 'amount' => '45.50', 'occurred_on' => '2026-11-10',
            'type' => 'expense', 'area' => 'family', 'note' => '',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $m = Movement::sole();
        $this->get('/movimenti')->assertOk()->assertSee('Spesa test')->assertSee('45,50 €');

        $this->put("/movimenti/{$m->id}", [
            'description' => 'Spesa modificata', 'amount' => '50', 'occurred_on' => '2026-11-10',
            'type' => 'expense', 'area' => 'family',
        ])->assertRedirect('/movimenti');
        $this->assertSame('Spesa modificata', $m->fresh()->description);

        $this->delete("/movimenti/{$m->id}")->assertRedirect();
        $this->assertDatabaseCount('movements', 0);
    }

    public function test_movement_validation_uses_dialog_bag(): void
    {
        $this->post('/movimenti', ['description' => '', 'amount' => 0])
            ->assertSessionHasErrorsIn('movement', ['description', 'amount', 'occurred_on', 'type', 'area']);
    }

    public function test_family_expenses(): void
    {
        $this->put('/famiglia/entrate', ['family_income' => 2500])->assertRedirect();
        $this->assertEquals(2500, Setting::values()['family_income']);

        $this->post('/famiglia/spese', ['label' => 'Palestra', 'amount' => 40])->assertRedirect();
        $gym = FamilyExpense::where('label', 'Palestra')->sole();

        $this->put('/famiglia/spese', ['expenses' => [$gym->id => ['label' => 'Palestra bimbi', 'amount' => 55]]])
            ->assertRedirect();
        $this->assertEquals(55, (float) $gym->fresh()->amount);

        $this->delete("/famiglia/spese/{$gym->id}")->assertRedirect();
        $this->assertNull($gym->fresh());
    }

    public function test_deadlines(): void
    {
        $this->assertSame(9, Deadline::count());

        $this->post('/scadenze', ['due_on' => '2027-01-15', 'label' => 'IMU', 'amount' => 300, 'category' => 'Tasse'])
            ->assertRedirect()->assertSessionHasNoErrors();
        $d = Deadline::where('label', 'IMU')->sole();

        $this->patch("/scadenze/{$d->id}/pagata")->assertRedirect();
        $this->assertTrue($d->fresh()->isPaid());
        $this->get('/scadenze')->assertDontSee('IMU');
        $this->get('/scadenze?pagate=1')->assertSee('IMU');

        $this->patch("/scadenze/{$d->id}/pagata");
        $this->assertFalse($d->fresh()->isPaid());

        $this->delete("/scadenze/{$d->id}")->assertRedirect();
        $this->assertSame(9, Deadline::count());
    }

    public function test_backup_export_and_reimport(): void
    {
        Movement::create(['occurred_on' => '2026-11-01', 'description' => 'Test', 'amount' => 10, 'type' => 'income', 'area' => 'business']);
        Income::create(['received_on' => '2026-11-01', 'category' => 'piva', 'source' => 'PS Company', 'gross_amount' => 1602, 'withholding_amount' => 0]);

        $response = $this->get('/impostazioni/backup')->assertOk();
        $json = $response->streamedContent();
        $this->assertStringContainsString('"PS Company"', $json);

        Movement::query()->delete();
        Income::query()->delete();

        $file = UploadedFile::fake()->createWithContent('backup.json', $json);
        $this->post('/impostazioni/backup', ['backup' => $file])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame(1, Movement::count());
        $this->assertSame(1, Income::count());
        $this->assertSame(9, Deadline::count());
    }

    public function test_import_legacy_js_backup(): void
    {
        $legacy = [
            'settings' => ['ps' => 1000, 'abifin' => 500, 'visio' => 500, 'taxRate' => 20, 'personalTransfer' => 2000, 'taxReserve' => 1000, 'familyIncome' => 1800],
            'movements' => [['description' => 'Vecchio', 'amount' => 99, 'date' => '2026-10-01', 'type' => 'expense', 'area' => 'personal', 'note' => '']],
            'deadlines' => [['date' => '2027-01-01', 'label' => 'Rata X', 'amount' => 100, 'category' => 'F24']],
            'familyExpenses' => [['label' => 'Mutuo', 'amount' => 700]],
        ];
        $file = UploadedFile::fake()->createWithContent('my-business-backup.json', json_encode($legacy));

        $this->post('/impostazioni/backup', ['backup' => $file])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertEquals(20, Setting::values()['tax_rate']);
        $this->assertSame('Vecchio', Movement::sole()->description);
        $this->assertSame('Rata X', Deadline::sole()->label);
        $this->assertSame('Mutuo', FamilyExpense::sole()->label);
    }

    public function test_invalid_backup_is_rejected(): void
    {
        $file = UploadedFile::fake()->createWithContent('x.json', json_encode(['movements' => [['description' => 'x']]]));

        $this->post('/impostazioni/backup', ['backup' => $file])->assertSessionHasErrors('backup');
        $this->assertSame(9, Deadline::count());
    }

    public function test_reset_requires_confirmation(): void
    {
        Movement::create(['occurred_on' => '2026-11-01', 'description' => 'Test', 'amount' => 10, 'type' => 'income', 'area' => 'business']);

        $this->post('/impostazioni/ripristina')->assertSessionHasErrors('confirm');
        $this->assertSame(1, Movement::count());

        $this->post('/impostazioni/ripristina', ['confirm' => 1])->assertRedirect();
        $this->assertSame(0, Movement::count());
    }
}
