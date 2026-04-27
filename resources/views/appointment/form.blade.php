<div class="space-y-6">
    
    <div>
        <x-input-label for="start_time" :value="__('Start Time')"/>
        <x-text-input id="start_time" name="start_time" type="text" class="mt-1 block w-full" :value="old('start_time', $appointment?->start_time)" autocomplete="start_time" placeholder="Start Time"/>
        <x-input-error class="mt-2" :messages="$errors->get('start_time')"/>
    </div>
    <div>
        <x-input-label for="end_time" :value="__('End Time')"/>
        <x-text-input id="end_time" name="end_time" type="text" class="mt-1 block w-full" :value="old('end_time', $appointment?->end_time)" autocomplete="end_time" placeholder="End Time"/>
        <x-input-error class="mt-2" :messages="$errors->get('end_time')"/>
    </div>
    <div>
        <x-input-label for="cancellation_reason" :value="__('Cancellation Reason')"/>
        <x-text-input id="cancellation_reason" name="cancellation_reason" type="text" class="mt-1 block w-full" :value="old('cancellation_reason', $appointment?->cancellation_reason)" autocomplete="cancellation_reason" placeholder="Cancellation Reason"/>
        <x-input-error class="mt-2" :messages="$errors->get('cancellation_reason')"/>
    </div>
    <div>
        <x-input-label for="payment_expires_at" :value="__('Payment Expires At')"/>
        <x-text-input id="payment_expires_at" name="payment_expires_at" type="text" class="mt-1 block w-full" :value="old('payment_expires_at', $appointment?->payment_expires_at)" autocomplete="payment_expires_at" placeholder="Payment Expires At"/>
        <x-input-error class="mt-2" :messages="$errors->get('payment_expires_at')"/>
    </div>
    <div>
        <x-input-label for="notes" :value="__('Notes')"/>
        <x-text-input id="notes" name="notes" type="text" class="mt-1 block w-full" :value="old('notes', $appointment?->notes)" autocomplete="notes" placeholder="Notes"/>
        <x-input-error class="mt-2" :messages="$errors->get('notes')"/>
    </div>
    <div>
        <x-input-label for="cancel_token" :value="__('Cancel Token')"/>
        <x-text-input id="cancel_token" name="cancel_token" type="text" class="mt-1 block w-full" :value="old('cancel_token', $appointment?->cancel_token)" autocomplete="cancel_token" placeholder="Cancel Token"/>
        <x-input-error class="mt-2" :messages="$errors->get('cancel_token')"/>
    </div>
    <div>
        <x-input-label for="cancel_token_expires_at" :value="__('Cancel Token Expires At')"/>
        <x-text-input id="cancel_token_expires_at" name="cancel_token_expires_at" type="text" class="mt-1 block w-full" :value="old('cancel_token_expires_at', $appointment?->cancel_token_expires_at)" autocomplete="cancel_token_expires_at" placeholder="Cancel Token Expires At"/>
        <x-input-error class="mt-2" :messages="$errors->get('cancel_token_expires_at')"/>
    </div>
    <div>
        <x-input-label for="customer_id" :value="__('Customer Id')"/>
        <x-text-input id="customer_id" name="customer_id" type="text" class="mt-1 block w-full" :value="old('customer_id', $appointment?->customer_id)" autocomplete="customer_id" placeholder="Customer Id"/>
        <x-input-error class="mt-2" :messages="$errors->get('customer_id')"/>
    </div>
    <div>
        <x-input-label for="user_id" :value="__('User Id')"/>
        <x-text-input id="user_id" name="user_id" type="text" class="mt-1 block w-full" :value="old('user_id', $appointment?->user_id)" autocomplete="user_id" placeholder="User Id"/>
        <x-input-error class="mt-2" :messages="$errors->get('user_id')"/>
    </div>
    <div>
        <x-input-label for="company_id" :value="__('Company Id')"/>
        <x-text-input id="company_id" name="company_id" type="text" class="mt-1 block w-full" :value="old('company_id', $appointment?->company_id)" autocomplete="company_id" placeholder="Company Id"/>
        <x-input-error class="mt-2" :messages="$errors->get('company_id')"/>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>Submit</x-primary-button>
    </div>
</div>