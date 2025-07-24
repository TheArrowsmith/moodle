module ApiHelper
  def api_client
    @api_client ||= ApiClient.new
  end

  def get(path, options = {})
    api_client.get(path, options)
  end

  def post(path, body = {}, options = {})
    api_client.post(path, body, options)
  end

  def put(path, body = {}, options = {})
    api_client.put(path, body, options)
  end

  def delete(path, options = {})
    api_client.delete(path, options)
  end

  # Activity helpers
  def create_activity(params)
    response = post('/activity', params)
    if response.success?
      activity = response.parsed_response
      @created_resources << { type: :activity, id: activity['id'] }
      activity
    else
      raise "Failed to create activity: #{response.code} - #{response.body}"
    end
  end

  def delete_activity(activity_id)
    delete("/activity/#{activity_id}")
  end

  # Course helpers
  def get_course_data(course_id = ENV['TEST_COURSE_ID'])
    get("/course/#{course_id}/management_data")
  end

  # Authentication helpers
  def authenticate_as(user_type = :teacher)
    username = ENV["TEST_#{user_type.upcase}_USERNAME"]
    password = ENV["TEST_#{user_type.upcase}_PASSWORD"]
    
    response = post('/auth/token', {
      username: username,
      password: password
    })

    if response.success?
      token_data = response.parsed_response
      api_client.set_token(token_data['token'])
      token_data
    else
      raise "Authentication failed for #{user_type}: #{response.code} - #{response.body}"
    end
  end

  def use_invalid_token
    api_client.set_token('invalid.token.here')
  end

  def clear_token
    api_client.clear_token
  end
end

class ApiClient
  include HTTParty
  
  def initialize
    @base_uri = ENV['MOODLE_BASE_URL']
    @token = nil
    @debug = ENV['API_DEBUG'] == 'true'
    @timeout = (ENV['API_TIMEOUT'] || 30).to_i
  end

  def set_token(token)
    @token = token
  end

  def clear_token
    @token = nil
  end

  def get(path, options = {})
    request(:get, path, options)
  end

  def post(path, body = {}, options = {})
    options[:body] = body.to_json
    request(:post, path, options)
  end

  def put(path, body = {}, options = {})
    options[:body] = body.to_json
    request(:put, path, options)
  end

  def delete(path, options = {})
    request(:delete, path, options)
  end

  private

  def request(method, path, options = {})
    url = "#{@base_uri}/local/courseapi/api/index.php#{path}"
    
    headers = {
      'Content-Type' => 'application/json',
      'Accept' => 'application/json'
    }
    
    headers['Authorization'] = "Bearer #{@token}" if @token
    
    options[:headers] = headers.merge(options[:headers] || {})
    options[:timeout] = @timeout
    
    if @debug
      puts "\n#{method.upcase} #{url}".blue
      puts "Headers: #{options[:headers]}".blue
      puts "Body: #{options[:body]}".blue if options[:body]
    end

    response = HTTParty.send(method, url, options)
    
    if @debug
      puts "Response #{response.code}: #{response.body}".yellow
    end

    response
  end
end