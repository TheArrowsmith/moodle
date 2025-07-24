module JwtHelper
  def decode_jwt(token)
    # Decode without verification to inspect the token
    JWT.decode(token, nil, false)
  rescue JWT::DecodeError => e
    raise "Failed to decode JWT: #{e.message}"
  end

  def validate_jwt_format(token)
    expect(token).to match(/^[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+$/)
  end

  def validate_jwt_payload(token, expected_fields = {})
    payload, header = decode_jwt(token)
    
    # Check required fields
    expect(payload).to have_key('user_id')
    expect(payload).to have_key('exp')
    
    # Check expected field values
    expected_fields.each do |field, value|
      expect(payload[field.to_s]).to eq(value)
    end
    
    # Check expiration
    exp_time = Time.at(payload['exp'])
    expect(exp_time).to be > Time.now
    
    payload
  end

  def expired_jwt
    # Create a JWT that expired 1 hour ago
    payload = {
      user_id: 1,
      course_id: 1,
      exp: Time.now.to_i - 3600
    }
    
    # Use a dummy secret since we're just creating an invalid token
    JWT.encode(payload, 'dummy_secret', 'HS256')
  end

  def tampered_jwt(valid_token)
    # Decode the token
    parts = valid_token.split('.')
    
    # Tamper with the payload
    payload = JSON.parse(Base64.decode64(parts[1]))
    payload['user_id'] = 99999
    
    # Re-encode with wrong signature
    tampered_payload = Base64.strict_encode64(payload.to_json).tr('+/', '-_').gsub(/[\n=]/, '')
    
    "#{parts[0]}.#{tampered_payload}.#{parts[2]}"
  end
end