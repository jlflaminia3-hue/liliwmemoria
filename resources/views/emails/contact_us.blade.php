<div style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
  <h1>New contact request</h1>

  <p><strong>First name:</strong> {{ $data['first_name'] }}</p>
  @if (!empty($data['last_name']))
    <p><strong>Last name:</strong> {{ $data['last_name'] }}</p>
  @endif
  <p><strong>Email:</strong> {{ $data['email'] }}</p>
  <p><strong>Phone:</strong> {{ $data['phone'] ?? '-' }}</p>
  @if (!empty($data['subject']))
    <p><strong>Subject:</strong> {{ $data['subject'] }}</p>
  @endif
  <p><strong>Reason:</strong> {{ ucwords(str_replace('_', ' ', $data['reason'])) }}</p>

  <p><strong>Message:</strong></p>
  <p style="white-space: pre-wrap; margin-top: 0;">{{ $data['message'] }}</p>
</div>
