{% extends "layouts/main.volt" %}

{% block title %}Login{% endblock %}

{% block content %}
    <h1 class="text-2xl font-bold mb-4">Login with OTP</h1>

    <form x-data="otpLogin()" @submit.prevent="sendOtp" class="space-y-4 max-w-sm">
        <input type="text" name="identifier" x-model="identifier" placeholder="email or phone"
               class="w-full border rounded p-2" required>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded"
                x-text="loading ? 'Sending...' : 'Send OTP'"></button>
        <p x-show="message" x-text="message" class="text-green-600 text-sm"></p>
    </form>

    <script>
        function otpLogin() {
            return {
                identifier: '',
                loading: false,
                message: '',
                sendOtp() {
                    this.loading = true;
                    fetch('/api/v1/auth/send-otp', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({identifier: this.identifier, channel: 'email'})
                    })
                    .then(() => { this.message = 'OTP sent. Check your inbox.'; })
                    .finally(() => { this.loading = false; });
                }
            }
        }
    </script>
{% endblock %}
