<?php

namespace Tests\Feature;

use App\Models\Income;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'received_on' => now()->format('Y-m-d'),
            'category' => 'piva',
            'source' => 'PS Company',
            'description' => 'Compenso ottobre',
            'document_number' => '12/2026',
            'gross_amount' => '1602.00',
            'withholding_amount' => '',
            'notes' => null,
        ], $overrides);
    }

    public function test_index_and_create_pages_render(): void
    {
        $this->actingAs($this->admin)->get('/entrate-lavoro')->assertOk()->assertSee('Registro entrate');
        $this->actingAs($this->admin)->get('/entrate-lavoro/create')->assertOk()->assertSee('Salva entrata');
    }

    public function test_admin_can_store_income(): void
    {
        $this->actingAs($this->admin)
            ->post('/entrate-lavoro', $this->payload())
            ->assertRedirect('/entrate-lavoro');

        $income = Income::sole();
        $this->assertSame('PS Company', $income->source);
        $this->assertEquals(1602, (float) $income->gross_amount);
        $this->assertEquals(0, (float) $income->withholding_amount);

        $this->actingAs($this->admin)->get('/entrate-lavoro')
            ->assertOk()
            ->assertSee('PS Company')
            ->assertSee('1.602,00 €');
    }

    public function test_save_and_add_another_redirects_to_create(): void
    {
        $this->actingAs($this->admin)
            ->post('/entrate-lavoro', $this->payload(['add_another' => 1]))
            ->assertRedirect('/entrate-lavoro/create');
    }

    public function test_validation(): void
    {
        $this->actingAs($this->admin)
            ->post('/entrate-lavoro', $this->payload([
                'category' => 'invalid',
                'gross_amount' => '0',
                'source' => '',
            ]))
            ->assertSessionHasErrors(['category', 'gross_amount', 'source']);

        $this->actingAs($this->admin)
            ->post('/entrate-lavoro', $this->payload(['gross_amount' => '100', 'withholding_amount' => '200']))
            ->assertSessionHasErrors(['withholding_amount']);

        $this->assertDatabaseCount('incomes', 0);
    }

    public function test_admin_can_update_and_delete(): void
    {
        $income = Income::create($this->payload(['withholding_amount' => 0]));

        $this->actingAs($this->admin)
            ->put("/entrate-lavoro/{$income->id}", $this->payload([
                'category' => 'occasionale',
                'source' => 'Cliente X',
                'gross_amount' => '1000',
                'withholding_amount' => '200',
            ]))
            ->assertRedirect('/entrate-lavoro');

        $income->refresh();
        $this->assertSame('occasionale', $income->category);
        $this->assertEquals(800, $income->net_amount);

        $this->actingAs($this->admin)->delete("/entrate-lavoro/{$income->id}")->assertRedirect();
        $this->assertDatabaseCount('incomes', 0);
    }

    public function test_filters_by_year_and_category(): void
    {
        Income::create($this->payload(['received_on' => '2025-05-10', 'source' => 'Old Client', 'withholding_amount' => 0]));
        Income::create($this->payload(['received_on' => now()->format('Y-m-d'), 'category' => 'dipendente', 'source' => 'Datore Y', 'withholding_amount' => 0]));

        $this->actingAs($this->admin)->get('/entrate-lavoro')
            ->assertOk()
            ->assertSee('Datore Y')
            ->assertDontSee('Old Client</b>', false);

        $this->actingAs($this->admin)->get('/entrate-lavoro?year=2025')
            ->assertOk()
            ->assertSee('Old Client')
            ->assertDontSee('Datore Y</b>', false);

        $this->actingAs($this->admin)->get('/entrate-lavoro?year=all&category=dipendente')
            ->assertOk()
            ->assertSee('Datore Y')
            ->assertDontSee('Old Client</b>', false);
    }
}
