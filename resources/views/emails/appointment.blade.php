<div style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
  <h1>New Appointment Request</h1>

  <p><strong>First name:</strong> {{ $data['first_name'] }}</p>
  <p><strong>Last name:</strong> {{ $data['last_name'] }}</p>
  <p><strong>Email:</strong> {{ $data['email'] }}</p>
  <p><strong>Phone:</strong> {{ $data['phone'] }}</p>
  <p><strong>Appointment Type:</strong> {{ ucwords(str_replace('_', ' ', $data['appointment_type'])) }}</p>
  <p><strong>Preferred Date:</strong> {{ \Carbon\Carbon::parse($data['appointment_date'])->format('F j, Y') }}</p>
  <p><strong>Preferred Time:</strong> {{ \Carbon\Carbon::parse($data['appointment_time'])->format('g:i A') }}</p>
  
  @if (!empty($data['subject']))
    <p><strong>Subject:</strong> {{ $data['subject'] }}</p>
  @endif

  @if (!empty($data['message']))
    <p><strong>Message:</strong></p>
    <p style="white-space: pre-wrap; margin-top: 0;">{{ $data['message'] }}</p>
  @endif
</div>
