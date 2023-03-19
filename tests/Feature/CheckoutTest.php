<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Enums\PaymentMethod as PMEnum;
use Illuminate\Testing\Fluent\AssertableJson;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;
    /**
     * POST 401.
     * User must be logged in.
     */
    public function test_checkout_1():void
    {
        $this->postJson('/api/checkout', [])
        ->assertStatus(401);
    }
    /**
     * POST 422.
     * Specific payload required.
     */
    public function test_checkout_2():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [])
        ->assertStatus(422)
        ->assertInvalid(['email', 'full-name', 'tel', 'cart-id', 'payment-method']);
    }
    /**
     * POST 422.
     * Invalid payload provided.
     */
    public function test_checkout_3():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [
            'email'=>'foo@baz.azaza',
            'full-name'=>'F00 1st',
            'tel'=>'kjsdfk3234',
            'cart-id'=>'foo',
            'payment-method'=>'azaza',
        ])
        ->assertStatus(422)
        ->assertInvalid(['email', /*'full-name',*/ 'tel', 'cart-id', 'payment-method']);
    }
    /**
     * POST 201.
     * Payload OK.
     */
    public function test_checkout_4():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [
            'email'=>'test@gmail.com',
            'full-name'=>'Baz Fooman',
            'tel'=>'2101111111',
            'cart-id'=>'1',
            'payment-method'=>PMEnum::OnDelivery->value,
        ])
        ->assertStatus(201)
        ->assertValid(['email', 'full-name', 'tel', 'cart-id', 'payment-method']);
    }
    /**
     * POST 422.
     * Card number is required when payment method is Card.
     */
    public function test_checkout_5():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [
            'email'=>'test@gmail.com',
            'full-name'=>'Baz Fooman',
            'tel'=>'2101111111',
            'cart-id'=>'1',
            'payment-method'=>PMEnum::Card->value,
        ])
        ->assertStatus(422)
        ->assertValid(['email', 'full-name', 'tel', 'cart-id', 'payment-method'])
        ->assertInvalid(['card-number']);
    }
    /**
     * POST 422.
     * Card is invalid.
     */
    public function test_checkout_6():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [
            'email'=>'test@gmail.com',
            'full-name'=>'Baz Fooman',
            'tel'=>'2101111111',
            'cart-id'=>'1',
            'payment-method'=>PMEnum::Card->value,
            'card-number'=>'4242 4242 424',
            'cvv'=>'1234',
            'card-expiration-date'=>'13/10',
        ])
        ->assertStatus(422)
        ->assertValid(['email', 'full-name', 'tel', 'cart-id', 'payment-method'])
        ->assertInvalid(['card-number', 'cvv', 'card-expiration-date']);
    }
    /**
     * POST 422.
     * Expiration date invalid.
     */
    public function test_checkout_7():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $this->postJson('/api/checkout', [
            'email'=>'test@gmail.com',
            'full-name'=>'Baz Fooman',
            'tel'=>'2101111111',
            'cart-id'=>'1',
            'payment-method'=>PMEnum::Card->value,
            'card-number'=>'4242 4242 4242 4242',
            'cvv'=>'123',
            'card-expiration-date'=>'12/21',
        ])
        ->assertStatus(422)
        ->assertValid([
            'email', 
            'full-name', 
            'tel', 
            'cart-id', 
            'payment-method', 
            'card-number', 
            'cvv'
        ])
        ->assertInvalid(['card-expiration-date']);
    }
    /**
     * POST 201.
     * Card is valid.
     */
    public function test_checkout_8():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $r = $this->postJson('/api/checkout', [
            'email'=>'test@gmail.com',
            'full-name'=>'Baz Fooman',
            'tel'=>'2101111111',
            'cart-id'=>'1',
            'payment-method'=>PMEnum::Card->value,
            'card-number'=>'4242 4242 4242 4242',
            'cvv'=>'123',
            'card-expiration-date'=>'12/30',
        ])
        ->assertStatus(201)
        ->assertValid([
            'email', 
            'full-name', 
            'tel', 
            'cart-id', 
            'payment-method', 
            'card-number', 
            'cvv', 
            'card-expiration-date'
        ])
        ->assertJson(fn (AssertableJson $jsonResponse) =>
            $jsonResponse
            ->has('data', fn (AssertableJson $data) =>
                $data
                ->has('id')
                ->has('transaction-id')
                ->where('payment-method', PMEnum::Card->value)
                ->where('amount', 75.5)
                ->where('status', 'TRUE')
                ->where('errors', NULL)
                ->has('created_at')
                ->has('updated_at')
            )
            
        );
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('payments', [
            'payment_method'=>PMEnum::Card->value,
            'amount'=>75.5,
            'status'=>'TRUE',
            'errors'=>NULL,
        ]);
    }
    /**
     * POST 201.
     * Paypal is valid.
     */
    public function test_checkout_9():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $r = $this->postJson('/api/checkout', [
            'email'=>'test@gmail.com',
            'full-name'=>'Baz Fooman',
            'tel'=>'2101111111',
            'cart-id'=>'1',
            'payment-method'=>PMEnum::Paypal->value,
        ])
        ->assertStatus(201)
        ->assertValid([
            'email', 
            'full-name', 
            'tel', 
            'cart-id', 
            'payment-method',
        ])
        ->assertJson(fn (AssertableJson $jsonResponse) =>
            $jsonResponse
            ->has('data', fn (AssertableJson $data) =>
                $data
                ->has('id')
                ->has('transaction-id')
                ->where('payment-method', PMEnum::Paypal->value)
                ->where('amount', 75.5)
                ->where('status', 'TRUE')
                ->where('errors', NULL)
                ->has('created_at')
                ->has('updated_at')
            )
            
        );
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('payments', [
            'payment_method'=>PMEnum::Paypal->value,
            'amount'=>75.5,
            'status'=>'TRUE',
            'errors'=>NULL,
        ]);
    }
    /**
     * POST 201.
     * On Delivery payment is valid.
     */
    public function test_checkout_10():void
    {
        $u = User::factory()->create();

        $this->actingAs($u);
        $r = $this->postJson('/api/checkout', [
            'email'=>'test@gmail.com',
            'full-name'=>'Baz Fooman',
            'tel'=>'2101111111',
            'cart-id'=>'1',
            'payment-method'=>PMEnum::OnDelivery->value,
        ])
        ->assertStatus(201)
        ->assertValid([
            'email', 
            'full-name', 
            'tel', 
            'cart-id', 
            'payment-method',
        ])
        ->assertJson(fn (AssertableJson $jsonResponse) =>
            $jsonResponse
            ->has('data', fn (AssertableJson $data) =>
                $data
                ->has('id')
                ->has('transaction-id')
                ->where('payment-method', PMEnum::OnDelivery->value)
                ->where('amount', 75.5)
                ->where('status', 'PENDING PAYMENT')
                ->where('errors', NULL)
                ->has('created_at')
                ->has('updated_at')
            )
            
        );
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseHas('payments', [
            'payment_method'=>PMEnum::OnDelivery->value,
            'amount'=>75.5,
            'status'=>'PENDING PAYMENT',
            'errors'=>NULL,
        ]);
    }
    /**
     * For more tests regarding errors I would have to mimic behavior of payment processing
     * APIs ( 503 unavailable ) and I would also need fail cases provided. 
     * Due to lack of time this part will be skipped.
     */
}
