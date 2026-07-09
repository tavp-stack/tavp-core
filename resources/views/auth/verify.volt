{% extends "layouts/main.volt" %}

{% block title %}Verify OTP{% endblock %}

{% block content %}
    <h1 class="text-2xl font-bold mb-4">Enter your code</h1>

    <form x-data="otpVerify()" @submit.prevent="verify" class="space-y-4 max-w-sm">
        <input type="hidden" name="identifier" value="{{ identifier }}">
        <div class="flex gap-2">
            <template x-for="i in 6" :key="i">
                <input type="text" maxlength="1" inputmode="numeric"
                       x-model="digits[i-1]" @input="autoAdvance(i)"
                       class="w-12 h-12 text-center border rounded text-xl">
            </template>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded"
                x-text="loading ? 'Verifying...' : 'Verify'"></button>
        <p x-show="error" x-text="error" class="text-red-600 text-sm"></p>
        <p x-show="countdown > 0" x-text="'Resend in ' + countdown + 's'" class="text-sm text-gray-500"></p>
    </form>

    <script>
        function otpVerify() {
            return {
                digits: ['', '', '', '', '', ''],
                loading: false,
                error: '',
                countdown: 0,
                autoAdvance(i) {
                    if (this.digits[i-1].length === 1 && i < 6) {
                        document.querySelectorAll('input')[i].focus();
                    }
                },
                verify() {
                    this.loading = true;
                    const code = this.digits.join('');
                    fetch('/api/v1/auth/verify-otp', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({
                            identifier: document.querySelector('[name=identifier]').value,
                            code: code
                        })
                    })
                    .then(r => r.ok ? window.location = '/dashboard' : Promise.reject())
                    .catch(() => { this.error = 'Invalid code.'; this.loading = false; });
                }
            }
        }
    </script>
{% endblock %}
