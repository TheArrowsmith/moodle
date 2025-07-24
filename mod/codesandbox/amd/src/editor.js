// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Code editor JavaScript for mod_codesandbox
 *
 * @package    mod_codesandbox
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification', 'core/str'], function($, Ajax, Notification, Str) {
    'use strict';

    return {
        init: function(cmid, starterCode, apiUrl, isGradable, initialLanguage) {
            var editor = null;
            var currentLanguage = initialLanguage || 'python';
            
            // Language to CodeMirror mode mapping
            var languageModes = {
                'python': 'python',
                'ruby': 'ruby',
                'elixir': 'erlang'  // Elixir uses Erlang mode
            };
            
            // Language-specific comment syntax
            var languageComments = {
                'python': '# ',
                'ruby': '# ',
                'elixir': '# '
            };
            
            // Default starter codes for each language
            var defaultStarters = {
                'python': '# Welcome to Python Code Sandbox!\n# Write your code below:\n\n',
                'ruby': '# Welcome to Ruby Code Sandbox!\n# Write your code below:\n\n',
                'elixir': '# Welcome to Elixir Code Sandbox!\n# Write your code below:\n\n'
            };
            
            // Initialize CodeMirror after DOM is ready
            $(document).ready(function() {
                var textarea = document.getElementById('code-editor');
                if (textarea) {
                    editor = CodeMirror.fromTextArea(textarea, {
                        mode: languageModes[currentLanguage],
                        theme: 'monokai',
                        lineNumbers: true,
                        indentUnit: 4,
                        lineWrapping: true,
                        autofocus: true
                    });
                    
                    // Set starter code if no previous submission
                    var currentValue = editor.getValue().trim();
                    if (!currentValue && starterCode) {
                        editor.setValue(starterCode);
                    } else if (!currentValue) {
                        // Use language-specific default starter
                        editor.setValue(defaultStarters[currentLanguage]);
                    }
                }
                
                // Handle language change
                $('#language-select').on('change', function() {
                    var newLanguage = $(this).val();
                    if (newLanguage !== currentLanguage) {
                        currentLanguage = newLanguage;
                        
                        // Update CodeMirror mode
                        editor.setOption('mode', languageModes[currentLanguage]);
                        
                        // If editor is empty or has default starter, update with new language starter
                        var currentCode = editor.getValue().trim();
                        var isDefaultStarter = false;
                        
                        // Check if current code is a default starter
                        for (var lang in defaultStarters) {
                            if (currentCode === defaultStarters[lang].trim()) {
                                isDefaultStarter = true;
                                break;
                            }
                        }
                        
                        if (!currentCode || isDefaultStarter) {
                            editor.setValue(defaultStarters[currentLanguage]);
                        }
                    }
                });
            });
            
            // Handle run button
            $('#run-code').on('click', function() {
                if (!editor) return;
                
                var code = editor.getValue();
                $('#loading-spinner').show();
                $('#run-code').prop('disabled', true);
                $('.output-section').text('');
                
                // Call external API
                $.ajax({
                    url: apiUrl + '/execute',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        code: code,
                        language: currentLanguage
                    }),
                    success: function(response) {
                        $('#stdout').text(response.stdout || '(no output)');
                        $('#stderr').text(response.stderr || '(no errors)');
                        
                        // Switch to appropriate tab
                        if (response.stderr) {
                            $('a[href="#stderr-tab"]').tab('show');
                        } else {
                            $('a[href="#stdout-tab"]').tab('show');
                        }
                    },
                    error: function(xhr, status, error) {
                        var message = 'Error: ';
                        if (xhr.responseJSON && xhr.responseJSON.detail) {
                            message += xhr.responseJSON.detail;
                        } else {
                            message += error || status || 'Could not connect to execution service';
                        }
                        Notification.alert('Execution Error', message);
                    },
                    complete: function() {
                        $('#loading-spinner').hide();
                        $('#run-code').prop('disabled', false);
                    }
                });
            });
            
            // Handle submit button (for grading)
            $('#submit-code').on('click', function() {
                if (!editor) return;
                
                var code = editor.getValue();
                $('#loading-spinner').show();
                $('#submit-code').prop('disabled', true);
                
                // Submit via AJAX to Moodle
                var promises = Ajax.call([{
                    methodname: 'mod_codesandbox_submit_code',
                    args: {
                        cmid: cmid,
                        code: code,
                        language: currentLanguage
                    }
                }]);
                
                promises[0].done(function(response) {
                    if (response.success) {
                        Notification.addNotification({
                            message: 'Code submitted successfully!',
                            type: 'success'
                        });
                        
                        // Display test results if available
                        if (response.results) {
                            displayTestResults(response.results);
                            $('a[href="#results-tab"]').tab('show');
                        }
                    } else {
                        Notification.alert('Submission Error', response.error || 'Failed to submit code');
                    }
                }).fail(function(ex) {
                    Notification.exception(ex);
                }).always(function() {
                    $('#loading-spinner').hide();
                    $('#submit-code').prop('disabled', false);
                });
            });
            
            // Handle clear button
            $('#clear-output').on('click', function() {
                $('.output-section').text('');
                $('#test-results').empty();
            });
            
            // Function to display test results
            function displayTestResults(results) {
                var $container = $('#test-results');
                $container.empty();
                
                if (results.score !== undefined) {
                    var percentage = (results.score * 100).toFixed(1);
                    var scoreClass = results.score >= 0.8 ? 'text-success' : 
                                   results.score >= 0.6 ? 'text-warning' : 'text-danger';
                    
                    $container.append(
                        '<div class="mb-3">' +
                        '<h4>Score: <span class="' + scoreClass + '">' + percentage + '%</span></h4>' +
                        '<p>Tests passed: ' + results.passed_tests + ' / ' + results.total_tests + '</p>' +
                        '</div>'
                    );
                }
                
                if (results.results && results.results.length > 0) {
                    var $list = $('<div class="test-result-list"></div>');
                    
                    results.results.forEach(function(test) {
                        var iconClass = test.passed ? 'fa-check text-success' : 'fa-times text-danger';
                        var $testItem = $(
                            '<div class="test-result-item mb-2">' +
                            '<i class="fa ' + iconClass + '"></i> ' +
                            '<strong>' + test.test_name + '</strong>' +
                            '</div>'
                        );
                        
                        if (!test.passed && test.message) {
                            $testItem.append(
                                '<pre class="test-error-message">' + 
                                escapeHtml(test.message) + 
                                '</pre>'
                            );
                        }
                        
                        $list.append($testItem);
                    });
                    
                    $container.append($list);
                }
            }
            
            // Helper function to escape HTML
            function escapeHtml(text) {
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return text.replace(/[&<>"']/g, function(m) { return map[m]; });
            }
        }
    };
});