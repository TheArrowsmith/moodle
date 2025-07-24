RSpec.describe 'JWT Token Management' do
  describe 'POST /auth/token' do
    context 'with valid credentials' do
      it 'returns a valid JWT token for teacher' do
        response = post('/auth/token', {
          username: ENV['TEST_TEACHER_USERNAME'],
          password: ENV['TEST_TEACHER_PASSWORD'],
          course_id: ENV['TEST_COURSE_ID'].to_i
        })

        expect(response.code).to eq(200)
        
        body = response.parsed_response
        expect(body).to have_key('token')
        expect(body).to have_key('expires_in')
        expect(body).to have_key('user')
        
        # Validate JWT format
        validate_jwt_format(body['token'])
        
        # Validate user data
        user = body['user']
        expect(user).to have_key('id')
        expect(user).to have_key('username')
        expect(user['username']).to eq(ENV['TEST_TEACHER_USERNAME'])
      end

      it 'returns a valid JWT token for admin' do
        response = post('/auth/token', {
          username: ENV['TEST_ADMIN_USERNAME'],
          password: ENV['TEST_ADMIN_PASSWORD']
        })

        expect(response.code).to eq(200)
        
        body = response.parsed_response
        validate_jwt_format(body['token'])
        
        # Admin token without specific course_id should still work
        expect(body['user']['username']).to eq(ENV['TEST_ADMIN_USERNAME'])
      end

      it 'includes correct JWT payload fields' do
        response = post('/auth/token', {
          username: ENV['TEST_TEACHER_USERNAME'],
          password: ENV['TEST_TEACHER_PASSWORD'],
          course_id: ENV['TEST_COURSE_ID'].to_i
        })

        token = response.parsed_response['token']
        payload = validate_jwt_payload(token)
        
        expect(payload).to have_key('user_id')
        expect(payload).to have_key('course_id')
        expect(payload['course_id']).to eq(ENV['TEST_COURSE_ID'].to_i)
        
        # Check expiration is approximately 60 minutes from now
        exp_time = Time.at(payload['exp'])
        time_diff = exp_time - Time.now
        expect(time_diff).to be_between(3500, 3700) # ~60 minutes
      end
    end

    context 'with invalid credentials' do
      it 'returns 401 for wrong password' do
        response = post('/auth/token', {
          username: ENV['TEST_TEACHER_USERNAME'],
          password: 'WrongPassword123!'
        })

        expect(response.code).to eq(401)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 401 for non-existent user' do
        response = post('/auth/token', {
          username: 'nonexistent_user',
          password: 'Password123!'
        })

        expect(response.code).to eq(401)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 403 for valid user without course access' do
        skip 'Requires a user without access to test course'
        
        # This would test the scenario where credentials are valid
        # but the user doesn't have access to the specified course
      end
    end

    context 'with missing parameters' do
      it 'returns 422 when username is missing' do
        response = post('/auth/token', {
          password: ENV['TEST_TEACHER_PASSWORD']
        })

        expect(response.code).to eq(422)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 422 when password is missing' do
        response = post('/auth/token', {
          username: ENV['TEST_TEACHER_USERNAME']
        })

        expect(response.code).to eq(422)
        expect(response.parsed_response).to have_key('error')
      end
    end
  end

  describe 'Token validation on protected endpoints' do
    before do
      # Get a valid token
      auth_response = authenticate_as(:teacher)
      @valid_token = auth_response['token']
    end

    it 'accepts valid tokens' do
      response = get('/user/me')
      expect(response.code).to eq(200)
    end

    it 'rejects requests without token' do
      clear_token
      response = get('/user/me')
      
      expect(response.code).to eq(401)
      expect(response.parsed_response['error']).to include('Authentication token is missing')
    end

    it 'rejects invalid tokens' do
      use_invalid_token
      response = get('/user/me')
      
      expect(response.code).to eq(401)
      expect(response.parsed_response['error']).to include('Invalid or expired authentication token')
    end

    it 'rejects expired tokens' do
      api_client.set_token(expired_jwt)
      response = get('/user/me')
      
      expect(response.code).to eq(401)
      expect(response.parsed_response['error']).to include('Invalid or expired authentication token')
    end

    it 'rejects tampered tokens' do
      api_client.set_token(tampered_jwt(@valid_token))
      response = get('/user/me')
      
      expect(response.code).to eq(401)
      expect(response.parsed_response['error']).to include('Invalid or expired authentication token')
    end
  end
end