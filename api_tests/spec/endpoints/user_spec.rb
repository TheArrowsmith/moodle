RSpec.describe 'User API Endpoints' do
  describe 'GET /user/me' do
    context 'when authenticated' do
      before do
        authenticate_as(:teacher)
      end

      it 'returns current user information' do
        response = get('/user/me')
        
        expect(response.code).to eq(200)
        
        user = response.parsed_response
        expect(user).to have_key('id')
        expect(user).to have_key('username')
        expect(user).to have_key('firstname')
        expect(user).to have_key('lastname')
        
        expect(user['username']).to eq(ENV['TEST_TEACHER_USERNAME'])
      end

      it 'returns consistent user data across multiple requests' do
        response1 = get('/user/me')
        response2 = get('/user/me')
        
        expect(response1.code).to eq(200)
        expect(response2.code).to eq(200)
        expect(response1.parsed_response).to eq(response2.parsed_response)
      end
    end

    context 'when authenticated as different users' do
      it 'returns correct data for teacher' do
        authenticate_as(:teacher)
        response = get('/user/me')
        
        expect(response.code).to eq(200)
        expect(response.parsed_response['username']).to eq(ENV['TEST_TEACHER_USERNAME'])
      end

      it 'returns correct data for admin' do
        authenticate_as(:admin)
        response = get('/user/me')
        
        expect(response.code).to eq(200)
        expect(response.parsed_response['username']).to eq(ENV['TEST_ADMIN_USERNAME'])
      end

      it 'returns correct data for student' do
        authenticate_as(:student)
        response = get('/user/me')
        
        expect(response.code).to eq(200)
        expect(response.parsed_response['username']).to eq(ENV['TEST_STUDENT_USERNAME'])
      end
    end

    context 'error handling' do
      it 'returns 401 when not authenticated' do
        clear_token
        response = get('/user/me')
        
        expect(response.code).to eq(401)
        expect(response.parsed_response).to have_key('error')
        expect(response.parsed_response['error']).to include('Authentication token is missing')
      end

      it 'returns 401 with invalid token' do
        use_invalid_token
        response = get('/user/me')
        
        expect(response.code).to eq(401)
        expect(response.parsed_response).to have_key('error')
        expect(response.parsed_response['error']).to include('Invalid or expired authentication token')
      end

      it 'returns 401 with expired token' do
        api_client.set_token(expired_jwt)
        response = get('/user/me')
        
        expect(response.code).to eq(401)
        expect(response.parsed_response).to have_key('error')
        expect(response.parsed_response['error']).to include('Invalid or expired authentication token')
      end
    end
  end
end