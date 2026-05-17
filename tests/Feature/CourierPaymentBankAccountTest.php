<?php

namespace Tests\Feature;

use App\Models\BankAccount;
use App\Models\Courier;
use App\Models\CourierPayment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierPaymentBankAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_courier_payment_edit_updates_bank_account_and_payment_method_together(): void
    {
        $user = User::factory()->create();
        [$courier, $payment, $order] = $this->makeLinkedCourierPayment();
        $newAccount = BankAccount::create([
            'name' => 'Settlement Account',
            'bank_name' => 'Commercial Bank',
            'account_number' => '1234567890',
            'holder_name' => 'Shoppy Max',
            'type' => 'Bank',
            'is_active' => true,
        ]);

        $this->actingAs($user)->put(route('courier-payments.update', $payment), [
            'courier_id' => $courier->id,
            'amount' => '900.00',
            'bank_account_id' => $newAccount->id,
            'reference_number' => 'CP-EDIT-001',
            'payment_note' => 'Corrected settlement account',
            'order_ids' => [$order->id],
            'courier_costs' => [
                $order->id => '100.00',
            ],
        ])->assertRedirect(route('courier-payments.index'));

        $payment->refresh();

        $this->assertSame($newAccount->id, $payment->bank_account_id);
        $this->assertSame($newAccount->display_label, $payment->payment_method);
    }

    public function test_courier_payment_edit_rejects_inactive_bank_account(): void
    {
        $user = User::factory()->create();
        [$courier, $payment, $order] = $this->makeLinkedCourierPayment();
        $inactiveAccount = BankAccount::create([
            'name' => 'Closed Account',
            'bank_name' => 'Old Bank',
            'account_number' => '9876543210',
            'type' => 'Bank',
            'is_active' => false,
        ]);

        $this->actingAs($user)->from(route('courier-payments.edit', $payment))->put(route('courier-payments.update', $payment), [
            'courier_id' => $courier->id,
            'amount' => '900.00',
            'bank_account_id' => $inactiveAccount->id,
            'reference_number' => null,
            'payment_note' => null,
            'order_ids' => [$order->id],
            'courier_costs' => [
                $order->id => '100.00',
            ],
        ])->assertRedirect(route('courier-payments.edit', $payment))
            ->assertSessionHasErrors('bank_account_id');

        $this->assertNotSame($inactiveAccount->id, $payment->fresh()->bank_account_id);
    }

    private function makeLinkedCourierPayment(): array
    {
        $courier = Courier::create([
            'name' => 'Test Courier',
            'is_active' => true,
        ]);
        $account = BankAccount::create([
            'name' => 'Main Account',
            'bank_name' => 'Main Bank',
            'account_number' => '111222333',
            'type' => 'Bank',
            'is_active' => true,
        ]);
        $payment = CourierPayment::create([
            'courier_id' => $courier->id,
            'amount' => 900,
            'payment_date' => now()->toDateString(),
            'payment_method' => $account->display_label,
            'bank_account_id' => $account->id,
        ]);
        $order = Order::forceCreate([
            'order_number' => 'ORD-'.now()->format('Ymd').'-9001',
            'order_date' => now()->toDateString(),
            'status' => 'confirm',
            'call_status' => 'confirm',
            'delivery_status' => 'delivered',
            'payment_method' => 'COD',
            'payment_status' => 'paid',
            'total_amount' => 1000,
            'paid_amount' => 0,
            'delivery_fee' => 150,
            'courier_charge' => 150,
            'courier_cost' => 100,
            'waybill_number' => 'WB-EDIT-001',
            'courier_id' => $courier->id,
            'courier_payment_id' => $payment->id,
        ]);

        return [$courier, $payment, $order];
    }
}
