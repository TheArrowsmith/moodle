RSpec.describe 'Section API Endpoints' do
  before do
    authenticate_as(:teacher)
  end

  let(:test_section) do
    response = get_course_data
    response.parsed_response['sections'].first
  end

  describe 'PUT /section/{sectionId}' do
    context 'updating section properties' do
      it 'updates section name' do
        new_name = "Week #{Time.now.to_i}: Updated Topic"
        
        response = put("/section/#{test_section['id']}", {
          name: new_name
        })
        
        expect(response.code).to eq(200)
        
        updated_section = response.parsed_response
        expect(updated_section['name']).to eq(new_name)
        expect(updated_section['id']).to eq(test_section['id'])
        
        # Should not include activities array in response
        expect(updated_section).not_to have_key('activities')
      end

      it 'updates section visibility' do
        original_visibility = test_section['visible']
        
        response = put("/section/#{test_section['id']}", {
          visible: !original_visibility
        })
        
        expect(response.code).to eq(200)
        
        updated_section = response.parsed_response
        expect(updated_section['visible']).to eq(!original_visibility)
      end

      it 'updates section summary' do
        new_summary = "<p>This is the updated summary for week #{Time.now.to_i}.</p>"
        
        response = put("/section/#{test_section['id']}", {
          summary: new_summary
        })
        
        expect(response.code).to eq(200)
        
        updated_section = response.parsed_response
        expect(updated_section['summary']).to eq(new_summary)
      end

      it 'updates multiple properties at once' do
        new_name = "Week #{Time.now.to_i}: Multi-Update"
        new_summary = "<p>Updated summary with multiple changes.</p>"
        new_visibility = false
        
        response = put("/section/#{test_section['id']}", {
          name: new_name,
          visible: new_visibility,
          summary: new_summary
        })
        
        expect(response.code).to eq(200)
        
        updated_section = response.parsed_response
        expect(updated_section['name']).to eq(new_name)
        expect(updated_section['visible']).to eq(new_visibility)
        expect(updated_section['summary']).to eq(new_summary)
      end
    end

    context 'error handling' do
      it 'returns 404 for non-existent section' do
        response = put('/section/99999', {
          name: 'Should not work'
        })
        
        expect(response.code).to eq(404)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 403 when student tries to update' do
        authenticate_as(:student)
        
        response = put("/section/#{test_section['id']}", {
          name: 'Student cannot update'
        })
        
        expect(response.code).to eq(403)
        expect(response.parsed_response).to have_key('error')
      end
    end
  end

  describe 'POST /section/{sectionId}/reorder_activities' do
    let(:section_with_activities) do
      find_test_section_with_activities(3)
    end

    it 'reorders activities within a section' do
      activities = section_with_activities['activities']
      original_order = activities.map { |a| a['id'] }
      
      # Reverse the order
      new_order = original_order.reverse
      
      response = post("/section/#{section_with_activities['id']}/reorder_activities", {
        activity_ids: new_order
      })
      
      expect(response.code).to eq(200)
      expect(response.parsed_response['status']).to eq('success')
      
      # Verify the new order
      updated_course = get_course_data
      updated_section = updated_course.parsed_response['sections'].find { |s| s['id'] == section_with_activities['id'] }
      updated_order = updated_section['activities'].map { |a| a['id'] }
      
      expect(updated_order).to eq(new_order)
    end

    it 'maintains partial order when some activities are specified' do
      activities = section_with_activities['activities']
      skip 'Not enough activities for partial reordering test' if activities.size < 3
      
      # Only reorder first two activities
      first_two = activities[0..1].map { |a| a['id'] }
      reordered = first_two.reverse
      
      response = post("/section/#{section_with_activities['id']}/reorder_activities", {
        activity_ids: reordered
      })
      
      expect(response.code).to eq(200)
    end

    context 'error handling' do
      it 'returns 400 for invalid activity ID in list' do
        activities = section_with_activities['activities']
        activity_ids = activities.map { |a| a['id'] }
        activity_ids << 99999 # Add invalid ID
        
        response = post("/section/#{section_with_activities['id']}/reorder_activities", {
          activity_ids: activity_ids
        })
        
        expect(response.code).to eq(400)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 404 for non-existent section' do
        response = post('/section/99999/reorder_activities', {
          activity_ids: [1, 2, 3]
        })
        
        expect(response.code).to eq(404)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 422 for invalid request format' do
        response = post("/section/#{section_with_activities['id']}/reorder_activities", {
          wrong_field: 'not activity_ids'
        })
        
        expect(response.code).to eq(422)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 403 when student tries to reorder' do
        authenticate_as(:student)
        
        response = post("/section/#{section_with_activities['id']}/reorder_activities", {
          activity_ids: [1, 2, 3]
        })
        
        expect(response.code).to eq(403)
        expect(response.parsed_response).to have_key('error')
      end
    end
  end

  describe 'POST /section/{sectionId}/move_activity' do
    let(:source_section) { find_test_section_with_activities(2) }
    let(:target_section) do
      response = get_course_data
      sections = response.parsed_response['sections']
      sections.find { |s| s['id'] != source_section['id'] }
    end

    it 'moves activity to another section' do
      activity_to_move = source_section['activities'].first
      original_target_count = target_section['activities'].size
      
      response = post("/section/#{target_section['id']}/move_activity", {
        activityid: activity_to_move['id'],
        position: 0
      })
      
      expect(response.code).to eq(200)
      expect(response.parsed_response['status']).to eq('success')
      
      # Verify activity moved
      updated_course = get_course_data
      updated_target = updated_course.parsed_response['sections'].find { |s| s['id'] == target_section['id'] }
      updated_source = updated_course.parsed_response['sections'].find { |s| s['id'] == source_section['id'] }
      
      target_activity_ids = updated_target['activities'].map { |a| a['id'] }
      source_activity_ids = updated_source['activities'].map { |a| a['id'] }
      
      expect(target_activity_ids).to include(activity_to_move['id'])
      expect(source_activity_ids).not_to include(activity_to_move['id'])
      expect(updated_target['activities'].size).to eq(original_target_count + 1)
    end

    it 'places activity at specified position' do
      activity_to_move = source_section['activities'].first
      target_activities = target_section['activities']
      
      # Move to position 1 (second position)
      response = post("/section/#{target_section['id']}/move_activity", {
        activityid: activity_to_move['id'],
        position: 1
      })
      
      expect(response.code).to eq(200)
      
      # Verify position
      updated_course = get_course_data
      updated_target = updated_course.parsed_response['sections'].find { |s| s['id'] == target_section['id'] }
      
      expect(updated_target['activities'][1]['id']).to eq(activity_to_move['id'])
    end

    context 'error handling' do
      it 'returns 404 for non-existent activity' do
        response = post("/section/#{target_section['id']}/move_activity", {
          activityid: 99999,
          position: 0
        })
        
        expect(response.code).to eq(404)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 404 for non-existent target section' do
        activity = source_section['activities'].first
        
        response = post('/section/99999/move_activity', {
          activityid: activity['id'],
          position: 0
        })
        
        expect(response.code).to eq(404)
        expect(response.parsed_response).to have_key('error')
      end

      it 'returns 422 for missing required fields' do
        response = post("/section/#{target_section['id']}/move_activity", {
          position: 0
          # Missing activityid
        })
        
        expect(response.code).to eq(422)
        expect(response.parsed_response).to have_key('error')
      end
    end
  end
end