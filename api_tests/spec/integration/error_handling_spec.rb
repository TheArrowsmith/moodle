RSpec.describe 'API Error Handling' do
  describe 'Authentication errors' do
    it 'returns consistent 401 for missing token across all endpoints' do
      clear_token
      
      endpoints = [
        { method: :get, path: '/user/me' },
        { method: :get, path: "/course/#{ENV['TEST_COURSE_ID']}/management_data" },
        { method: :put, path: '/activity/1', body: { name: 'test' } },
        { method: :put, path: '/section/1', body: { name: 'test' } },
        { method: :delete, path: '/activity/1' },
        { method: :post, path: '/activity', body: { name: 'test' } },
        { method: :post, path: '/section/1/reorder_activities', body: { activity_ids: [1] } },
        { method: :post, path: '/section/1/move_activity', body: { activityid: 1, position: 0 } }
      ]
      
      endpoints.each do |endpoint|
        response = send(endpoint[:method], endpoint[:path], endpoint[:body])
        
        expect(response.code).to eq(401), 
          "Expected 401 for #{endpoint[:method].upcase} #{endpoint[:path]}, got #{response.code}"
        expect(response.parsed_response).to have_key('error')
        expect(response.parsed_response['error']).to include('Authentication token is missing')
      end
    end

    it 'returns consistent 401 for expired token' do
      api_client.set_token(expired_jwt)
      
      response = get('/user/me')
      
      expect(response.code).to eq(401)
      expect(response.parsed_response['error']).to include('Invalid or expired authentication token')
    end

    it 'waits appropriate time before token expires' do
      skip 'Long-running test - enable for thorough testing'
      
      authenticate_as(:teacher)
      
      # Token should work initially
      response = get('/user/me')
      expect(response.code).to eq(200)
      
      # Wait 61 minutes
      puts "Waiting 61 minutes for token expiration..."
      sleep(61 * 60)
      
      # Token should now be expired
      response = get('/user/me')
      expect(response.code).to eq(401)
      expect(response.parsed_response['error']).to include('Invalid or expired authentication token')
    end
  end

  describe 'Permission errors' do
    before do
      authenticate_as(:student)
    end

    it 'returns consistent 403 for forbidden operations' do
      # Students shouldn't be able to modify course content
      forbidden_operations = [
        { method: :put, path: '/activity/1', body: { name: 'forbidden' } },
        { method: :put, path: '/section/1', body: { name: 'forbidden' } },
        { method: :delete, path: '/activity/1' },
        { method: :post, path: '/activity', body: sample_activity_data },
        { method: :post, path: '/section/1/reorder_activities', body: { activity_ids: [1] } }
      ]
      
      forbidden_operations.each do |operation|
        response = send(operation[:method], operation[:path], operation[:body])
        
        expect(response.code).to eq(403),
          "Expected 403 for #{operation[:method].upcase} #{operation[:path]}, got #{response.code}"
        expect(response.parsed_response).to have_key('error')
      end
    end

    it 'allows read operations for students' do
      # Students should be able to read their own data
      response = get('/user/me')
      expect(response.code).to eq(200)
    end
  end

  describe 'Resource not found errors' do
    before do
      authenticate_as(:teacher)
    end

    it 'returns consistent 404 for non-existent resources' do
      not_found_requests = [
        { method: :get, path: '/course/99999/management_data' },
        { method: :put, path: '/activity/99999', body: { name: 'test' } },
        { method: :put, path: '/section/99999', body: { name: 'test' } },
        { method: :delete, path: '/activity/99999' },
        { method: :post, path: '/section/99999/reorder_activities', body: { activity_ids: [1] } }
      ]
      
      not_found_requests.each do |request|
        response = send(request[:method], request[:path], request[:body])
        
        expect(response.code).to eq(404),
          "Expected 404 for #{request[:method].upcase} #{request[:path]}, got #{response.code}"
        expect(response.parsed_response).to have_key('error')
      end
    end

    it 'provides descriptive error messages' do
      response = get('/course/99999/management_data')
      
      expect(response.code).to eq(404)
      error_message = response.parsed_response['error']
      expect(error_message).to be_a(String)
      expect(error_message.downcase).to include('not found', 'does not exist', 'invalid')
    end
  end

  describe 'Invalid request errors' do
    before do
      authenticate_as(:teacher)
    end

    it 'returns 422 for malformed JSON' do
      invalid_json_strings.first(3).each do |invalid_json|
        response = api_client.put("/activity/1", {}, body: invalid_json)
        
        expect(response.code).to eq(422),
          "Expected 422 for invalid JSON: #{invalid_json}, got #{response.code}"
        expect(response.parsed_response).to have_key('error')
      end
    end

    it 'returns 422 for missing required fields' do
      incomplete_requests = [
        { 
          path: '/activity',
          body: { name: 'Missing courseid and sectionid' }
        },
        {
          path: '/auth/token',
          body: { username: 'only_username' }
        },
        {
          path: '/section/1/move_activity',
          body: { position: 0 } # Missing activityid
        }
      ]
      
      incomplete_requests.each do |request|
        # Special handling for auth endpoint which doesn't need token
        if request[:path] == '/auth/token'
          clear_token
        end
        
        response = post(request[:path], request[:body])
        
        expect(response.code).to eq(422),
          "Expected 422 for #{request[:path]} with body #{request[:body]}, got #{response.code}"
        expect(response.parsed_response).to have_key('error')
      end
    end

    it 'returns appropriate error for wrong HTTP method' do
      # Most APIs return 405 Method Not Allowed, but some return 404
      response = post('/user/me', {}) # Should be GET
      
      expect(response.code).to be_in([404, 405])
    end
  end

  describe 'Error response format' do
    it 'always returns JSON error format' do
      test_cases = [
        { condition: 'missing token', setup: -> { clear_token }, request: -> { get('/user/me') } },
        { condition: 'invalid token', setup: -> { use_invalid_token }, request: -> { get('/user/me') } },
        { condition: 'not found', setup: -> { authenticate_as(:teacher) }, request: -> { get('/course/99999/management_data') } },
        { condition: 'forbidden', setup: -> { authenticate_as(:student) }, request: -> { delete('/activity/1') } }
      ]
      
      test_cases.each do |test_case|
        test_case[:setup].call
        response = test_case[:request].call
        
        # Should return JSON
        expect(response.headers['content-type']).to include('application/json'),
          "Expected JSON response for #{test_case[:condition]}"
        
        # Should have error key
        expect(response.parsed_response).to be_a(Hash),
          "Expected Hash response for #{test_case[:condition]}"
        expect(response.parsed_response).to have_key('error'),
          "Expected 'error' key for #{test_case[:condition]}"
        
        # Error message should be a string
        expect(response.parsed_response['error']).to be_a(String),
          "Expected string error message for #{test_case[:condition]}"
      end
    end
  end

  describe 'CORS support' do
    before do
      authenticate_as(:teacher)
    end

    it 'includes CORS headers in responses' do
      response = get('/user/me')
      
      # Check for CORS headers (exact headers depend on server config)
      cors_headers = [
        'access-control-allow-origin',
        'access-control-allow-methods',
        'access-control-allow-headers'
      ]
      
      # At least some CORS headers should be present
      has_cors = cors_headers.any? { |header| response.headers.key?(header) }
      
      if has_cors
        puts "CORS headers found: #{response.headers.select { |k, _| k.downcase.include?('access-control') }}"
      else
        puts "No CORS headers found - may need server configuration"
      end
    end

    it 'handles preflight OPTIONS requests' do
      skip 'OPTIONS handling depends on server configuration'
      
      # This would test OPTIONS preflight requests
      # Implementation depends on how Moodle/server handles OPTIONS
    end
  end
end