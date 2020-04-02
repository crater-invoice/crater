<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crater\User;
use Crater\Expense;
use Crater\CompanySetting;
use Crater\ExpenseCategory;
use Laravel\Passport\Passport;
use SettingsSeeder;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->seed(SettingsSeeder::class);
        $user = User::find(1);
        $this->withHeaders([
            'company' => $user->company_id,
        ]);
        Passport::actingAs(
            $user,
            ['*']
        );
    }


    /** @test */
    public function testGetExpenses()
    {
        $response = $this->json('GET', 'api/expenses?page=1');

        $response->assertOk();
    }

    /** @test */
    public function testGetCreateExpenseData()
    {
        $response = $this->json('GET', 'api/expenses/create');

        $response->assertOk();
    }

    /** @test */
    public function testCrateExpense()
    {
        $expense = factory(Expense::class)->raw();

        $response = $this->json('POST', 'api/expenses', $expense);

        $expense2 = $response->decodeResponseJson()['expense'];

        $response->assertOk();
        $this->assertEquals($expense['notes'], $expense2['notes']);
        $this->assertEquals($expense['expense_category_id'], $expense2['expense_category_id']);
        $this->assertEquals($expense['amount'], $expense2['amount']);
    }

    /** @test */
    public function testCreateExpenseRequiresExpanseDate()
    {
        $expense = factory(Expense::class)->raw([
            'expense_date' => ''
        ]);

        $response = $this->json('POST', 'api/expenses', $expense);

        $response->assertStatus(422)->assertJsonValidationErrors(['expense_date']);
    }

    /** @test */
    public function testCreateExpenseRequiresAmount()
    {
        $expense = factory(Expense::class)->raw([
            'amount' => ''
        ]);

        $response = $this->json('POST', 'api/expenses', $expense);

        $response->assertStatus(422)->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function testCreateExpenseRequiresCategory()
    {
        $expense = factory(Expense::class)->raw([
            'expense_category_id' => ''
        ]);

        $response = $this->json('POST', 'api/expenses', $expense);

        $response->assertStatus(422)->assertJsonValidationErrors(['expense_category_id']);
    }

    /** @test */
    public function testGetEditExpenseData()
    {
        $expense = factory(Expense::class)->create([
            'expense_date' => '2019-02-05'
        ]);

        $response = $this->json('GET', 'api/expenses/'.$expense->id.'/edit');

        $response->assertOk();
    }

    /** @test */
    public function testUpdateExpense()
    {
        $expense = factory(Expense::class)->create([
            'expense_date' => '2019-02-05'
        ]);

        $expense2 = factory(Expense::class)->raw();

        $response = $this->json('PUT', 'api/expenses/'.$expense->id, $expense2);

        $expense3 = $response->decodeResponseJson()['expense'];

        $response->assertOk();
        $this->assertEquals($expense3['notes'], $expense2['notes']);
        $this->assertEquals($expense3['expense_category_id'], $expense2['expense_category_id']);
        $this->assertEquals($expense3['amount'], $expense2['amount']);
    }

    /** @test */
    public function testUpdateExpenseRequiresExpenseDate()
    {
        $expense = factory(Expense::class)->create([
            'expense_date' => '2019-02-05'
        ]);

        $expense2 = factory(Expense::class)->raw([
            'expense_date' => ''
        ]);

        $response = $this->json('PUT', 'api/expenses/'.$expense->id, $expense2);

        $response->assertStatus(422)->assertJsonValidationErrors(['expense_date']);
    }

    /** @test */
    public function testUpdateExpenseRequiresAmount()
    {
        $expense = factory(Expense::class)->create([
            'expense_date' => '2019-02-05'
        ]);

        $expense2 = factory(Expense::class)->raw([
            'amount' => ''
        ]);

        $response = $this->json('PUT', 'api/expenses/'.$expense->id, $expense2);

        $response->assertStatus(422)->assertJsonValidationErrors(['amount']);
    }

    /** @test */
    public function testUpdateExpenseRequiresCategory()
    {
        $expense = factory(Expense::class)->create([
            'expense_date' => '2019-02-05'
        ]);

        $expense2 = factory(Expense::class)->raw([
            'expense_category_id' => ''
        ]);

        $response = $this->json('PUT', 'api/expenses/'.$expense->id, $expense2);

        $response->assertStatus(422)->assertJsonValidationErrors(['expense_category_id']);
    }

    /** @test */
    public function testDeleteExpenses()
    {
        $expense = factory(Expense::class)->create([

            'expense_date' => '2019-02-05'
        ]);

        $response = $this->json('DELETE', 'api/expenses/'.$expense->id);

        $response->assertOk()
            ->assertJson([
                'success' => true
            ]);
    }

    /** @test */
    public function testSearchExpenses()
    {
        $filters = [
            'page' => 1,
            'limit' => 15,
            'expense_category_id' => 1,
            'search' => 'cate',
            'from_date' => '09/09/2016',
            'to_date' => '10/09/2016'
        ];

        $queryString = http_build_query($filters, '', '&');

        $response = $this->json('GET', 'api/expenses?'.$queryString);

        $response->dump();
        $response->assertOk();
    }

    /** @test */
    public function testDeleteMultipleExpenses()
    {
        $expenses = factory(Expense::class, 3)->create([
            'expense_date' => '2019-02-05'
        ]);

        $ids = $expenses->pluck('id');

        $data = [
            'id' => $ids,
            'type' => 'expense'
        ];

        $response = $this->json('POST', 'api/expenses/delete', $data);

        $response
            ->assertOk()
            ->assertJson([
                'success' => true
            ]);
    }
}
