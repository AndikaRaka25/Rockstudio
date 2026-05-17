<div wire:poll.3s="loadSchedule">
    {{-- Success/Error Messages --}}
    @if($successMessage)
        <div class="alert alert-success">✅ {{ $successMessage }}</div>
    @endif
    @if($errorMessage)
        <div class="alert alert-error">❌ {{ $errorMessage }}</div>
    @endif

    {{-- Studio Selector --}}
    <div class="studio-tabs">
        @foreach($studios as $s)
            <button wire:click="switchStudio({{ $s->id }})" class="studio-tab {{ $s->id === $studioId ? 'active' : '' }}">
                🎵 {{ $s->name }}
            </button>
        @endforeach
    </div>

    {{-- Week Navigator --}}
    <div class="week-navigator">
        <div class="week-nav-controls">
            <button wire:click="previousWeek" class="btn btn-secondary btn-sm btn-icon" title="{{ __('booking.prev_week') }}">◀</button>
            <button wire:click="thisWeek" class="btn btn-secondary btn-sm">{{ __('booking.this_week') }}</button>
            <button wire:click="nextWeek" class="btn btn-secondary btn-sm btn-icon" title="{{ __('booking.next_week') }}">▶</button>
        </div>
        <div class="week-label">{{ $schedule['week_label'] ?? '' }}</div>
        <div>
            <input type="date" wire:change="goToDate($event.target.value)" class="form-input" style="width:auto;padding:6px 12px;font-size:13px;" title="{{ __('booking.select_date') }}">
        </div>
    </div>

    {{-- Schedule Table --}}
    <div class="schedule-container">
        <table class="schedule-table">
            <thead>
                <tr>
                    <th style="min-width:60px;">{{ __('booking.session') }}</th>
                    <th style="min-width:90px;">{{ __('booking.hour') }}</th>
                    @foreach($schedule['days'] ?? [] as $day)
                        <th class="{{ $day['is_today'] ? 'today-header' : '' }}">
                            {{ $day['day_name'] }}<br>
                            <small>{{ $day['formatted_date'] }}</small>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @for($i = 0; $i < 8; $i++)
                    <tr>
                        <td class="session-label">Sesi {{ $i + 1 }}</td>
                        <td class="time-label">
                            {{ $schedule['days'][0]['sessions'][$i]['start_time'] ?? '' }} - {{ $schedule['days'][0]['sessions'][$i]['end_time'] ?? '' }}
                        </td>
                        @foreach($schedule['days'] ?? [] as $day)
                            @php $cell = $day['sessions'][$i] ?? null; @endphp
                            @if($cell)
                                <td class="session-cell {{ $cell['status'] }}"
                                    @if($cell['status'] === 'available')
                                        wire:click="selectSession('{{ $day['date'] }}', {{ $cell['session_number'] }})"
                                        title="{{ __('booking.book_now') }}"
                                    @elseif($cell['status'] === 'own')
                                        title="{{ __('booking.own') }}"
                                        onclick="window.location='{{ route('renter.my-bookings') }}'"
                                    @endif
                                >
                                    @if($cell['display_name'])
                                        <div class="cell-name">{{ $cell['display_name'] }}</div>
                                    @endif
                                    @if($cell['status'] === 'pending')
                                        <div class="cell-status">🔒 {{ __('booking.pending') }}</div>
                                    @elseif($cell['status'] === 'own')
                                        <div class="cell-status">⭐ Booking Anda</div>
                                    @elseif($cell['status'] === 'past')
                                        <div class="cell-status" style="opacity:0.6;font-size:12px;">❌ Berlalu</div>
                                    @elseif($cell['status'] === 'available' && !$day['is_past'])
                                        <div class="cell-status" style="opacity:0.4;">{{ __('booking.available') }}</div>
                                    @endif
                                </td>
                            @endif
                        @endforeach
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    {{-- Legend --}}
    <div class="schedule-legend">
        <div class="legend-item"><div class="legend-dot available"></div> {{ __('booking.available') }}</div>
        <div class="legend-item"><div class="legend-dot pending"></div> {{ __('booking.pending') }}</div>
        <div class="legend-item"><div class="legend-dot confirmed"></div> {{ __('booking.confirmed') }}</div>
        <div class="legend-item"><div class="legend-dot own"></div> {{ __('booking.own') }}</div>
        <div class="legend-item"><div class="legend-dot past"></div> {{ __('booking.past') }}</div>
    </div>

    {{-- Booking Modal --}}
    @if($showBookingModal)
        <div class="modal-overlay" wire:click.self="closeModal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">
                        {{ $showPaymentStep ? __('booking.payment_title') : __('booking.form_title') }}
                    </h3>
                    <button class="modal-close" wire:click="closeModal">✕</button>
                </div>
                <div class="modal-body">
                    @if($showPaymentStep)
                        {{-- Timer --}}
                        <div class="timer-bar" x-data="{ seconds: @entangle('remainingSeconds') }" x-init="
                            let timer = setInterval(() => {
                                if(seconds > 0) { seconds--; } else { clearInterval(timer); $wire.lockExpired(); }
                            }, 1000);
                        ">
                            <span>⏱️ {{ __('booking.time_remaining') }}</span>
                            <span class="timer-value" x-text="Math.floor(seconds/60) + ':' + String(seconds%60).padStart(2,'0')"></span>
                        </div>

                        {{-- Booking Summary --}}
                        <div class="booking-summary">
                            <div class="summary-row"><span class="label">{{ __('booking.studio_name') }}</span><span class="value">{{ $studio->name }}</span></div>
                            <div class="summary-row"><span class="label">{{ __('booking.booking_date') }}</span><span class="value">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d M Y') }}</span></div>
                            <div class="summary-row"><span class="label">{{ __('booking.booking_session') }}</span><span class="value">Sesi {{ $selectedSession }}</span></div>
                            <div class="summary-row"><span class="label">{{ __('booking.total_price') }}</span><span class="value">Rp {{ number_format($studio->price_per_session, 0, ',', '.') }}</span></div>
                            <div class="summary-row summary-total"><span class="label">{{ __('booking.dp_amount') }}</span><span class="value" style="color:var(--color-primary-dark);">Rp {{ number_format($studio->dp_amount, 0, ',', '.') }}</span></div>
                            <div class="summary-row"><span class="label">{{ __('booking.remaining') }}</span><span class="value">Rp {{ number_format($studio->price_per_session - $studio->dp_amount, 0, ',', '.') }}</span></div>
                        </div>

                        <p style="font-size:13px;color:var(--color-text-secondary);margin-bottom:16px;">{{ __('booking.no_refund_policy') }}</p>

                        {{-- Pay Button (Dummy Flow for Testing) --}}
                        @if($snapToken)
                            <div style="display:flex;gap:10px;margin-top:20px;">
                                <button class="btn btn-primary" style="flex:1;justify-content:center;background:#10B981;color:#fff;" wire:click="simulatePayment('qris')">
                                    📱 Simulate QRIS
                                </button>
                                <button class="btn btn-primary" style="flex:1;justify-content:center;background:#3B82F6;color:#fff;" wire:click="simulatePayment('bank_transfer')">
                                    🏦 Simulate Bank VA
                                </button>
                            </div>
                            <p style="text-align:center;font-size:12px;color:var(--color-text-secondary);margin-top:8px;">*Mode testing: Bypassing Midtrans API</p>
                        @endif
                    @else
                        {{-- Booking Form --}}
                        <div class="booking-summary" style="margin-bottom:20px;">
                            <div class="summary-row"><span class="label">{{ __('booking.studio_name') }}</span><span class="value">{{ $studio->name }}</span></div>
                            <div class="summary-row"><span class="label">{{ __('booking.booking_date') }}</span><span class="value">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d M Y') }}</span></div>
                            <div class="summary-row"><span class="label">{{ __('booking.booking_session') }}</span><span class="value">Sesi {{ $selectedSession }}</span></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('booking.booker_name') }} *</label>
                            <input type="text" wire:model="bookerName" class="form-input" required>
                            @error('bookerName') <div class="error-text">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('booking.booker_phone') }} *</label>
                            <input type="text" wire:model="bookerPhone" class="form-input" required>
                            @error('bookerPhone') <div class="error-text">{{ $message }}</div> @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('booking.band_name') }}</label>
                            <input type="text" wire:model="bandName" class="form-input" placeholder="{{ __('booking.band_name_placeholder') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">{{ __('booking.notes') }}</label>
                            <textarea wire:model="notes" class="form-textarea" rows="2" placeholder="{{ __('booking.notes_placeholder') }}"></textarea>
                        </div>
                    @endif
                </div>
                @if(!$showPaymentStep)
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="closeModal">{{ __('messages.cancel') }}</button>
                        <button class="btn btn-primary" wire:click="lockAndBook">🔒 {{ __('booking.book_now') }}</button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

@script
<script>
    window.payWithMidtrans = function(token) {
        if (typeof window.snap !== 'undefined') {
            window.snap.pay(token, {
                onSuccess: function(result) { $wire.paymentSuccess(result.order_id); },
                onPending: function(result) { $wire.paymentPending(); },
                onError: function(result) { alert('Payment failed. Please try again.'); },
                onClose: function() { /* User closed popup - lock still active */ }
            });
        } else {
            alert('Payment system not loaded. Please refresh the page.');
        }
    };
</script>
@endscript
