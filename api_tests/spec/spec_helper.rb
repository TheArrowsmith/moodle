require 'bundler/setup'
require 'rspec'
require 'rspec/retry'
require 'httparty'
require 'jwt'
require 'dotenv'
require 'faker'
require 'colorize'
require 'json'

# Load environment variables
Dotenv.load('.env', '.env.local')

# Load support files
Dir[File.join(__dir__, 'support', '**', '*.rb')].each { |f| require f }

RSpec.configure do |config|
  # Enable flags like --only-failures and --next-failure
  config.example_status_persistence_file_path = ".rspec_status"

  # Disable RSpec exposing methods globally on `Module` and `main`
  config.disable_monkey_patching!

  config.expect_with :rspec do |c|
    c.syntax = :expect
  end

  # Retry configuration for flaky tests
  config.verbose_retry = true
  config.display_try_failure_messages = true
  config.default_retry_count = 2
  config.exceptions_to_retry = [Net::ReadTimeout, HTTParty::Error]

  # Include helpers
  config.include ApiHelper
  config.include JwtHelper
  config.include TestData

  # Before hooks
  config.before(:suite) do
    puts "\nRunning API tests against: #{ENV['MOODLE_BASE_URL']}".green
    puts "Using course ID: #{ENV['TEST_COURSE_ID']}\n".green
    
    # Verify environment variables are set
    required_env_vars = %w[
      MOODLE_BASE_URL
      TEST_ADMIN_USERNAME
      TEST_ADMIN_PASSWORD
      TEST_COURSE_ID
    ]
    
    missing_vars = required_env_vars.select { |var| ENV[var].nil? || ENV[var].empty? }
    if missing_vars.any?
      puts "Missing required environment variables: #{missing_vars.join(', ')}".red
      puts "Please copy .env.example to .env and fill in the values.".yellow
      exit 1
    end
  end

  config.before(:each) do |example|
    # Reset any shared state
    @current_token = nil
    @created_resources = []
  end

  config.after(:each) do |example|
    # Clean up created resources
    cleanup_test_resources if defined?(@created_resources) && @created_resources.any?
  end
end

# Global helper method to cleanup test resources
def cleanup_test_resources
  @created_resources.each do |resource|
    case resource[:type]
    when :activity
      delete_activity(resource[:id]) rescue nil
    when :section
      # Sections typically can't be deleted via API
    end
  end
  @created_resources.clear
end