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
 * Dashboard JavaScript for report_codeprogress
 *
 * @package    report_codeprogress
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
    'use strict';

    return {
        init: function(courseid) {
            this.courseid = courseid;
            this.loadProgressData();
        },
        
        loadProgressData: function() {
            var self = this;
            
            // Call the custom API
            var promises = Ajax.call([{
                methodname: 'local_customapi_get_sandbox_grades',
                args: {courseid: this.courseid}
            }]);
            
            promises[0].done(function(response) {
                self.processData(response);
                self.renderTable();
                self.renderCharts();
                self.renderSummaryStats();
                $('#loading-message').hide();
                $('#progress-content').show();
            }).fail(function(ex) {
                Notification.exception(ex);
                $('#loading-message').hide();
                $('#error-message').show();
                $('#error-text').text(ex.message || 'Failed to load data');
            });
        },
        
        processData: function(data) {
            // Transform flat data into structured format
            this.students = {};
            this.activities = {};
            this.rawData = data;
            
            data.forEach(function(record) {
                if (!this.students[record.userid]) {
                    this.students[record.userid] = {
                        id: record.userid,
                        name: record.fullname,
                        username: record.username,
                        grades: {}
                    };
                }
                
                if (!this.activities[record.activityid]) {
                    this.activities[record.activityid] = {
                        id: record.activityid,
                        name: record.activityname,
                        cmid: record.cmid
                    };
                }
                
                this.students[record.userid].grades[record.activityid] = {
                    grade: record.grade,
                    status: record.submissionstatus,
                    timesubmitted: record.timesubmitted
                };
            }, this);
        },
        
        renderTable: function() {
            var $header = $('#table-header');
            var $body = $('#table-body');
            var $footer = $('#table-footer');
            
            // Add activity columns to header
            Object.values(this.activities).forEach(function(activity) {
                $header.append('<th class="text-center">' + this.escapeHtml(activity.name) + '</th>');
            }, this);
            
            // Add student rows
            Object.values(this.students).forEach(function(student) {
                var $row = $('<tr>');
                $row.append('<td class="sticky-col">' + this.escapeHtml(student.name) + '</td>');
                
                Object.values(this.activities).forEach(function(activity) {
                    var grade = student.grades[activity.id];
                    var $cell = $('<td class="text-center">');
                    
                    if (grade && grade.status === 'submitted') {
                        var gradeValue = grade.grade !== null ? grade.grade : 0;
                        var cellClass = this.getGradeClass(gradeValue);
                        $cell.addClass(cellClass);
                        $cell.text(gradeValue + '%');
                    } else {
                        $cell.addClass('text-muted');
                        $cell.html('<i class="fa fa-minus"></i>');
                    }
                    
                    $row.append($cell);
                }, this);
                
                $body.append($row);
            }, this);
            
            // Add summary row
            var $summaryRow = $('<tr class="font-weight-bold">');
            $summaryRow.append('<td class="sticky-col">Average</td>');
            
            Object.values(this.activities).forEach(function(activity) {
                var scores = [];
                Object.values(this.students).forEach(function(student) {
                    var grade = student.grades[activity.id];
                    if (grade && grade.status === 'submitted' && grade.grade !== null) {
                        scores.push(parseFloat(grade.grade));
                    }
                });
                
                var avg = scores.length ? (scores.reduce((a, b) => a + b) / scores.length).toFixed(1) : '-';
                var $cell = $('<td class="text-center">');
                if (avg !== '-') {
                    $cell.addClass(this.getGradeClass(parseFloat(avg)));
                    $cell.text(avg + '%');
                } else {
                    $cell.text(avg);
                }
                $summaryRow.append($cell);
            }, this);
            
            $footer.append($summaryRow);
        },
        
        renderCharts: function() {
            // Average scores chart
            this.renderAverageScoresChart();
            
            // Submission status chart
            this.renderSubmissionStatusChart();
        },
        
        renderAverageScoresChart: function() {
            var ctx = document.getElementById('average-scores-chart').getContext('2d');
            
            var labels = [];
            var averages = [];
            
            Object.values(this.activities).forEach(function(activity) {
                labels.push(activity.name);
                
                var scores = [];
                Object.values(this.students).forEach(function(student) {
                    var grade = student.grades[activity.id];
                    if (grade && grade.status === 'submitted' && grade.grade !== null) {
                        scores.push(parseFloat(grade.grade));
                    }
                });
                
                var avg = scores.length ? scores.reduce((a, b) => a + b) / scores.length : 0;
                averages.push(avg.toFixed(1));
            }, this);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Average Score (%)',
                        data: averages,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + '%';
                                }
                            }
                        }
                    }
                }
            });
        },
        
        renderSubmissionStatusChart: function() {
            var ctx = document.getElementById('submission-status-chart').getContext('2d');
            
            var submitted = 0;
            var notSubmitted = 0;
            
            Object.values(this.students).forEach(function(student) {
                Object.values(this.activities).forEach(function(activity) {
                    var grade = student.grades[activity.id];
                    if (grade && grade.status === 'submitted') {
                        submitted++;
                    } else {
                        notSubmitted++;
                    }
                }, this);
            }, this);
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Submitted', 'Not Submitted'],
                    datasets: [{
                        data: [submitted, notSubmitted],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(255, 99, 132, 0.6)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    var total = context.dataset.data.reduce((a, b) => a + b);
                                    var percentage = ((context.parsed / total) * 100).toFixed(1);
                                    return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        },
        
        renderSummaryStats: function() {
            // Total students
            $('#total-students').text(Object.keys(this.students).length);
            
            // Total assignments
            $('#total-assignments').text(Object.keys(this.activities).length);
            
            // Overall average
            var allScores = [];
            Object.values(this.students).forEach(function(student) {
                Object.values(this.activities).forEach(function(activity) {
                    var grade = student.grades[activity.id];
                    if (grade && grade.status === 'submitted' && grade.grade !== null) {
                        allScores.push(parseFloat(grade.grade));
                    }
                }, this);
            }, this);
            
            var overallAvg = allScores.length ? 
                (allScores.reduce((a, b) => a + b) / allScores.length).toFixed(1) : 0;
            $('#overall-average').text(overallAvg + '%');
            
            // Completion rate
            var totalPossible = Object.keys(this.students).length * Object.keys(this.activities).length;
            var totalSubmitted = allScores.length;
            var completionRate = totalPossible > 0 ? 
                ((totalSubmitted / totalPossible) * 100).toFixed(1) : 0;
            $('#completion-rate').text(completionRate + '%');
        },
        
        getGradeClass: function(grade) {
            if (grade >= 80) return 'text-success';
            if (grade >= 60) return 'text-warning';
            return 'text-danger';
        },
        
        escapeHtml: function(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    };
});