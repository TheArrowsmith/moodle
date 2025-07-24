RSpec.describe 'Activity API Endpoints' do
  before do
    authenticate_as(:teacher)
  end

  describe 'PUT /activity/{activityId}' do
    let(:test_activity) do
      # Find or create a test activity
      response = get_course_data
      sections = response.parsed_response['sections']
      activity = sections.flat_map { |s| s['activities'] }.first
      
      unless activity
        # Create one if none exist
        created = create_activity(sample_activity_data)
        wait_for_propagation
        created
      else
        activity
      end
    end

    context 'updating activity properties' do
      it 'updates activity name' do
        new_name = "Updated Activity #{Time.now.to_i}"
        
        response = put("/activity/#{test_activity['id']}", {
          name: new_name
        })
        
        expect(response.code).to eq(200)
        
        updated_activity = response.parsed_response
        expect(updated_activity['name']).to eq(new_name)
        expect(updated_activity['id']).to eq(test_activity['id'])
      end

      it 'updates activity visibility' do
        original_visibility = test_activity['visible']
        
        response = put("/activity/#{test_activity['id']}", {
          visible: !original_visibility
        })
        
        expect(response.code).to eq(200)
        
        updated_activity = response.parsed_response
        expect(updated_activity['visible']).to eq(!original_visibility)
      end

      it 'updates multiple properties at once' do
        new_name = "Multi-Update Activity #{Time.now.to_i}"
        new_visibility = false
        
        response = put("/activity/#{test_activity['id']}", {
          name: new_name,
          visible: new_visibility
        })
        
        expect(response.code).to eq(200)
        
        updated_activity = response.parsed_response
        expect(updated_activity['name']).to eq(new_name)
        expect(updated_activity['visible']).to eq(new_visibility)
      end

      it 'performs partial updates without affecting other properties' do
        # Update only name
        new_name = "Partial Update #{Time.now.to_i}"
        original_visibility = test_activity['visible']
        
        response = put("/activity/#{test_activity['id']}", {
          name: new_name
        })
        
        expect(response.code).to eq(200)
        
        updated_activity = response.parsed_response
        expect(updated_activity['name']).to eq(new_name)
        expect(updated_activity['visible']).to eq(original_visibility)
      end
    end

    context 'error handling' do
      it 'returns 404 for non-existent activity' do
        response = put('/activity/99999', {
          name: 'Should not work'
        })
        
        expect(response.code).to eq(404)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 422 for invalid request body' do
        response = put("/activity/#{test_activity['id']}", 'invalid json')
        
        expect(response.code).to eq(422)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 403 when user lacks permissions' do
        authenticate_as(:student)
        
        response = put("/activity/#{test_activity['id']}", {
          name: 'Student cannot update'
        })
        
        expect(response.code).to eq(403)
        expect(response.parsed_response).to have_key('error')
      end
    end
  end

  describe 'DELETE /activity/{activityId}' do
    let(:activity_to_delete) do
      create_activity(sample_activity_data(name: "To Be Deleted #{Time.now.to_i}"))
    end

    it 'successfully deletes an activity' do
      activity_id = activity_to_delete['id']
      
      response = delete("/activity/#{activity_id}")
      
      expect(response.code).to eq(204)
      expect(response.body).to be_empty
      
      # Verify activity is gone
      course_response = get_course_data
      all_activities = course_response.parsed_response['sections'].flat_map { |s| s['activities'] }
      activity_ids = all_activities.map { |a| a['id'] }
      
      expect(activity_ids).not_to include(activity_id)
    end

    it 'returns 404 when deleting already deleted activity' do
      activity_id = activity_to_delete['id']
      
      # Delete once
      delete("/activity/#{activity_id}")
      
      # Try to delete again
      response = delete("/activity/#{activity_id}")
      
      expect(response.code).to eq(404)
      expect(response.parsed_response).to have_key('error')
    end

    it 'returns 403 when student tries to delete' do
      activity_id = activity_to_delete['id']
      authenticate_as(:student)
      
      response = delete("/activity/#{activity_id}")
      
      expect(response.code).to eq(403)
      expect(response.parsed_response).to have_key('error')
      
      # Cleanup
      authenticate_as(:teacher)
      delete("/activity/#{activity_id}")
    end
  end

  describe 'POST /activity' do
    context 'creating new activities' do
      it 'creates an assignment activity' do
        activity_data = sample_activity_data(
          modname: 'assign',
          name: "API Assignment #{Time.now.to_i}"
        )
        
        response = post('/activity', activity_data)
        
        expect(response.code).to eq(200)
        
        created_activity = response.parsed_response
        expect(created_activity).to have_key('id')
        expect(created_activity['name']).to eq(activity_data[:name])
        expect(created_activity['modname']).to eq('assign')
        expect(created_activity['visible']).to eq(true)
        
        @created_resources << { type: :activity, id: created_activity['id'] }
      end

      it 'creates different activity types' do
        %w[quiz forum resource].each do |modname|
          activity_data = sample_activity_data(
            modname: modname,
            name: "API #{modname.capitalize} #{Time.now.to_i}"
          )
          
          response = post('/activity', activity_data)
          
          expect(response.code).to eq(200)
          
          created_activity = response.parsed_response
          expect(created_activity['modname']).to eq(modname)
          
          @created_resources << { type: :activity, id: created_activity['id'] }
        end
      end

      it 'creates hidden activities' do
        activity_data = sample_activity_data(
          name: "Hidden Activity #{Time.now.to_i}",
          visible: false
        )
        
        response = post('/activity', activity_data)
        
        expect(response.code).to eq(200)
        
        created_activity = response.parsed_response
        expect(created_activity['visible']).to eq(false)
        
        @created_resources << { type: :activity, id: created_activity['id'] }
      end
    end

    context 'error handling' do
      it 'returns 422 for missing required fields' do
        response = post('/activity', {
          name: 'Missing courseid and sectionid'
        })
        
        expect(response.code).to eq(422)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 404 for non-existent section' do
        activity_data = sample_activity_data(
          sectionid: 99999
        )
        
        response = post('/activity', activity_data)
        
        expect(response.code).to eq(404)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 403 when student tries to create' do
        authenticate_as(:student)
        
        response = post('/activity', sample_activity_data)
        
        expect(response.code).to eq(403)
        expect(response.parsed_response).to have_key('error')
      end
    end
  end
end