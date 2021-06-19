<?php

use Crater\Models\Estimate;
use Crater\Models\EstimateItem;
use Crater\Models\Tax;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Artisan::call('db:seed', ['--class' => 'DatabaseSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'DemoSeeder', '--force' => true]);

    $user = User::where('role', 'super admin')->first();
    $this->withHeaders([
        'company' => $user->company_id,
    ]);
    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('estimate has many estimate items', function () {
    $estimate = Estimate::factory()->create();

    $estimate = Estimate::factory()->hasItems(5)->create();

    $this->assertCount(5, $estimate->items);

    $this->assertTrue($estimate->items()->exists());
});

test('estimate belongs to user', function () {
    $estimate = Estimate::factory()->forUser()->create();

    $this->assertTrue($estimate->user()->exists());
});

test('estimate has many taxes', function () {
    $estimate = Estimate::factory()->hasTaxes(5)->create();

    $this->assertCount(5, $estimate->taxes);

    $this->assertTrue($estimate->taxes()->exists());
});

test('get next estimate number', function () {
    $estimate = Estimate::factory()->create();

    $prefix = $estimate->getEstimatePrefixAttribute();

    $nextNumber = $estimate->getNextEstimateNumber($prefix);

    $estimate1 = Estimate::factory()->create();

    $this->assertEquals($prefix.'-'.$nextNumber, $estimate1['estimate_number']);
});

test('get estimate prefix attribute', function () {
    $estimate = Estimate::factory()->create();

    $num = $estimate->getEstimateNumAttribute();

    $prefix = $estimate->getEstimatePrefixAttribute();

    $this->assertEquals($prefix.'-'.$num, $estimate['estimate_number']);
});

test('get estimate num attribute', function () {
    $estimate = Estimate::factory()->create();

    $prefix = $estimate->getEstimatePrefixAttribute();

    $num = $estimate->getEstimateNumAttribute();

    $this->assertEquals($prefix.'-'.$num, $estimate['estimate_number']);
});

test('create estimate', function () {
    $estimate = Estimate::factory()->raw();

    $item = EstimateItem::factory()->raw();

    $estimate['items'] = [];
    array_push($estimate['items'], $item);

    $estimate['taxes'] = [];
    array_push($estimate['taxes'], Tax::factory()->raw());

    $request = new Request();

    $request->replace($estimate);

    $response = Estimate::createEstimate($request);

    $this->assertDatabaseHas('estimate_items', [
        'estimate_id' => $response->id,
        'name' => $item['name'],
        'description' => $item['description'],
        'price' => $item['price'],
        'quantity' => $item['quantity'],
        'total' => $item['total'],
    ]);

    $this->assertDatabaseHas('estimates', [
        'estimate_number' => $estimate['estimate_number'],
        'user_id' => $estimate['user_id'],
        'template_name' => $estimate['template_name'],
        'sub_total' => $estimate['sub_total'],
        'total' => $estimate['total'],
        'discount' => $estimate['discount'],
        'discount_type' => $estimate['discount_type'],
        'discount_val' => $estimate['discount_val'],
        'tax' => $estimate['tax'],
        'notes' => $estimate['notes'],
    ]);
});

test('update estimate', function () {
    $estimate = Estimate::factory()->hasItems()->hasTaxes()->create();

    $newEstimate = Estimate::factory()->raw();

    $item = EstimateItem::factory()->raw([
        'estimate_id' => $estimate->id,
    ]);

    $newEstimate['items'] = [];
    $newEstimate['taxes'] = [];

    array_push($newEstimate['items'], $item);
    array_push($newEstimate['taxes'], Tax::factory()->raw());

    $request = new Request();

    $request->replace($newEstimate);

    $estimate_number = explode("-", $newEstimate['estimate_number']);

    $number_attributes['estimate_number'] = $estimate_number[0].'-'.sprintf('%06d', intval($estimate_number[1]));

    $estimate->updateEstimate($request);

    $this->assertDatabaseHas('estimate_items', [
        'estimate_id' => $estimate->id,
        'name' => $item['name'],
        'description' => $item['description'],
        'price' => $item['price'],
        'total' => $item['total'],
        'quantity' => $item['quantity'],
    ]);

    $this->assertDatabaseHas('estimates', [
        'estimate_number' => $newEstimate['estimate_number'],
        'user_id' => $newEstimate['user_id'],
        'template_name' => $newEstimate['template_name'],
        'sub_total' => $newEstimate['sub_total'],
        'total' => $newEstimate['total'],
        'discount' => $newEstimate['discount'],
        'discount_type' => $newEstimate['discount_type'],
        'discount_val' => $newEstimate['discount_val'],
        'tax' => $newEstimate['tax'],
        'notes' => $newEstimate['notes'],
    ]);
});

test('create items', function () {
    $estimate = Estimate::factory()->create();

    $items = [];

    $item = EstimateItem::factory()->raw([
        'invoice_id' => $estimate->id,
    ]);

    array_push($items, $item);

    $request = new Request();

    $request->replace(['items' => $items ]);

    Estimate::createItems($estimate, $request);

    $this->assertDatabaseHas('estimate_items', [
        'estimate_id' => $estimate->id,
        'description' => $item['description'],
        'price' => $item['price'],
        'tax' => $item['tax'],
        'quantity' => $item['quantity'],
        'total' => $item['total'],
    ]);

    $this->assertCount(1, $estimate->items);
});

test('create taxes', function () {
    $estimate = Estimate::factory()->create();
    $taxes = [];

    $tax1 = Tax::factory()->raw([
        'estimate_id' => $estimate->id,
    ]);

    $tax2 = Tax::factory()->raw([
        'estimate_id' => $estimate->id,
    ]);

    array_push($taxes, $tax1);
    array_push($taxes, $tax2);

    $request = new Request();

    $request->replace(['taxes' => $taxes ]);

    Estimate::createTaxes($estimate, $request);

    $this->assertCount(2, $estimate->taxes);

    $this->assertDatabaseHas('taxes', [
        'estimate_id' => $estimate->id,
        'name' => $tax1['name'],
        'amount' => $tax1['amount'],
    ]);
});
